<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Common;
use Simbiat\Config\Talks;
use Simbiat\Curl;
use Simbiat\HomePage;
use Simbiat\Security;
use Simbiat\Talks\Entities\Post;
use Simbiat\Talks\Search\Posts;
use Simbiat\Talks\Search\Threads;

class User extends Entity
{
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

    protected function getFromDB(): array
    {
        
        $dbData =  HomePage::$dbController->selectRow('SELECT `username`, `phone`, `ff_token`, `registered`, `updated`, `parentid`, (IF(`parentid` IS NULL, NULL, (SELECT `username` FROM `uc__users` WHERE `userid`=:userid))) as `parentname`, `birthday`, `firstname`, `lastname`, `middlename`, `fathername`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website` FROM `uc__users` WHERE `userid`=:userid', ['userid'=>[$this->id, 'int']]);
        if (empty($dbData)) {
            return [];
        }
        #Get user's groups
        $dbData['groups'] = HomePage::$dbController->selectColumn('SELECT `groupid` FROM `uc__user_to_group` WHERE `userid`=:userid', ['userid' => [$this->id, 'int']]);
        #Get permissions
        $dbData['permissions'] = $this->getPermissions();
        if (in_array($this->id, [Talks::userIDs['Unknown user'], Talks::userIDs['System user'], Talks::userIDs['Deleted user']])) {
            #System users need to be treated as not activated
            $dbData['activated'] = false;
        } else {
            $dbData['activated'] = !in_array(Talks::groupsIDs['Unverified'], $dbData['groups'], true);
        }
        $dbData['currentAvatar'] = $this->getAvatar();
        return $dbData;
    }

    #Function to do processing
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
        #Cleanup the array
        unset($fromDB['parentid'], $fromDB['parentname'], $fromDB['firstname'], $fromDB['lastname'], $fromDB['middlename'], $fromDB['fathername'], $fromDB['prefix'], $fromDB['suffix'], $fromDB['registered'], $fromDB['updated'], $fromDB['birthday']);
        #Populate the rest properties
        $this->arrayToProperties($fromDB);
    }

    private function getPermissions(): array
    {
        try {
            return HomePage::$dbController->selectColumn('
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
    public function getEmails(): array
    {
        try {
            return HomePage::$dbController->selectAll('SELECT `email`, `subscribed`, `activation` FROM `uc__emails` WHERE `userid`=:userid ORDER BY `email`;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
    
    #Get current avatar
    public function getAvatar(): string
    {
        try {
            $avatar = HomePage::$dbController->selectValue('SELECT CONCAT(\'/img/avatars/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) AS `url` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid AND `current`=1 LIMIT 1', ['userid' => [$this->id, 'int']]);
            if (empty($avatar)) {
                $avatar = '/img/avatar.svg';
            }
        } catch (\Throwable) {
            $avatar = '/img/avatar.svg';
        }
        return $avatar;
    }
    
    #Get all avatars
    public function getAvatars(): array
    {
        try {
            return HomePage::$dbController->selectAll('SELECT CONCAT(\'/img/avatars/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) as `url`, `uc__avatars`.`fileid`, `current` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid ORDER BY `current` DESC;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
    
    public function addAvatar(bool $setActive = false, string $link = ''): array
    {
        $upload = (new Curl)->upload($link);
        if ($upload === false || empty($upload['type'])) {
            if (!empty($upload['http_error'])) {
                return $upload;
            } else {
                return ['http_error' => 500, 'reason' => 'Failed to upload file'];
            }
        }
        if (preg_match('/^image\/.+/ui', $upload['type']) !== 1) {
            #Remove file from DB
            @HomePage::$dbController->query('DELETE FROM `sys__files` WHERE `fileid`=:fileid;', [':fileid' => $upload['hash']]);
            #Remove physical file
            @unlink($upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name']);
            return ['http_error' => 400, 'reason' => 'File is not an image'];
        }
        try {
            #Add to DB
            HomePage::$dbController->query('INSERT IGNORE INTO `uc__avatars` (`userid`, `fileid`, `current`) VALUES (:userid, :fileid, 0);', [':userid' => [$this->id, 'int'], ':fileid' => $upload['hash']]);
            if ($setActive) {
                return $this->setAvatar($upload['hash']);
            }
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to add avatar to library'];
        }
        return ['location' => $upload['location'], 'response' => true];
    }
    
    public function delAvatar(): array
    {
        $fileid = $_POST['avatar'] ?? '';
        #Delete the avatar (only allow deletion of those, that are not current)
        HomePage::$dbController->query('DELETE FROM `uc__avatars` WHERE `userid`=:userid AND `fileid`=:fileid AND `current`=0;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }
    
    public function setAvatar(string $fileid = ''): array
    {
        $fileid = $_POST['avatar'] ?? $fileid ?? '';
        HomePage::$dbController->query([
            #Set the chosen avatar as current
            ['UPDATE `uc__avatars` SET `current`=1 WHERE `userid`=:userid AND `fileid`=:fileid;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]],
            #Set the rest as non-current
            ['UPDATE `uc__avatars` SET `current`=0 WHERE `userid`=:userid AND `fileid`<>:fileid;', [':userid' => [$this->id, 'int'], ':fileid' => $fileid]],
        ]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }

    public function getFF(): array
    {
        $outputArray = [];
        #Get token
        $outputArray['token'] = HomePage::$dbController->selectValue('SELECT `ff_token` FROM `uc__users` WHERE `userid`=:userid;', [':userid' => [$this->id, 'int']]);
        #Get linked characters
        $outputArray['characters'] = HomePage::$dbController->selectAll('SELECT \'character\' as `type`, `characterid` as `id`, `name`, `avatar` as `icon` FROM `ffxiv__character` WHERE `userid`=:userid ORDER BY `name`;', [':userid' => [$this->id, 'int']]);
        #Get linked groups
        if (!empty($outputArray['characters'])) {
            foreach ($outputArray['characters'] as $character) {
                $outputArray['groups'][$character['id']] = HomePage::$dbController->selectAll(
                    '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, COALESCE(`ffxiv__freecompany`.`crest`, `ffxiv__freecompany`.`grandcompanyid`) AS `icon` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__freecompany_character`.`rankid`=`ffxiv__freecompany_rank`.`rankid` WHERE `characterid`=:id AND `ffxiv__freecompany_character`.`current`=1 AND `ffxiv__freecompany_character`.`rankid`=0)
                UNION ALL
                (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, NULL AS `icon` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rankid`=`ffxiv__linkshell_rank`.`lsrankid` WHERE `characterid`=:id AND `ffxiv__linkshell_character`.`current`=1 AND `ffxiv__linkshell_character`.`rankid`=1)
                UNION ALL
                (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, `ffxiv__pvpteam`.`crest` AS `icon` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rankid`=`ffxiv__pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `ffxiv__pvpteam_character`.`current`=1 AND `ffxiv__pvpteam_character`.`rankid`=1)
                ORDER BY `name` ASC;',
                    [':id' => [$character['id'], 'int']]
                );
            }
        }
        return $outputArray;
    }

    public function changeUsername(string $newName): array
    {
        #Check if new name is valid
        if (empty($newName) && $this->bannedName($newName) || $this->usedName($newName)) {
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
            $result = HomePage::$dbController->query('UPDATE `uc__users` SET `username`=:username WHERE `userid`=:userid;', [
                ':userid' => [$this->id, 'int'],
                ':username' => $newName,
            ]);
            if ($result) {
                $_SESSION['username'] = $newName;
            }
            if (session_status() === PHP_SESSION_ACTIVE) {
                @session_regenerate_id(true);
            }
            #Log the change
            Security::log('User details change', 'Changed name', ['name'=> ['old' => $this->username, 'new' => $newName]]);
            return ['response' => $result];
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to change the username'];
        }
    }

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
            if (isset($_POST['details']['name'][$field]) && $this->name[$field] !== $_POST['details']['name'][$field]) {
                $log[$field] = ['old' => $this->name[$field], 'new' => $_POST['details']['name'][$field]];
                /** @noinspection SqlResolve */
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
        if (isset($_POST['details']['timezone']) && $this->timezone !== $_POST['details']['timezone'] && in_array($_POST['details']['timezone'], timezone_identifiers_list())) {
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
            } else {
                if ($_POST['details']['sex'] > 1) {
                    $_POST['details']['sex'] = 1;
                } elseif ($_POST['details']['sex'] < 0) {
                    $_POST['details']['sex'] = 0;
                }
            }
            if ($this->sex !== $_POST['details']['sex']) {
                $log['sex'] = ['old' => $this->sex, 'new' => $_POST['details']['sex']];
                $queries[] = [
                    'UPDATE `uc__users` SET `sex`=:sex WHERE `userid`=:userid;',
                    [
                        ':userid' => [$this->id, 'int'],
                        ':sex' => [
                            ($_POST['details']['sex'] === NULL ? NULL : $_POST['details']['sex']),
                            ($_POST['details']['sex'] === NULL ? 'null' : 'int'),
                        ],
                    ]
                ];
            }
        }
        #Query for website
        if (isset($_POST['details']['website'])) {
            if (preg_match('/https?:\/\/(www\.)?[-a-zA-Z\d@:%._+~#=]{1,256}\.[a-zA-Z\d()]{1,6}\b([-a-zA-Z\d()@:%_+.~#?&/=]*)/ui', $_POST['details']['website']) !== 1 || mb_strlen($_POST['details']['website']) > 255) {
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
            if (isset($_POST['details'][$field]) && $this->$field !== $_POST['details'][$field]) {
                $log[$field] = ['old' => $this->$field, 'new' => $_POST['details'][$field]];
                /** @noinspection SqlResolve */
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
        } else {
            $result = HomePage::$dbController->query($queries);
            #Log the change
            Security::log('User details change', 'Changed details', $log);
            return ['response' => $result];
        }
    }

    #Function to check if username is already used
    public function usedName(string $name): bool
    {
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `username` FROM `uc__users` WHERE `username`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether name is banned
    public function bannedName(string $name): bool
    {
        #Check format
        if (preg_match('/^[\p{L}\d.!#$%&\'*+\/=?_`{|}~\- ^]{1,64}$/ui', $name) !== 1) {
            return false;
        }
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `name` FROM `ban__names` WHERE `name`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }

    public function login(bool $afterRegister = false): array
    {
        #Check if already logged in and return early
        if ($_SESSION['userid'] !== 1) {
            if ($afterRegister) {
                return ['status' => 201, 'response' => true];
            } else {
                return ['response' => true];
            }
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
        if (
            (new Email($_POST['signinup']['email']))->isBanned() ||
            $this->bannedName($_POST['signinup']['email'])
        ) {
            Security::log('Failed login', 'Prohibited credentials provided: `'.$_POST['signinup']['email'].'`');
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get password of the user, while also checking if it exists
        try {
            $credentials = HomePage::$dbController->selectRow('SELECT `uc__users`.`userid`, `username`, `password`, `strikes` FROM `uc__emails` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__emails`.`userid` WHERE `uc__emails`.`email`=:mail',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if password is set (means that user does exist)
        if (empty($credentials['password'])) {
            Security::log('Failed login', 'No user found');
            return ['http_error' => 403, 'reason' => 'No user found'];
        }
        #Check for strikes
        if ($credentials['strikes'] >= 5) {
            Security::log('Failed login', 'Too many failed login attempts');
            return ['http_error' => 403, 'reason' => 'Too many failed login attempts. Try password reset.'];
        }
        #Check the password
        if ($this->setId($credentials['userid'])->passValid($_POST['signinup']['password'], $credentials['password']) === false) {
            Security::log('Failed login', 'Bad password');
            return ['http_error' => 403, 'reason' => 'Bad password'];
        }
        #Get permissions
        $_SESSION['permissions'] = $this->getPermissions();
        if (!in_array('canLogin', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `canLogin` permission'];
        }
        #Add username and userid to session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['userid'] = $credentials['userid'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            $this->rememberMe();
            Security::log('Login', 'Successful login with cookie setup');
        } else {
            Security::log('Login', 'Successful login');
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        if ($afterRegister) {
            return ['status' => 201, 'response' => true];
        } else {
            return ['response' => true];
        }
    }

    #Setting cookie for remembering user
    public function rememberMe(string $cookieId = ''): void
    {
        try {
            #Generate cookie ID
            if (empty($cookieId)) {
                $cookieId = bin2hex(random_bytes(64));
            }
            #Generate cookie password
            $pass = bin2hex(random_bytes(128));
            #Write cookie data to DB
            if (HomePage::$dbController === null) {
                #If we can't write to DB for some reason - do not share any data with client
                return;
            }
            if (HomePage::$dbController !== null && ((!empty($_SESSION['userid']) && !in_array($_SESSION['userid'], [Talks::userIDs['Unknown user'], Talks::userIDs['System user'], Talks::userIDs['Deleted user']])) || !empty($this->id))) {
                HomePage::$dbController->query('INSERT INTO `uc__cookies` (`cookieid`, `validator`, `userid`) VALUES (:cookie, :pass, :id) ON DUPLICATE KEY UPDATE `validator`=:pass, `userid`=:id, `time`=CURRENT_TIMESTAMP();',
                    [
                        ':cookie' => $cookieId,
                        ':pass' => hash('sha3-512', $pass),
                        ':id' => $this->id ?? [$_SESSION['userid'], 'int'],
                    ]
                );
                #Set cookie ID to session if it's not already linked or if it was linked to other cookie (not sure if that would even be possible)
                if (empty($_SESSION['cookieid']) || $_SESSION['cookieid'] !== $cookieId) {
                    $_SESSION['cookieid'] = $cookieId;
                }
            } else {
                return;
            }
            #Set options
            $options = ['expires' => gmdate('D, d-M-Y H:i:s', time()+60*60*24*30).' GMT', 'path' => '/', 'domain' => Common::$http_host, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict'];
            #Set cookie value
            $value = json_encode(['id' => Security::encrypt($cookieId), 'pass'=> $pass],JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
            setcookie('rememberme_'.Common::$http_host, $value, $options);
        } catch (\Throwable) {
            #Do nothing, since not critical
        }
    }

    #Function to validate password
    public function passValid(string $password, string $hash): bool
    {
        if (empty($this->id)) {
            return false;
        }
        #Validate password
        try {
            if (password_verify($password, $hash)) {
                #Check if it needs rehashing
                if (password_needs_rehash($hash, PASSWORD_ARGON2ID, \Simbiat\Config\Security::$argonSettings)) {
                    #Rehash password and reset strikes (if any)
                    $this->passChange($password);
                } else {
                    #Reset strikes (if any)
                    $this->resetStrikes();
                }
                return true;
            } else {
                #Increase strike count
                HomePage::$dbController->query(
                    'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `userid`=:userid',
                    [':userid' => [$this->id, 'string']]);
                Security::log('Failed login', 'Strike added');
                return false;
            }
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to change the password
    public function passChange(string $password): bool
    {
        if (empty($this->id)) {
            return false;
        }
        $result = HomePage::$dbController->query(
            'UPDATE `uc__users` SET `password`=:password, `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [$this->id, 'string'],
                ':password' => [Security::passHash($password), 'string'],
            ]
        );
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        Security::log('Password change', 'Attempted to change password', $result);
        return $result;
    }

    public function resetStrikes(): bool
    {
        if (empty($this->id)) {
            return false;
        }
        return HomePage::$dbController->query(
            'UPDATE `uc__users` SET `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [strval($this->id), 'string']
            ]
        );
    }
    
    public function deleteCookie(): bool
    {
        if (empty($this->id) || empty($_POST['cookie'])) {
            return false;
        }
        Security::log('Logout', 'Manually deleted a cookie');
        return HomePage::$dbController->query(
            'DELETE FROM `uc__cookies` WHERE `userid`=:userid AND `cookieid`=:cookie;',
            [
                ':userid' => [strval($this->id), 'string'],
                ':cookie' => $_POST['cookie'],
            ]
        );
    }
    
    public function deleteSession(): bool
    {
        if (empty($this->id) || empty($_POST['session'])) {
            return false;
        }
        Security::log('Logout', 'Manually deleted a session');
        return HomePage::$dbController->query(
            'DELETE FROM `uc__sessions` WHERE `userid`=:userid AND `sessionid`=:session;',
            [
                ':userid' => [strval($this->id), 'string'],
                ':session' => $_POST['session'],
            ]
        );
    }
    
    public function getThreads(): array
    {
        $where = '`talks__threads`.`createdby`=:userid';
        $bindings = [':userid' => [$this->id, 'int'],];
        if (!in_array('viewScheduled', $_SESSION['permissions'])) {
            $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('viewPrivate', $_SESSION['permissions'])) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:createdby)';
            $bindings[':createdby'] = [$_SESSION['userid'], 'int'];
        }
        $threads = (new Threads($bindings, $where, '`talks__threads`.`created` DESC'))->listEntities();
        return $threads['entities'];
    }
    
    public function getPosts(): array
    {
        $where = '`talks__posts`.`createdby`=:userid';
        $bindings = [':userid' => [$this->id, 'int'],];
        if (!in_array('viewScheduled', $_SESSION['permissions'])) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('viewPrivate', $_SESSION['permissions'])) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:createdby)';
            $bindings[':createdby'] = [$_SESSION['userid'], 'int'];
        }
        $posts = (new Posts($bindings, $where, '`talks__posts`.`created` DESC'))->listEntities();
        #Get like value, for each post, if current user has appropriate permission
        $postObject = new Post();
        foreach ($posts['entities'] as &$post) {
            $post['liked'] = $postObject->setId($post['id'])->isLiked();
        }
        return $posts['entities'];
    }
    
    #Similar to getPosts(), but only gets posts, that are the first posts in threads
    public function getTalksStarters(): array
    {
        #Can't think of a good way to get this in 1 query, thus first getting the latest threads
        $threads = $this->getThreads();
        #Now we get post details
        if (!empty($threads)) {
            #Get post IDs
            $ids = array_column($threads, 'firstPost');
        } else {
            return [];
        }
        #Get posts
        $where = '';
        $bindings = [];
        if (!in_array('viewScheduled', $_SESSION['permissions'])) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('viewPrivate', $_SESSION['permissions'])) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:createdby)';
            $bindings[':createdby'] = [$_SESSION['userid'], 'int'];
        }
        $posts = (new Posts($bindings, '`talks__posts`.`postid` IN ('.implode(',', $ids).')'.$where, '`talks__posts`.`created` DESC'))->listEntities();
        if (!empty($posts['entities'])) {
            #Get like value, for each post, if current user has appropriate permission
            $postObject = new Post();
            foreach ($posts['entities'] as &$post) {
                $post['liked'] = $postObject->setId($post['id'])->isLiked();
            }
            return $posts['entities'];
        } else {
            return [];
        }
    }
}
