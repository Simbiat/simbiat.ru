<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol;

use Simbiat\Arrays\Converters;
use Simbiat\Arrays\Editors;
use Simbiat\Database\Query;
use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Errors;
use Simbiat\Website\Images;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Search\Posts;
use Simbiat\Website\Search\Threads;
use Simbiat\Website\Security;
use function in_array;
use function is_array;
use function is_string;

/**
 * Main user class
 */
class User extends Entity
{
    #Maximum number of unused avatars per user
    public const int AVATAR_LIMIT = 10;
    #Entity's properties
    public string $username;
    #System flag, if true, user can't be deleted
    public bool $system = false;
    #Real name
    public array $name = [
        'first_name' => null,
        'last_name' => null,
        'middle_name' => null,
        'father_name' => null,
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
    #Time zone
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
    public array $permissions = ['view_posts', 'view_bic', 'view_ff'];
    #Whether the account is activated
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
        
        $dbData = Query::query('SELECT `username`, `system`, `phone`, `ff_token`, `registered`, `updated`, `parent_id`, (IF(`parent_id` IS NULL, NULL, (SELECT `username` FROM `uc__users` WHERE `user_id`=:user_id))) as `parentname`, `birthday`, `first_name`, `last_name`, `middle_name`, `father_name`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website`, `blog`, `changelog`, `knowledgebase` FROM `uc__users` LEFT JOIN `uc__user_to_section` ON `uc__users`.`user_id`=`uc__user_to_section`.`user_id` WHERE `uc__users`.`user_id`=:user_id OR `uc__users`.`user_id`=2', ['user_id' => [$this->id, 'int']], return: 'row');
        if (empty($dbData)) {
            return [];
        }
        #Get user's groups
        $dbData['groups'] = Query::query('SELECT `group_id` FROM `uc__user_to_group` WHERE `user_id`=:user_id', ['user_id' => [$this->id, 'int']], return: 'column');
        #Get permissions
        $dbData['permissions'] = $this->getPermissions();
        if ($this->system) {
            #System users need to be treated as not activated
            $dbData['activated'] = false;
        } else {
            $dbData['activated'] = !in_array(Config::GROUP_IDS['Unverified'], $dbData['groups'], true);
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
        $this->name['first_name'] = $fromDB['first_name'];
        $this->name['last_name'] = $fromDB['last_name'];
        $this->name['middle_name'] = $fromDB['middle_name'];
        $this->name['father_name'] = $fromDB['father_name'];
        $this->name['prefix'] = $fromDB['prefix'];
        $this->name['suffix'] = $fromDB['suffix'];
        #Populate dates
        $this->dates['registered'] = $fromDB['registered'];
        $this->dates['updated'] = $fromDB['updated'];
        $this->dates['birthday'] = $fromDB['birthday'];
        #Populate parent details
        $this->parent['id'] = $fromDB['parent_id'];
        $this->parent['name'] = $fromDB['parentname'];
        #Pupulate personal sections
        $this->sections = [
            'blog' => empty($fromDB['blog']) ? null : $fromDB['blog'],
            'changelog' => empty($fromDB['changelog']) ? null : $fromDB['changelog'],
            'knowledgebase' => empty($fromDB['knowledgebase']) ? null : $fromDB['knowledgebase'],
        ];
        $this->system = (bool)$fromDB['system'];
        #Clean up the array
        unset($fromDB['system'], $fromDB['parent_id'], $fromDB['parentname'], $fromDB['first_name'], $fromDB['last_name'], $fromDB['middle_name'], $fromDB['father_name'], $fromDB['prefix'],
            $fromDB['suffix'], $fromDB['registered'], $fromDB['updated'], $fromDB['birthday'], $fromDB['blog'], $fromDB['changelog'], $fromDB['knowledgebase']);
        #Populate the rest properties
        Converters::arrayToProperties($this, $fromDB);
    }
    
    /**
     * Get user permissions
     * @return array
     */
    private function getPermissions(): array
    {
        try {
            return Query::query('
                SELECT * FROM (
                    SELECT `uc__group_to_permission`.`permission` FROM `uc__group_to_permission` LEFT JOIN `uc__groups` ON `uc__group_to_permission`.`group_id`=`uc__groups`.`group_id` LEFT JOIN `uc__permissions` ON `uc__group_to_permission`.`permission`=`uc__permissions`.`permission` LEFT JOIN `uc__user_to_group` ON `uc__group_to_permission`.`group_id`=`uc__user_to_group`.`group_id` WHERE `user_id`=:user_id
                    UNION ALL
                    SELECT `permission` FROM `uc__user_to_permission` WHERE `user_id`=:user_id
                ) as `temp` GROUP BY `permission`;
            ', ['user_id' => [$this->id, 'int']], return: 'column');
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
            $result = Query::query('SELECT `email`, `subscribed`, `activation` FROM `uc__emails` WHERE `user_id`=:user_id ORDER BY `email`;', [':user_id' => [$this->id, 'int']], return: 'all');
            $this->emails = $result;
            return $result;
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
            $avatar = Query::query('SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) AS `url` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`file_id`=`sys__files`.`file_id` WHERE `uc__avatars`.`user_id`=:user_id AND `current`=1 LIMIT 1', ['user_id' => [$this->id, 'int']], return: 'value');
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
            $result = Query::query('SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) as `url`, `uc__avatars`.`file_id`, `current` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`file_id`=`sys__files`.`file_id` WHERE `uc__avatars`.`user_id`=:user_id ORDER BY `current` DESC;', [':user_id' => [$this->id, 'int']], return: 'all');
            $this->avatars = $result;
            return $result;
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
            if ($this->system) {
                return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
            }
            #Get current avatars
            $avatars = $this->getAvatars();
            #Count values in `current` column
            $counts = array_count_values(array_column($avatars, 'current'));
            #If a count of 0 values does not exist, then we set it to 0 properly
            if (empty($counts[0])) {
                $counts[0] = 0;
            }
            #Check if we are not trying to add an excessive avatar (compare the number of non-current avatars to the limit).
            #If we are setting one for a character - ignore this limitation, though, because it is possible that this character is being used as the current avatar, which we will need to update
            if ($character === null && $counts[0] === self::AVATAR_LIMIT) {
                return ['http_error' => 413, 'reason' => 'Maximum of '.self::AVATAR_LIMIT.' unused avatars reached'];
            }
            $upload = new Curl()->upload($link, true);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            #Log the change
            Security::log('Avatar', 'Added avatar', $upload['hash']);
            #Add to DB
            Query::query(
                'INSERT IGNORE INTO `uc__avatars` (`user_id`, `file_id`, `character_id`, `current`) VALUES (:user_id, :file_id, :character, 0);',
                [
                    ':user_id' => [$this->id, 'int'],
                    ':file_id' => $upload['hash'],
                    ':character' => [
                        ($character),
                        ($character === null ? 'null' : 'int')
                    ]
                ]
            );
            if ($setActive) {
                return $this->setAvatar($upload['hash']);
            }
            if ($character !== null && Query::query('SELECT `file_id` FROM `uc__avatars` WHERE `user_id`=:user_id AND `current`=1 AND `character_id`=:character;', [':user_id' => [$this->id, 'int'], ':character' => [$character, 'int']], return: 'check')) {
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
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        $file_id = $_POST['avatar'] ?? '';
        #Log the change
        Security::log('Avatar', 'Deleted avatar', $file_id);
        #Delete the avatar (only allow deletion of those that are not current)
        Query::query('DELETE FROM `uc__avatars` WHERE `user_id`=:user_id AND `file_id`=:file_id AND `current`=0;', [':user_id' => [$this->id, 'int'], ':file_id' => $file_id]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }
    
    /**
     * Set current avatar
     * @param string $file_id
     *
     * @return array
     */
    public function setAvatar(string $file_id = ''): array
    {
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        if (!empty($_POST['avatar']) && is_string($_POST['avatar'])) {
            $file_id = $_POST['avatar'];
        }
        #Log the change
        Security::log('Avatar', 'Changed active avatar', $file_id);
        Query::query([
            #Set the chosen avatar as current
            ['UPDATE `uc__avatars` SET `current`=1 WHERE `user_id`=:user_id AND `file_id`=:file_id;', [':user_id' => [$this->id, 'int'], ':file_id' => $file_id]],
            #Set the rest as non-current
            ['UPDATE `uc__avatars` SET `current`=0 WHERE `user_id`=:user_id AND `file_id`<>:file_id;', [':user_id' => [$this->id, 'int'], ':file_id' => $file_id]],
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
        $outputArray['token'] = Query::query('SELECT `ff_token` FROM `uc__users` WHERE `user_id`=:user_id;', [':user_id' => [$this->id, 'int']], return: 'value');
        #Get linked characters
        $outputArray['characters'] = Query::query('SELECT \'character\' as `type`, `ffxiv__character`.`character_id` as `id`, `name`, `avatar` as `icon` FROM `ffxiv__character` LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id` WHERE `user_id`=:user_id ORDER BY `name`;', [':user_id' => [$this->id, 'int']], return: 'all');
        #Get linked groups
        if (!empty($outputArray['characters'])) {
            foreach ($outputArray['characters'] as $character) {
                $outputArray['groups'][$character['id']] = AbstractTrackerEntity::cleanCrestResults(Query::query(
                /** @lang SQL */ '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`fc_id` AS `id`, `ffxiv__freecompany`.`name` as `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `gc_id` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`fc_id`=`ffxiv__freecompany`.`fc_id` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`fc_id`=`ffxiv__freecompany`.`fc_id` AND `ffxiv__freecompany_character`.`rank_id`=`ffxiv__freecompany_rank`.`rank_id` WHERE `character_id`=:id AND `ffxiv__freecompany_character`.`current`=1 AND `ffxiv__freecompany_character`.`rank_id`=0)
                UNION ALL
                (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`ls_id` AS `id`, `ffxiv__linkshell`.`name` as `name`, null as `crest_part_1`, null as `crest_part_2`, null as `crest_part_3`, null as `gc_id` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`ls_id`=`ffxiv__linkshell`.`ls_id` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rank_id`=`ffxiv__linkshell_rank`.`ls_rank_id` WHERE `character_id`=:id AND `ffxiv__linkshell_character`.`current`=1 AND `ffxiv__linkshell_character`.`rank_id`=1)
                UNION ALL
                (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvp_id` AS `id`, `ffxiv__pvpteam`.`name` as `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, null as `gc_id` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvp_id`=`ffxiv__pvpteam`.`pvp_id` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rank_id`=`ffxiv__pvpteam_rank`.`pvp_rank_id` WHERE `character_id`=:id AND `ffxiv__pvpteam_character`.`current`=1 AND `ffxiv__pvpteam_character`.`rank_id`=1)
                ORDER BY `name`;',
                    [':id' => [$character['id'], 'int']], return: 'all'
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
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        $sanitizedName = Sanitization::removeNonPrintable($newName, true);
        if (!is_string($sanitizedName)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        $newName = $sanitizedName;
        #Check if the new name is valid
        if (empty($newName) || $this->bannedName($newName) || $this->usedName($newName)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        #Check if we have the current username and get it if we do not
        if (empty($this->username)) {
            $this->get();
        }
        if ($this->username === $newName) {
            return ['response' => true];
        }
        try {
            $result = Query::query('UPDATE `uc__users` SET `username`=:username WHERE `user_id`=:user_id;', [
                ':user_id' => [$this->id, 'int'],
                ':username' => $newName,
            ]);
            if ($result) {
                $_SESSION['username'] = $newName;
            }
            if (session_status() === PHP_SESSION_ACTIVE) {
                Security::session_regenerate_id(true);
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
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        if (empty($_POST['details'])) {
            return ['http_error' => 400, 'reason' => 'No data provided'];
        }
        #Ensure we get current values (to not generate queries for fields with the same data
        $this->get();
        #Generate queries and data for log
        $queries = [];
        $log = [];
        #Queries for names
        foreach (['first_name', 'last_name', 'middle_name', 'father_name', 'prefix', 'suffix'] as $field) {
            $_POST['details']['name'][$field] = Sanitization::removeNonPrintable($_POST['details']['name'][$field] ?? '', true);
            if ($this->name[$field] !== $_POST['details']['name'][$field]) {
                $log[$field] = ['old' => $this->name[$field], 'new' => $_POST['details']['name'][$field]];
                $queries[] = [
                    'UPDATE `uc__users` SET `'.$field.'`=:'.$field.' WHERE `user_id`=:user_id;',
                    [
                        ':user_id' => [$this->id, 'int'],
                        ':'.$field => [
                            (empty($_POST['details']['name'][$field]) ? NULL : $_POST['details']['name'][$field]),
                            (empty($_POST['details']['name'][$field]) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        #Query for a birthday
        if (isset($_POST['details']['dates']['birthday']) && $this->dates['birthday'] !== $_POST['details']['dates']['birthday']) {
            $log['birthday'] = ['old' => $this->name['birthday'], 'new' => $_POST['details']['dates']['birthday']];
            $queries[] = [
                'UPDATE `uc__users` SET `birthday`=:birthday WHERE `user_id`=:user_id;',
                [
                    ':user_id' => [$this->id, 'int'],
                    ':birthday' => [
                        (empty($_POST['details']['dates']['birthday']) ? NULL : $_POST['details']['dates']['birthday']),
                        (empty($_POST['details']['dates']['birthday']) ? 'null' : 'date'),
                    ],
                ]
            ];
        }
        #Query for time zone
        $_POST['details']['timezone'] = Sanitization::removeNonPrintable($_POST['details']['timezone'] ?? 'UTC', true);
        if ($this->timezone !== $_POST['details']['timezone'] && in_array($_POST['details']['timezone'], timezone_identifiers_list(), true)) {
            $log['timezone'] = ['old' => $this->timezone, 'new' => $_POST['details']['timezone']];
            $queries[] = [
                'UPDATE `uc__users` SET `timezone`=:timezone WHERE `user_id`=:user_id;',
                [
                    ':user_id' => [$this->id, 'int'],
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
                    'UPDATE `uc__users` SET `sex`=:sex WHERE `user_id`=:user_id;',
                    [
                        ':user_id' => [$this->id, 'int'],
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
                    'UPDATE `uc__users` SET `timezone`=:timezone WHERE `user_id`=:user_id;',
                    [
                        ':user_id' => [$this->id, 'int'],
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
                    'UPDATE `uc__users` SET `'.$field.'`=:'.$field.' WHERE `user_id`=:user_id;',
                    [
                        ':user_id' => [$this->id, 'int'],
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
        $result = Query::query($queries);
        #Log the change
        Security::log('User details change', 'Changed details', $log);
        return ['response' => $result];
    }
    
    /**
     * Function to check if a name is already used
     * @param string $name
     *
     * @return bool
     */
    public function usedName(string $name): bool
    {
        #Check against DB table
        try {
            return Query::query('SELECT `username` FROM `uc__users` WHERE `username`=:name', [':name' => $name], return: 'check');
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Function to check whether a name is banned
     * @param string $name
     *
     * @return bool
     */
    public function bannedName(string $name): bool
    {
        #Check the format
        if (preg_match('/^[\p{L}\d.!$%&\'*+\/=?_`{|}~\- ^]{1,64}$/ui', $name) !== 1) {
            return true;
        }
        #Check against DB table
        try {
            return Query::query('SELECT `name` FROM `uc__bad_names` WHERE `name`=:name', [':name' => $name], return: 'check');
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
        if ($_SESSION['user_id'] !== 1) {
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
        $isEmail = filter_var($_POST['signinup']['email'], FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
        if (
            (!$isEmail && $this->bannedName($_POST['signinup']['email'])) ||
            ($isEmail && new Email($_POST['signinup']['email'])->isBanned())
        ) {
            Security::log('Failed login', 'Prohibited credentials provided: `'.$_POST['signinup']['email'].'`');
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (Query::$dbh === null) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get the password of the user while also checking if it exists
        try {
            $credentials = Query::query('SELECT `uc__users`.`user_id`, `uc__users`.`username`, `uc__users`.`password`, `uc__users`.`strikes` FROM `uc__emails` LEFT JOIN `uc__users` on `uc__users`.`user_id`=`uc__emails`.`user_id` WHERE `uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail LIMIT 1',
                [':mail' => $_POST['signinup']['email']], return: 'row'
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if a password is set (means that a user does exist)
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
        if (!$this->setId($credentials['user_id'])->passValid($_POST['signinup']['password'], $credentials['password'])) {
            Security::log('Failed login', 'Bad password');
            return ['http_error' => 403, 'reason' => 'Wrong login or password'];
        }
        #Get permissions
        $_SESSION['permissions'] = $this->getPermissions();
        if (!in_array('can_login', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_login` permission'];
        }
        #Add username and user_id to the session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['user_id'] = $credentials['user_id'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            $this->rememberMe();
            Security::log('Login', 'Successful login with cookie setup', 'Cookie ID is '.($_SESSION['cookie_id'] ?? 'NULL'));
        } else {
            Security::log('Login', 'Successful login');
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            Security::session_regenerate_id(true);
        }
        if ($afterRegister) {
            return ['status' => 201, 'response' => true];
        }
        return ['response' => true];
    }
    
    /**
     * Setting cookie for remembering user
     * @param string $cookie_id
     *
     * @return void
     */
    public function rememberMe(string $cookie_id = ''): void
    {
        try {
            if ($this->system) {
                return;
            }
            #Generate cookie ID
            if (empty($cookie_id)) {
                $cookie_id = bin2hex(random_bytes(64));
            }
            #Generate cookie password
            $pass = bin2hex(random_bytes(128));
            $hashedPass = Security::passHash($pass);
            #Write cookie data to DB
            if (!empty($this->id) || (!empty($_SESSION['user_id']) && !in_array($_SESSION['user_id'], [Config::USER_IDS['Unknown user'], Config::USER_IDS['System user'], Config::USER_IDS['Deleted user']], true))) {
                #Check if a cookie exists and get its `validator`. This also helps with race conditions a bit
                $currentPass = Query::query('SELECT `validator` FROM `uc__cookies` WHERE `user_id`=:id AND `cookie_id`=:cookie',
                    [
                        ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                        ':cookie' => $cookie_id
                    ], return: 'value'
                );
                if (empty($currentPass)) {
                    $affected = Query::query('INSERT IGNORE INTO `uc__cookies` (`cookie_id`, `validator`, `user_id`) VALUES (:cookie, :pass, :id);',
                        [
                            ':cookie' => $cookie_id,
                            ':pass' => $hashedPass,
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                        ]
                    );
                } else {
                    $affected = Query::query('UPDATE `uc__cookies` SET `validator`=:pass, `time`=CURRENT_TIMESTAMP() WHERE `user_id`=:id AND `cookie_id`=:cookie AND `validator`=:validator;',
                        [
                            ':cookie' => $cookie_id,
                            ':pass' => $hashedPass,
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                            ':validator' => $currentPass,
                        ], return: 'affected'
                    );
                }
                #Update stuff only if we did insert a cookie or update the validator value
                if ($affected > 0) {
                    #Set cookie ID to session if it's not already linked or if it was linked to another cookie (not sure if that would even be possible)
                    if (empty($_SESSION['cookie_id']) || $_SESSION['cookie_id'] !== $cookie_id) {
                        $_SESSION['cookie_id'] = $cookie_id;
                    }
                    #Set cookie
                    $currentPass = Query::query('SELECT `validator` FROM `uc__cookies` WHERE `user_id`=:id AND `cookie_id`=:cookie',
                        [
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                            ':cookie' => $cookie_id
                        ], return: 'value'
                    );
                    #Another attempt to prevent race conditions
                    if ($currentPass === $hashedPass) {
                        setcookie('rememberme_'.Config::$http_host,
                            json_encode(['cookie_id' => Security::encrypt($cookie_id), 'pass' => Security::encrypt($pass)], JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION),
                            array_merge(Config::$cookie_settings, ['expires' => time() + 2592000]),
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
                if (password_needs_rehash($hash, PASSWORD_ARGON2ID, Config::$argon_settings)) {
                    #Rehash password and reset strikes (if any)
                    $this->passChange($password);
                } else {
                    #Reset strikes (if any)
                    $this->resetStrikes();
                }
                return true;
            }
            #Increase strike count
            Query::query(
                'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `user_id`=:user_id',
                [':user_id' => [$this->id, 'string']]);
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
        if ($this->system) {
            return false;
        }
        $result = Query::query(
            'UPDATE `uc__users` SET `password`=:password, `strikes`=0, `password_reset`=NULL WHERE `user_id`=:user_id;',
            [
                ':user_id' => [$this->id, 'string'],
                ':password' => [Security::passHash($password), 'string'],
            ]
        );
        if (session_status() === PHP_SESSION_ACTIVE) {
            Security::session_regenerate_id(true);
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
        if ($this->system) {
            return false;
        }
        return Query::query(
            'UPDATE `uc__users` SET `strikes`=0, `password_reset`=NULL WHERE `user_id`=:user_id;',
            [
                ':user_id' => [(string)$this->id, 'string']
            ]
        );
    }
    
    /**
     * Delete cookie
     *
     * @param bool $logout Flag indicating whether a cookie is being deleted during normal logout
     *
     * @return bool
     */
    public function deleteCookie(bool $logout = false): bool
    {
        if (empty($this->id) || empty($_POST['cookie'])) {
            return false;
        }
        if ($this->system) {
            return false;
        }
        $result = Query::query(
            'DELETE FROM `uc__cookies` WHERE `user_id`=:user_id AND `cookie_id`=:cookie;',
            [
                ':user_id' => [$this->id, 'int'],
                ':cookie' => $_POST['cookie'],
            ], return: 'affected'
        );
        if ($result > 0) {
            Security::log('Logout', $logout ? 'Cookie deleted during logout' : 'Manually deleted a cookie', 'Cookie ID deleted is '.$_POST['cookie']);
        }
        return true;
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
        if ($this->system) {
            return false;
        }
        $result = Query::query(
            'DELETE FROM `uc__sessions` WHERE `user_id`=:user_id AND `session_id`=:session;',
            [
                ':user_id' => [(string)$this->id, 'string'],
                ':session' => $_POST['session'],
            ], return: 'affected'
        );
        if ($result > 0) {
            Security::log('Logout', 'Manually deleted a session', 'Session ID deleted is '.$_POST['session']);
        }
        return true;
    }
    
    /**
     * Get threads created by a user
     * @return array
     */
    public function getThreads(): array
    {
        $where = '`talks__threads`.`author`=:user_id';
        $bindings = [':user_id' => [$this->id, 'int'],];
        if (!in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`author`=:author)';
            $bindings[':author'] = [$_SESSION['user_id'], 'int'];
        }
        $threads = new Threads($bindings, $where, '`talks__threads`.`created` DESC')->listEntities();
        #Clean any threads with empty `firstPost` (means the thread is either empty or is in progress of creation)
        foreach ($threads['entities'] as $key => $thread) {
            if (empty($thread['firstPost'])) {
                unset($threads['entities'][$key]);
            }
        }
        return $threads['entities'];
    }
    
    /**
     * Get posts created by the user
     * @return array
     */
    public function getPosts(): array
    {
        $where = '`talks__posts`.`author`=:author';
        $bindings = [':author' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int'],];
        if (!$this->id !== $_SESSION['user_id'] && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!$this->id !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`private`=0';
        }
        $posts = new Posts($bindings, $where, '`talks__posts`.`created` DESC')->listEntities();
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
        #Now we get post's details
        if (!empty($threads)) {
            #Keep only items with og_image
            if ($onlyWithBanner) {
                foreach ($threads as $key => $thread) {
                    if (empty($thread['og_image'])) {
                        unset($threads[$key]);
                    } else {
                        $thread['og_image'] = Images::ogImage($thread['og_image']);
                        if (empty($thread['og_image'])) {
                            unset($threads[$key]);
                        } else {
                            $threads[$key]['og_image'] = $thread['og_image'];
                        }
                    }
                }
                if (empty($threads)) {
                    return [];
                }
            }
            #Convert regular 0, 1, ... n IDs to real thread IDs for later use
            $threads = Editors::digitToKey($threads, 'id');
            #Get the posts' IDs
            $ids = array_column($threads, 'firstPost');
        } else {
            return [];
        }
        #Get posts
        $where = '';
        $bindings = [':user_id' => [$_SESSION['user_id'], 'int']];
        if (!in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()';
        }
        if (!in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`author`=:author)';
            $bindings[':author'] = [$_SESSION['user_id'], 'int'];
        }
        $bindings[':postIDs'] = [$ids, 'in', 'int'];
        $posts = new Posts($bindings, '`talks__posts`.`post_id` IN (:postIDs)'.$where, '`talks__posts`.`created` DESC')->listEntities();
        if (is_array($posts) && !empty($posts['entities'])) {
            #Get like value for each post if the current user has appropriate permission
            foreach ($posts['entities'] as &$post) {
                if (!empty($threads[$post['thread_id']]['og_image'])) {
                    $post['og_image'] = $threads[$post['thread_id']]['og_image'];
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
            array_merge(Config::$cookie_settings, ['expires' => time() - 3600])
        );
        #From DB
        if (!empty($_SESSION['cookie_id'])) {
            $_POST['cookie'] = $_SESSION['cookie_id'];
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
     * In case of errors, we return simple `false`. I think malicious actors may abuse different error messages here.
     * @param bool $hard
     *
     * @return bool
     */
    public function remove(bool $hard = false): bool
    {
        #Check if we are trying to remove one of the system users and prevent that
        if ($this->system) {
            return false;
        }
        try {
            #Close the session to avoid changing it in any way
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @session_write_close();
            #Check if hard removal or regular one was requested
            if ($hard) {
                #Hard removal means complete removal of the user entity, except for sections/threads/posts/files created, where we first update the user IDs
                #The rest will be dealt with through foreign key constraints
                $queries = [
                    [
                        'UPDATE `talks__sections` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__sections` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `last_poster`=:deleted WHERE `last_poster`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `talks__posts_history` SET `user_id`=:deleted WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'UPDATE `sys__files` SET `user_id`=:deleted WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [Config::USER_IDS['Deleted user'], 'int']]
                    ],
                    [
                        'DELETE FROM `talks__likes` WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int']]
                    ],
                    [
                        'DELETE FROM `uc__users` WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int']]
                    ],
                ];
            } else {
                #Soft removal only changes groups for the user
                $queries = [
                    [
                        'DELETE FROM `uc__user_to_group` WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int']]
                    ],
                    [
                        'INSERT INTO `uc__user_to_group` (`user_id`, `group_id`) VALUES (:user_id, :group_id);',
                        [
                            ':user_id' => [$this->id, 'int'],
                            ':group_id' => [Config::GROUP_IDS['Deleted'], 'int'],
                        ]
                    ],
                ];
            }
            #We also remove all cookies and sessions
            $queries[] = [
                'DELETE FROM `uc__cookies` WHERE `user_id`=:user_id;',
                [':user_id' => $this->id]
            ];
            $queries[] = [
                'DELETE FROM `uc__sessions` WHERE `user_id`=:user_id;',
                [':user_id' => $this->id]
            ];
            #If queries ran successfully - logout properly
            if (Query::query($queries)) {
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
        Security::log('User removal', 'Removal', ['user_id' => $this->id, 'hard' => $hard, 'result' => $result], ($hard ? Config::USER_IDS['Deleted user'] : $this->id));
        return $result;
    }
}