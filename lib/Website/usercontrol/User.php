<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol;

use Simbiat\Website\Abstracts\Entity;
use Simbiat\ArrayHelpers;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Errors;
use Simbiat\http20\IRI;
use Simbiat\Website\Images;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Security;
use Simbiat\Website\Talks\Search\Posts;
use Simbiat\Website\Talks\Search\Threads;

use function in_array, is_string, is_array;

/**
 * Main user class
 */
class User extends Entity
{
    #Maximum number of unused avatars per user
    public const int avatarLimit = 10;
    #Entity's properties
    public string $username;
    #Real name
    public array $name = [
        'firstname' => null,
        'lastname' => null,
        'middlename' => null,
        'fathername' => null,
        'prefix' => null,
        'suffix' => null,
    ];
    #Phone
    public ?string $phone = null;
    #Dates
    public array $dates = [
        'registered' => null,
        'updated' => null,
        'birthday' => null,
    ];
    #Parent details
    public array $parent = [
        'id' => null,
        'name' => null,
    ];
    #Personal sections
    public array $sections = [
        'blog' => null,
        'changelog' => null,
        'knowledgebase' => null,
    ];
    #FF Token
    public ?string $ff_token = null;
    #Sex
    public ?int $sex = null;
    #About
    public ?string $about = null;
    #Timezone
    public ?string $timezone = null;
    #Country
    public ?string $country = null;
    #City
    public ?string $city = null;
    #Website
    public ?string $website = null;
    #Groups
    public array $groups = [];
    #Permissions
    public array $permissions = ['viewPosts', 'viewBic', 'viewFF'];
    #Whether account is activated
    public bool $activated = false;
    #Emails
    public array $emails = [];
    #Avatars
    public array $avatars = [];
    #Current avatar
    public ?string $currentAvatar = null;
    
    /**
     * Function to get initial data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        
        $dbData = Config::$dbController->selectRow('SELECT `username`, `phone`, `ff_token`, `registered`, `updated`, `parentid`, (IF(`parentid` IS NULL, NULL, (SELECT `username` FROM `uc__users` WHERE `userid`=:userid))) as `parentname`, `birthday`, `firstname`, `lastname`, `middlename`, `fathername`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website`, `blog`, `changelog`, `knowledgebase` FROM `uc__users` LEFT JOIN `uc__user_to_section` ON `uc__users`.`userid`=`uc__user_to_section`.`userid` WHERE `uc__users`.`userid`=:userid', ['userid' => [$this->id, 'int']]);
        if (empty($dbData)) {
            return [];
        }
        #Get user's groups
        $dbData['groups'] = Config::$dbController->selectColumn('SELECT `groupid` FROM `uc__user_to_group` WHERE `userid`=:userid', ['userid' => [$this->id, 'int']]);
        #Get permissions
        $dbData['permissions'] = $this->getPermissions();
        if (in_array($this->id, [Config::userIDs['Unknown user'], Config::userIDs['System user'], Config::userIDs['Deleted user']], true)) {
            #System users need to be treated as not activated
            $dbData['activated'] = false;
        } else {
            $dbData['activated'] = !in_array(Config::groupsIDs['Unverified'], $dbData['groups'], true);
        }
        $dbData['currentAvatar'] = $this->getAvatar();
        return $dbData;
    }
    
    /**
     * Function process database data
     * @param array $fromDB
     *
     * @return void
     */
    protected function process(array $fromDB): void
    {
        #Populate names
        $this->name['firstname'] = $fromDB['firstname'];
        $this->name['lastname'] = $fromDB['lastname'];
        $this->name['middlename'] = $fromDB['middlename'];
        $this->name['fathername'] = $fromDB['fathername'];
        $this->name['prefix'] = $fromDB['prefix'];
        $this->name['suffix'] = $fromDB['suffix'];
        #Populate dates
        $this->dates['registered'] = $fromDB['registered'];
        $this->dates['updated'] = $fromDB['updated'];
        $this->dates['birthday'] = $fromDB['birthday'];
        #Populate parent details
        $this->parent['id'] = $fromDB['parentid'];
        $this->parent['name'] = $fromDB['parentname'];
        #Pupulate personal sections
        $this->sections = [
            'blog' => empty($fromDB['blog']) ? null : $fromDB['blog'],
            'changelog' => empty($fromDB['changelog']) ? null : $fromDB['changelog'],
            'knowledgebase' => empty($fromDB['knowledgebase']) ? null : $fromDB['knowledgebase'],
        ];
        #Cleanup the array
        unset($fromDB['parentid'], $fromDB['parentname'], $fromDB['firstname'], $fromDB['lastname'], $fromDB['middlename'], $fromDB['fathername'], $fromDB['prefix'],
            $fromDB['suffix'], $fromDB['registered'], $fromDB['updated'], $fromDB['birthday'], $fromDB['blog'], $fromDB['changelog'], $fromDB['knowledgebase']);
        #Populate the rest properties
        $this->arrayToProperties($fromDB);
    }
    
    /**
     * Get user permissions
     * @return array
     */
    private function getPermissions(): array
    {
        try {
            return Config::$dbController->selectColumn('
                SELECT * FROM (
                    SELECT `uc__group_to_permission`.`permission` FROM `uc__group_to_permission` LEFT JOIN `uc__groups` ON `uc__group_to_permission`.`groupid`=`uc__groups`.`groupid` LEFT JOIN `uc__permissions` ON `uc__group_to_permission`.`permission`=`uc__permissions`.`permission` LEFT JOIN `uc__user_to_group` ON `uc__group_to_permission`.`groupid`=`uc__user_to_group`.`groupid` WHERE `userid`=:userid
                    UNION ALL
                    SELECT `permission` FROM `uc__user_to_permission` WHERE `userid`=:userid
                ) as `temp` GROUP BY `permission`;
            ', ['userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Get user email addresses
     * @return array
     */
    public function getEmails(): array
    {
        try {
            return Config::$dbController->selectAll('SELECT `email`, `subscribed`, `activation` FROM `uc__emails` WHERE `userid`=:userid ORDER BY `email`;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Get current avatar
     * @return string
     */
    public function getAvatar(): string
    {
        try {
            $avatar = Config::$dbController->selectValue('SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) AS `url` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid AND `current`=1 LIMIT 1', ['userid' => [$this->id, 'int']]);
            if (empty($avatar)) {
                $avatar = '/assets/images/avatar.svg';
            }
        } catch (\Throwable) {
            $avatar = '/assets/images/avatar.svg';
        }
        return $avatar;
    }
    
    /**
     * Get all avatars
     * @return array
     */
    public function getAvatars(): array
    {
        try {
            return Config::$dbController->selectAll('SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) as `url`, `uc__avatars`.`fileid`, `current` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid ORDER BY `current` DESC;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Add avatar
     * @param bool     $setActive Make avatar active right away
     * @param string   $link      Link to avatar
     * @param int|null $character FFXIV character, if avatar is from one
     *
     * @return array
     */
    public function addAvatar(bool $setActive = false, string $link = '', ?int $character = null): array
    {
        try {
            #Get current avatars
            $avatars = $this->getAvatars();
            #Count values in `current` column
            $counts = array_count_values(array_column($avatars, 'current'));
            #If count of 0 values does not exist, then we set it to 0 properly
            if (empty($counts[0])) {
                $counts[0] = 0;
            }
            #Check if we are not trying to add an excessive avatar (compare number of non-current avatars to the limit)
            #If we are setting one for a character - ignore this limitation, though, because it is possible that this character is being used as current avatar, which we will need to update
            if ($character === null && $counts[0] === self::avatarLimit) {
                return ['http_error' => 413, 'reason' => 'Maximum of '.self::avatarLimit.' unused avatars reached'];
            }
            $upload = new Curl()->upload($link, true);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            #Log the change
            Security::log('Avatar', 'Added avatar', $upload['hash']);
            #Add to DB
            Config::$dbController->query(
                'INSERT IGNORE INTO `uc__avatars` (`userid`, `fileid`, `characterid`, `current`) VALUES (:userid, :fileid, :character, 0);',
                [
                    ':userid' => [$this->id, 'int'],
                    ':fileid' => $upload['hash'],
                    ':character' => [
                        ($character),
                        ($character === null ? 'null' : 'int')
                    ]
                ]
            );
            if ($setActive) {
                return $this->setAvatar($upload['hash']);
            }
            if ($character !== null && Config::$dbController->check('SELECT `fileid` FROM `uc__avatars` WHERE `userid`=:userid AND `current`=1 AND `characterid`=:character;', [':userid' => [$this->id, 'int'], ':character' => [$character, 'int']])) {
                #Set the new one as active
                return $this->setAvatar($upload['hash']);
            }
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to add avatar to library'];
        }
        if (empty($upload['location'])) {
            return ['http_error' => 500, 'reason' => 'No file path determined'];
        }
        return ['location' => $upload['location'], 'response' => true];
    }
    
    /**
     * Remove avatar
     * @return array
     */
    public function delAvatar(): array
    {
        $fileid = $_POST['avatar'] ?? '';
        #Log the change
        Security::log('Avatar', 'Deleted avatar', $fileid);
        #Delete the avatar (only allow deletion of those, that are not current)
        Config::$dbController->query('DELETE FROM `uc__avatars` WHERE `userid`=:userid AND `fileid`=:fileid AND `current`=0;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }
    
    /**
     * Set current avatar
     * @param string $fileid
     *
     * @return array
     */
    public function setAvatar(string $fileid = ''): array
    {
        if (!empty($_POST['avatar']) && is_string($_POST['avatar'])) {
            $fileid = $_POST['avatar'];
        }
        #Log the change
        Security::log('Avatar', 'Changed active avatar', $fileid);
        Config::$dbController->query([
            #Set the chosen avatar as current
            ['UPDATE `uc__avatars` SET `current`=1 WHERE `userid`=:userid AND `fileid`=:fileid;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]],
            #Set the rest as non-current
            ['UPDATE `uc__avatars` SET `current`=0 WHERE `userid`=:userid AND `fileid`<>:fileid;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]],
        ]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }
    
    /**
     * Get owned FFXIV entities
     * @return array
     */
    public function getFF(): array
    {
        $outputArray = [];
        #Get token
        $outputArray['token'] = Config::$dbController->selectValue('SELECT `ff_token` FROM `uc__users` WHERE `userid`=:userid;', [':userid' => [$this->id, 'int']]);
        #Get linked characters
        $outputArray['characters'] = Config::$dbController->selectAll('SELECT \'character\' as `type`, `characterid` as `id`, `name`, `avatar` as `icon` FROM `ffxiv__character` WHERE `userid`=:userid ORDER BY `name`;', [':userid' => [$this->id, 'int']]);
        #Get linked groups
        if (!empty($outputArray['characters'])) {
            foreach ($outputArray['characters'] as $character) {
                $outputArray['groups'][$character['id']] = \Simbiat\Website\fftracker\Entity::cleanCrestResults(Config::$dbController->selectAll(
                    '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `grandcompanyid` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__freecompany_character`.`rankid`=`ffxiv__freecompany_rank`.`rankid` WHERE `characterid`=:id AND `ffxiv__freecompany_character`.`current`=1 AND `ffxiv__freecompany_character`.`rankid`=0)
                UNION ALL
                (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, null as `crest_part_1`, null as `crest_part_2`, null as `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rankid`=`ffxiv__linkshell_rank`.`lsrankid` WHERE `characterid`=:id AND `ffxiv__linkshell_character`.`current`=1 AND `ffxiv__linkshell_character`.`rankid`=1)
                UNION ALL
                (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rankid`=`ffxiv__pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `ffxiv__pvpteam_character`.`current`=1 AND `ffxiv__pvpteam_character`.`rankid`=1)
                ORDER BY `name` ASC;',
                    [':id' => [$character['id'], 'int']]
                ));
            }
        }
        return $outputArray;
    }
    
    /**
     * Change username
     * @param string $newName
     *
     * @return array|true[]
     */
    public function changeUsername(string $newName): array
    {
        $sanitizedName = Sanitization::removeNonPrintable($newName, true);
        if (!is_string($sanitizedName)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        $newName = $sanitizedName;
        #Check if new name is valid
        if (empty($newName) || $this->bannedName($newName) || $this->usedName($newName)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        #Check if we have current username and get it if we do not
        if (empty($this->username)) {
            $this->get();
        }
        if ($this->username === $newName) {
            return ['response' => true];
        }
        try {
            $result = Config::$dbController->query('UPDATE `uc__users` SET `username`=:username WHERE `userid`=:userid;', [
                ':userid' => [$this->id, 'int'],
                ':username' => $newName,
            ]);
            if ($result) {
                $_SESSION['username'] = $newName;
            }
            if (session_status() === PHP_SESSION_ACTIVE) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @session_regenerate_id(true);
            }
            #Log the change
            Security::log('User details change', 'Changed name', ['name' => ['old' => $this->username, 'new' => $newName]]);
            return ['response' => $result];
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to change the username'];
        }
    }
    
    /**
     * Update user profile data
     * @return array
     */
    public function updateProfile(): array
    {
        if (empty($_POST['details'])) {
            return ['http_error' => 400, 'reason' => 'No data provided'];
        }
        #Ensure we get current values (to not generate queries for fields with same data
        $this->get();
        #Generate queries and data for log
        $queries = [];
        $log = [];
        #Queries for names
        foreach (['firstname', 'lastname', 'middlename', 'fathername', 'prefix', 'suffix'] as $field) {
            $_POST['details']['name'][$field] = Sanitization::removeNonPrintable($_POST['details']['name'][$field] ?? '', true);
            if ($this->name[$field] !== $_POST['details']['name'][$field]) {
                $log[$field] = ['old' => $this->name[$field], 'new' => $_POST['details']['name'][$field]];
                $queries[] = [
                    'UPDATE `uc__users` SET `'.$field.'`=:'.$field.' WHERE `userid`=:userid;',
                    [
                        ':userid' => [$this->id, 'int'],
                        ':'.$field => [
                            (empty($_POST['details']['name'][$field]) ? NULL : $_POST['details']['name'][$field]),
                            (empty($_POST['details']['name'][$field]) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        #Query for birthday
        if (isset($_POST['details']['dates']['birthday']) && $this->dates['birthday'] !== $_POST['details']['dates']['birthday']) {
            $log['birthday'] = ['old' => $this->name['birthday'], 'new' => $_POST['details']['dates']['birthday']];
            $queries[] = [
                'UPDATE `uc__users` SET `birthday`=:birthday WHERE `userid`=:userid;',
                [
                    ':userid' => [$this->id, 'int'],
                    ':birthday' => [
                        (empty($_POST['details']['dates']['birthday']) ? NULL : $_POST['details']['dates']['birthday']),
                        (empty($_POST['details']['dates']['birthday']) ? 'null' : 'date'),
                    ],
                ]
            ];
        }
        #Query for timezone
        $_POST['details']['timezone'] = Sanitization::removeNonPrintable($_POST['details']['timezone'] ?? 'UTC', true);
        if ($this->timezone !== $_POST['details']['timezone'] && in_array($_POST['details']['timezone'], timezone_identifiers_list(), true)) {
            $log['timezone'] = ['old' => $this->timezone, 'new' => $_POST['details']['timezone']];
            $queries[] = [
                'UPDATE `uc__users` SET `timezone`=:timezone WHERE `userid`=:userid;',
                [
                    ':userid' => [$this->id, 'int'],
                    ':timezone' => [
                        (empty($_POST['details']['timezone']) ? NULL : $_POST['details']['timezone']),
                        (empty($_POST['details']['timezone']) ? 'null' : 'string'),
                    ],
                ]
            ];
        }
        #Query for sex
        if (isset($_POST['details']['sex'])) {
            if ($_POST['details']['sex'] === 'null') {
                $_POST['details']['sex'] = null;
            } elseif ($_POST['details']['sex'] > 1) {
                $_POST['details']['sex'] = 1;
            } elseif ($_POST['details']['sex'] <= 0) {
                $_POST['details']['sex'] = 0;
            }
            if ($this->sex !== $_POST['details']['sex']) {
                $log['sex'] = ['old' => $this->sex, 'new' => $_POST['details']['sex']];
                $queries[] = [
                    'UPDATE `uc__users` SET `sex`=:sex WHERE `userid`=:userid;',
                    [
                        ':userid' => [$this->id, 'int'],
                        ':sex' => [
                            ($_POST['details']['sex']),
                            ($_POST['details']['sex'] === NULL ? 'null' : 'int'),
                        ],
                    ]
                ];
            }
        }
        #Query for website
        if (isset($_POST['details']['website'])) {
            $_POST['details']['website'] = Security::sanitizeURL($_POST['details']['website']);
            if (!empty($_POST['details']['website']) || mb_strlen($_POST['details']['website'], 'UTF-8') > 255) {
                $_POST['details']['website'] = $this->website ?? null;
            }
            if ($this->website !== $_POST['details']['website']) {
                $log['website'] = ['old' => $this->website, 'new' => $_POST['details']['website']];
                $queries[] = [
                    'UPDATE `uc__users` SET `timezone`=:timezone WHERE `userid`=:userid;',
                    [
                        ':userid' => [$this->id, 'int'],
                        ':timezone' => [
                            (empty($_POST['details']['timezone']) ? NULL : $_POST['details']['timezone']),
                            (empty($_POST['details']['timezone']) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        #Queries for other fields
        foreach (['country', 'city', 'about'] as $field) {
            $_POST['details'][$field] = Sanitization::removeNonPrintable($_POST['details'][$field] ?? '', true);
            if ($this->$field !== $_POST['details'][$field]) {
                $log[$field] = ['old' => $this->$field, 'new' => $_POST['details'][$field]];
                $queries[] = [
                    'UPDATE `uc__users` SET `'.$field.'`=:'.$field.' WHERE `userid`=:userid;',
                    [
                        ':userid' => [$this->id, 'int'],
                        ':'.$field => [
                            (empty($_POST['details'][$field]) ? NULL : $_POST['details'][$field]),
                            (empty($_POST['details'][$field]) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        if (empty($queries)) {
            return ['http_error' => 400, 'reason' => 'No changes detected'];
        }
        $result = Config::$dbController->query($queries);
        #Log the change
        Security::log('User details change', 'Changed details', $log);
        return ['response' => $result];
    }
    
    /**
     * Function to check if username is already used
     * @param string $name
     *
     * @return bool
     */
    public function usedName(string $name): bool
    {
        #Check against DB table
        try {
            return Config::$dbController->check('SELECT `username` FROM `uc__users` WHERE `username`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Function to check whether name is banned
     * @param string $name
     *
     * @return bool
     */
    public function bannedName(string $name): bool
    {
        #Check format
        if (preg_match('/^[\p{L}\d.!$%&\'*+\/=?_`{|}~\- ^]{1,64}$/ui', $name) !== 1) {
            return true;
        }
        #Check against DB table
        try {
            return Config::$dbController->check('SELECT `name` FROM `ban__names` WHERE `name`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Login to the system
     * @param bool $afterRegister Flag indicating if login is being done after initial registration
     *
     * @return array|true[]
     */
    public function login(bool $afterRegister = false): array
    {
        #Check if already logged in and return early
        if ($_SESSION['userid'] !== 1) {
            if ($afterRegister) {
                return ['status' => 201, 'response' => true];
            }
            return ['response' => true];
        }
        #Validating data
        if (empty($_POST['signinup']['email'])) {
            Security::log('Failed login', 'No email provided');
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        if (empty($_POST['signinup']['password'])) {
            Security::log('Failed login', 'No password provided');
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        #Check if banned
        $isEmail = filter_var($_POST['signinup']['email'], FILTER_VALIDATE_EMAIL);
        if (
            (!$isEmail && $this->bannedName($_POST['signinup']['email'])) ||
            ($isEmail && (new Email($_POST['signinup']['email']))->isBanned())
        ) {
            Security::log('Failed login', 'Prohibited credentials provided: `'.$_POST['signinup']['email'].'`');
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (empty(Config::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get password of the user, while also checking if it exists
        try {
            $credentials = Config::$dbController->selectRow('SELECT `uc__users`.`userid`, `uc__users`.`username`, `uc__users`.`password`, `uc__users`.`strikes` FROM `uc__emails` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__emails`.`userid` WHERE `uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail LIMIT 1',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if password is set (means that user does exist)
        if (empty($credentials['password'])) {
            Security::log('Failed login', 'No user found');
            return ['http_error' => 403, 'reason' => 'Wrong login or password'];
        }
        #Check for strikes
        if ($credentials['strikes'] >= 5) {
            Security::log('Failed login', 'Too many failed login attempts');
            return ['http_error' => 403, 'reason' => 'Too many failed login attempts. Try password reset.'];
        }
        #Check the password
        if ($this->setId($credentials['userid'])->passValid($_POST['signinup']['password'], $credentials['password']) === false) {
            Security::log('Failed login', 'Bad password');
            return ['http_error' => 403, 'reason' => 'Wrong login or password'];
        }
        #Get permissions
        $_SESSION['permissions'] = $this->getPermissions();
        if (!in_array('canLogin', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canLogin` permission'];
        }
        #Add username and userid to session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['userid'] = $credentials['userid'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            $this->rememberMe();
            Security::log('Login', 'Successful login with cookie setup', 'Cookie ID is '.($_SESSION['cookieid'] ?? 'NULL'));
        } else {
            Security::log('Login', 'Successful login');
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @session_regenerate_id(true);
        }
        if ($afterRegister) {
            return ['status' => 201, 'response' => true];
        }
        return ['response' => true];
    }
    
    /**
     * Setting cookie for remembering user
     * @param string $cookieId
     *
     * @return void
     */
    public function rememberMe(string $cookieId = ''): void
    {
        try {
            #Generate cookie ID
            if (empty($cookieId)) {
                $cookieId = bin2hex(random_bytes(64));
            }
            #Generate cookie password
            $pass = bin2hex(random_bytes(128));
            $hashedPass = Security::passHash($pass);
            #Write cookie data to DB
            if (Config::$dbController !== null && ((!empty($_SESSION['userid']) && !in_array($_SESSION['userid'], [Config::userIDs['Unknown user'], Config::userIDs['System user'], Config::userIDs['Deleted user']], true)) || !empty($this->id))) {
                #Check if cookie exist, and get its `validator`. This also helps with race conditions a bit
                $currentPass = Config::$dbController->selectValue('SELECT `validator` FROM `uc__cookies` WHERE `userid`=:id AND `cookieid`=:cookie',
                    [
                        ':id' => [$this->id ?? $_SESSION['userid'], 'int'],
                        ':cookie' => $cookieId
                    ]
                );
                if (empty($currentPass)) {
                    Config::$dbController->query('INSERT IGNORE INTO `uc__cookies` (`cookieid`, `validator`, `userid`) VALUES (:cookie, :pass, :id);',
                        [
                            ':cookie' => $cookieId,
                            ':pass' => $hashedPass,
                            ':id' => [$this->id ?? $_SESSION['userid'], 'int'],
                        ]
                    );
                } else {
                    Config::$dbController->query('UPDATE `uc__cookies` SET `validator`=:pass, `time`=CURRENT_TIMESTAMP() WHERE `userid`=:id AND `cookieid`=:cookie AND `validator`=:validator;',
                        [
                            ':cookie' => $cookieId,
                            ':pass' => $hashedPass,
                            ':id' => [$this->id ?? $_SESSION['userid'], 'int'],
                            ':validator' => $currentPass,
                        ]
                    );
                }
                #Update stuff only if we did insert cookie or update the validator value
                if (Config::$dbController->getResult() > 0) {
                    #Set cookie ID to session if it's not already linked or if it was linked to other cookie (not sure if that would even be possible)
                    if (empty($_SESSION['cookieid']) || $_SESSION['cookieid'] !== $cookieId) {
                        $_SESSION['cookieid'] = $cookieId;
                    }
                    #Set cookie
                    $currentPass = Config::$dbController->selectValue('SELECT `validator` FROM `uc__cookies` WHERE `userid`=:id AND `cookieid`=:cookie',
                        [
                            ':id' => [$this->id ?? $_SESSION['userid'], 'int'],
                            ':cookie' => $cookieId
                        ]
                    );
                    #Another attempt to prevent race conditions
                    if ($currentPass === $hashedPass) {
                        setcookie('rememberme_'.Config::$http_host,
                            json_encode(['cookieid' => Security::encrypt($cookieId), 'pass' => Security::encrypt($pass)], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION),
                            array_merge(Config::$cookieSettings, ['expires' => time() + 2592000]),
                        );
                    }
                }
            }
            return;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            #Do nothing, since not critical
        }
    }
    
    /**
     * Function to validate password
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function passValid(#[\SensitiveParameter] string $password, #[\SensitiveParameter] string $hash): bool
    {
        if (empty($this->id)) {
            return false;
        }
        #Validate password
        try {
            if (password_verify($password, $hash)) {
                #Check if it needs rehashing
                if (password_needs_rehash($hash, PASSWORD_ARGON2ID, Config::$argonSettings)) {
                    #Rehash password and reset strikes (if any)
                    $this->passChange($password);
                } else {
                    #Reset strikes (if any)
                    $this->resetStrikes();
                }
                return true;
            }
            #Increase strike count
            Config::$dbController->query(
                'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `userid`=:userid',
                [':userid' => [$this->id, 'string']]);
            Security::log('Failed login', 'Strike added');
            return false;
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Function to change the password
     * @param string $password
     *
     * @return bool
     */
    public function passChange(#[\SensitiveParameter] string $password): bool
    {
        if (empty($this->id)) {
            return false;
        }
        $result = Config::$dbController->query(
            'UPDATE `uc__users` SET `password`=:password, `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [$this->id, 'string'],
                ':password' => [Security::passHash($password), 'string'],
            ]
        );
        if (session_status() === PHP_SESSION_ACTIVE) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @session_regenerate_id(true);
        }
        Security::log('Password change', 'Attempted to change password', $result);
        return $result;
    }
    
    /**
     * Reset number of failed logins
     * @return bool
     */
    public function resetStrikes(): bool
    {
        if (empty($this->id)) {
            return false;
        }
        return Config::$dbController->query(
            'UPDATE `uc__users` SET `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [(string)$this->id, 'string']
            ]
        );
    }
    
    /**
     * Delete cookie
     *
     * @param bool $logout Flag indicating whether cookie is being deleted during normal logout
     *
     * @return bool
     */
    public function deleteCookie(bool $logout = false): bool
    {
        if (empty($this->id) || empty($_POST['cookie'])) {
            return false;
        }
        $result = Config::$dbController->query(
            'DELETE FROM `uc__cookies` WHERE `userid`=:userid AND `cookieid`=:cookie;',
            [
                ':userid' => [$this->id, 'int'],
                ':cookie' => $_POST['cookie'],
            ]
        );
        if (Config::$dbController->getResult() > 0) {
            Security::log('Logout', $logout ? 'Cookie deleted during logout' : 'Manually deleted a cookie', 'Cookie ID deleted is '.$_POST['cookie']);
        }
        return $result;
    }
    
    /**
     * Delete session
     * @return bool
     */
    public function deleteSession(): bool
    {
        if (empty($this->id) || empty($_POST['session'])) {
            return false;
        }
        $result = Config::$dbController->query(
            'DELETE FROM `uc__sessions` WHERE `userid`=:userid AND `sessionid`=:session;',
            [
                ':userid' => [(string)$this->id, 'string'],
                ':session' => $_POST['session'],
            ]
        );
        if (Config::$dbController->getResult() > 0) {
            Security::log('Logout', 'Manually deleted a session', 'Session ID deleted is '.$_POST['session']);
        }
        return $result;
    }
    
    /**
     * Get threads created by user
     * @return array
     */
    public function getThreads(): array
    {
        $where = '`talks__threads`.`createdby`=:userid';
        $bindings = [':userid' => [$this->id, 'int'],];
        if (!in_array('viewScheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('viewPrivate', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:createdby)';
            $bindings[':createdby'] = [$_SESSION['userid'], 'int'];
        }
        $threads = (new Threads($bindings, $where, '`talks__threads`.`created` DESC'))->listEntities();
        #Clean any threads with empty `firstPost` (means thread is either empty or is in progress of creation)
        foreach ($threads['entities'] as $key => $thread) {
            if (empty($thread['firstPost'])) {
                unset($threads['entities'][$key]);
            }
        }
        return $threads['entities'];
    }
    
    /**
     * Get posts created by user
     * @return array
     */
    public function getPosts(): array
    {
        $where = '`talks__posts`.`createdby`=:createdby';
        $bindings = [':createdby' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int'],];
        if (!$this->id !== $_SESSION['userid'] && !in_array('viewScheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!$this->id !== $_SESSION['userid'] && !in_array('viewPrivate', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`private`=0';
        }
        $posts = (new Posts($bindings, $where, '`talks__posts`.`created` DESC'))->listEntities();
        if (!is_array($posts)) {
            return [];
        }
        return $posts['entities'];
    }
    
    /**
     * Similar to getPosts(), but only gets posts, that are the first posts in threads
     * @param bool $onlyWithBanner
     *
     * @return array
     */
    public function getTalksStarters(bool $onlyWithBanner = false): array
    {
        #Can't think of a good way to get this in 1 query, thus first getting the latest threads
        $threads = $this->getThreads();
        #Now we get post details
        if (!empty($threads)) {
            #Keep only items with ogimage
            if ($onlyWithBanner) {
                foreach ($threads as $key => $thread) {
                    if (empty($thread['ogimage'])) {
                        unset($threads[$key]);
                    } else {
                        $thread['ogimage'] = Images::ogImage($thread['ogimage']);
                        if (empty($thread['ogimage'])) {
                            unset($threads[$key]);
                        } else {
                            $threads[$key]['ogimage'] = $thread['ogimage'];
                        }
                    }
                }
                if (empty($threads)) {
                    return [];
                }
            }
            #Convert regular 0, 1, ... n IDs to real thread IDs for later use
            $threads = ArrayHelpers::DigitToKey($threads, 'id');
            #Get post IDs
            $ids = array_column($threads, 'firstPost');
        } else {
            return [];
        }
        #Get posts
        $where = '';
        $bindings = [':userid' => [$_SESSION['userid'], 'int']];
        if (!in_array('viewScheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('viewPrivate', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:createdby)';
            $bindings[':createdby'] = [$_SESSION['userid'], 'int'];
        }
        $posts = (new Posts($bindings, '`talks__posts`.`postid` IN ('.implode(',', $ids).')'.$where, '`talks__posts`.`created` DESC'))->listEntities();
        if (is_array($posts) && !empty($posts['entities'])) {
            #Get like value, for each post, if current user has appropriate permission
            foreach ($posts['entities'] as &$post) {
                if (!empty($threads[$post['threadid']]['ogimage'])) {
                    $post['ogimage'] = $threads[$post['threadid']]['ogimage'];
                }
            }
            return $posts['entities'];
        }
        return [];
    }
    
    /**
     * Function to log the user out
     * @return bool
     */
    public function logout(): bool
    {
        Security::log('Logout', 'Logout');
        #Remove rememberme cookie
        #From browser
        setcookie('rememberme_'.Config::$http_host, '',
            array_merge(Config::$cookieSettings, ['expires' => time() - 3600])
        );
        #From DB
        if (!empty($_SESSION['cookieid'])) {
            $_POST['cookie'] = $_SESSION['cookieid'];
            $this->deleteCookie(true);
        }
        #Clean session (affects $_SESSION only)
        session_unset();
        #Destroy session (destroys it storage)
        $result = session_destroy();
        if (!headers_sent()) {
            header('Clear-Site-Data: "*"');
        }
        return $result;
    }
    
    /**
     * Function to remove the user
     * In case of errors, we return simple false. I think different error messages may be used by malicious actors here.
     * @param bool $hard
     *
     * @return bool
     */
    public function remove(bool $hard = false): bool
    {
        #Check if we are trying to remove one of the system users and prevent that
        if (in_array($this->id, Config::userIDs, true)) {
            return false;
        }
        try {
            #Close session to avoid changing it in any way
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @session_write_close();
            #Check if hard removal or regular one was requested
            if ($hard) {
                #Hard removal means complete removal of the user entity, except for sections/threads/posts/files created, where we first update the user IDs
                #The rest will be dealt with through foreign key constraints
                $queries = [
                    [
                        'UPDATE `talks__sections` SET `createdby`=:deleted WHERE `createdby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__sections` SET `updatedby`=:deleted WHERE `updatedby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `createdby`=:deleted WHERE `createdby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `updatedby`=:deleted WHERE `updatedby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `lastpostby`=:deleted WHERE `lastpostby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `createdby`=:deleted WHERE `createdby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `updatedby`=:deleted WHERE `updatedby`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts_history` SET `userid`=:deleted WHERE `userid`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `sys__files` SET `userid`=:deleted WHERE `userid`=:userid;',
                        [':userid' => [$this->id, 'int'], ':deleted' => [Config::userIDs['Deleted user'], 'int']]
                    ],
                    [
                        'DELETE FROM `talks__likes` WHERE `userid`=:userid;',
                        [':userid' => [$this->id, 'int']]
                    ],
                    [
                        'DELETE FROM `uc__users` WHERE `userid`=:userid;',
                        [':userid' => [$this->id, 'int']]
                    ],
                ];
            } else {
                #Soft removal only changes groups for the user
                $queries = [
                    [
                        'DELETE FROM `uc__user_to_group` WHERE `userid`=:userid;',
                        [':userid' => [$this->id, 'int']]
                    ],
                    [
                        'INSERT INTO `uc__user_to_group` (`userid`, `groupid`) VALUES (:userid, :groupid);',
                        [
                            ':userid' => [$this->id, 'int'],
                            ':groupid' => [Config::groupsIDs['Deleted'], 'int'],
                        ]
                    ],
                ];
            }
            #We also remove all cookies and sessions
            $queries[] = [
                'DELETE FROM `uc__cookies` WHERE `userid`=:userid;',
                [':userid' => $this->id]
            ];
            $queries[] = [
                'DELETE FROM `uc__sessions` WHERE `userid`=:userid;',
                [':userid' => $this->id]
            ];
            #If queries ran successfully - logout properly
            if (Config::$dbController->query($queries)) {
                $this->logout();
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            $result = false;
        }
        #Log
        Security::log('User removal', 'Removal', ['userid' => $this->id, 'hard' => $hard, 'result' => $result], ($hard ? Config::userIDs['Deleted user'] : $this->id));
        return $result;
    }
}