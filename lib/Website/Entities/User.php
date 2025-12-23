<?php
declare(strict_types = 1);

namespace Simbiat\Website\Entities;

use GeoIp2\Database\Reader;
use Simbiat\Arrays\Converters;
use Simbiat\Arrays\Editors;
use Simbiat\Database\Query;
use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Entities\Notifications\LoginFailed;
use Simbiat\Website\Entities\Notifications\LoginSuccess;
use Simbiat\Website\Entities\Notifications\PasswordChange;
use Simbiat\Website\Entities\Notifications\PasswordReset;
use Simbiat\Website\Entities\Notifications\UserLock;
use Simbiat\Website\Enums\LogTypes;
use Simbiat\Website\Enums\SystemUsers;
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
final class User extends Entity
{
    /**
     * Maximum number of unused avatars per user
     */
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
    #Whether the account is banned
    public bool $banned = false;
    #Emails
    public array $emails = [];
    #Avatars
    public array $avatars = [];
    #Current avatar
    public ?string $current_avatar = null;
    #Number of strikes
    public int $strikes = 0;
    
    /**
     * Function to get initial data from DB
     *
     * @return array
     */
    protected function getFromDB(): array
    {
        
        $db_data = Query::query('SELECT `username`, `system`, `strikes`, `phone`, `ff_token`, `registered`, `updated`, `parent_id`, (IF(`parent_id` IS NULL, NULL, (SELECT `username` FROM `uc__users` WHERE `user_id`=:user_id))) AS `parentname`, `birthday`, `first_name`, `last_name`, `middle_name`, `father_name`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website`, `blog`, `changelog`, `knowledgebase` FROM `uc__users` LEFT JOIN `uc__user_to_section` ON `uc__users`.`user_id`=`uc__user_to_section`.`user_id` WHERE `uc__users`.`user_id`=:user_id', ['user_id' => [$this->id, 'int']], return: 'row');
        if (empty($db_data)) {
            return [];
        }
        #Get user's groups
        $db_data['groups'] = Query::query('SELECT `group_id` FROM `uc__user_to_group` WHERE `user_id`=:user_id', ['user_id' => [$this->id, 'int']], return: 'column');
        if (in_array(5, $db_data['groups'], true)) {
            $this->banned = true;
        }
        #Get permissions
        $db_data['permissions'] = $this->getPermissions();
        if ($this->system) {
            #System users need to be treated as not activated
            $db_data['activated'] = false;
        } else {
            $db_data['activated'] = !in_array(Config::GROUP_IDS['Unverified'], $db_data['groups'], true);
        }
        $db_data['current_avatar'] = $this->getAvatar();
        return $db_data;
    }
    
    /**
     * Function process database data
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        #Populate names
        $this->name['first_name'] = $from_db['first_name'];
        $this->name['last_name'] = $from_db['last_name'];
        $this->name['middle_name'] = $from_db['middle_name'];
        $this->name['father_name'] = $from_db['father_name'];
        $this->name['prefix'] = $from_db['prefix'];
        $this->name['suffix'] = $from_db['suffix'];
        #Populate dates
        $this->dates['registered'] = $from_db['registered'];
        $this->dates['updated'] = $from_db['updated'];
        $this->dates['birthday'] = $from_db['birthday'];
        #Populate parent details
        $this->parent['id'] = $from_db['parent_id'];
        $this->parent['name'] = $from_db['parentname'];
        #Pupulate personal sections
        $this->sections = [
            'blog' => empty($from_db['blog']) ? null : $from_db['blog'],
            'changelog' => empty($from_db['changelog']) ? null : $from_db['changelog'],
            'knowledgebase' => empty($from_db['knowledgebase']) ? null : $from_db['knowledgebase'],
        ];
        $this->system = (bool)$from_db['system'];
        #Clean up the array
        unset($from_db['system'], $from_db['parent_id'], $from_db['parentname'], $from_db['first_name'], $from_db['last_name'], $from_db['middle_name'], $from_db['father_name'], $from_db['prefix'],
            $from_db['suffix'], $from_db['registered'], $from_db['updated'], $from_db['birthday'], $from_db['blog'], $from_db['changelog'], $from_db['knowledgebase']);
        #Populate the rest properties
        Converters::arrayToProperties($this, $from_db);
    }
    
    /**
     * Get user permissions
     *
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
                ) AS `temp` GROUP BY `permission`;
            ', ['user_id' => [$this->id, 'int']], return: 'column');
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Get user email addresses
     *
     * @return array
     */
    public function getEmails(): array
    {
        try {
            $result['emails'] = Query::query('SELECT `email`, `subscribed`, `activation` FROM `uc__emails` WHERE `user_id`=:user_id ORDER BY `email`;', [':user_id' => [$this->id, 'int']], return: 'all');
            #Count how many emails are activated (to restrict removal of emails)
            $result['count_activated'] = \count(\array_filter(\array_column($result['emails'], 'activation'), '\is_null'));
            $result['count_subscribed'] = \count(\array_filter(\array_column($result['emails'], 'subscribed'), static function ($x) {
                return $x !== null;
            }));
            $this->emails = $result;
            return $result;
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Get current avatar
     *
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
     *
     * @return array
     */
    public function getAvatars(): array
    {
        try {
            $result = Query::query('SELECT CONCAT(\'/assets/images/avatars/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) AS `url`, `uc__avatars`.`file_id`, `current` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`file_id`=`sys__files`.`file_id` WHERE `uc__avatars`.`user_id`=:user_id ORDER BY `current` DESC;', [':user_id' => [$this->id, 'int']], return: 'all');
            $this->avatars = $result;
            return $result;
        } catch (\Throwable) {
            return [];
        }
    }
    
    /**
     * Add avatar
     *
     * @param bool     $set_active Make avatar active right away
     * @param string   $link       Link to avatar
     * @param int|null $character  FFXIV character, if avatar is from one
     *
     * @return array
     */
    public function addAvatar(bool $set_active = false, string $link = '', ?int $character = null): array
    {
        try {
            if ($this->system) {
                return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
            }
            #Get current avatars
            $avatars = $this->getAvatars();
            #Count values in `current` column
            $counts = \array_count_values(\array_column($avatars, 'current'));
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
            Security::log(LogTypes::Avatar->value, 'Added avatar', $upload['hash']);
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
            if ($set_active) {
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
     *
     * @return array
     */
    public function delAvatar(): array
    {
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        $file_id = $_POST['avatar'] ?? '';
        #Log the change
        Security::log(LogTypes::Avatar->value, 'Deleted avatar', $file_id);
        #Delete the avatar (only allow deletion of those that are not current)
        Query::query('DELETE FROM `uc__avatars` WHERE `user_id`=:user_id AND `file_id`=:file_id AND `current`=0;', [':user_id' => [$this->id, 'int'], ':file_id' => $file_id]);
        return ['location' => $this->getAvatar(), 'response' => true];
    }
    
    /**
     * Set current avatar
     *
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
        Security::log(LogTypes::Avatar->value, 'Changed active avatar', $file_id);
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
     *
     * @return array
     */
    public function getFF(): array
    {
        $output_array = [];
        #Get token
        $output_array['token'] = Query::query('SELECT `ff_token` FROM `uc__users` WHERE `user_id`=:user_id;', [':user_id' => [$this->id, 'int']], return: 'value');
        #Get linked characters
        $output_array['characters'] = Query::query('SELECT \'character\' AS `type`, `ffxiv__character`.`character_id` AS `id`, `name`, `avatar` AS `icon` FROM `ffxiv__character` LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id` WHERE `user_id`=:user_id ORDER BY `name`;', [':user_id' => [$this->id, 'int']], return: 'all');
        #Get linked groups
        if (!empty($output_array['characters'])) {
            foreach ($output_array['characters'] as $character) {
                $output_array['groups'][$character['id']] = AbstractTrackerEntity::cleanCrestResults(Query::query(
                /** @lang SQL */ '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`fc_id` AS `id`, `ffxiv__freecompany`.`name` AS `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `gc_id` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`fc_id`=`ffxiv__freecompany`.`fc_id` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`fc_id`=`ffxiv__freecompany`.`fc_id` AND `ffxiv__freecompany_character`.`rank_id`=`ffxiv__freecompany_rank`.`rank_id` WHERE `character_id`=:id AND `ffxiv__freecompany_character`.`current`=1 AND `ffxiv__freecompany_character`.`rank_id`=0)
                UNION ALL
                (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`ls_id` AS `id`, `ffxiv__linkshell`.`name` AS `name`, NULL AS `crest_part_1`, NULL AS `crest_part_2`, NULL AS `crest_part_3`, NULL AS `gc_id` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`ls_id`=`ffxiv__linkshell`.`ls_id` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rank_id`=`ffxiv__linkshell_rank`.`ls_rank_id` WHERE `character_id`=:id AND `ffxiv__linkshell_character`.`current`=1 AND `ffxiv__linkshell_character`.`rank_id`=1)
                UNION ALL
                (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvp_id` AS `id`, `ffxiv__pvpteam`.`name` AS `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, NULL AS `gc_id` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvp_id`=`ffxiv__pvpteam`.`pvp_id` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rank_id`=`ffxiv__pvpteam_rank`.`pvp_rank_id` WHERE `character_id`=:id AND `ffxiv__pvpteam_character`.`current`=1 AND `ffxiv__pvpteam_character`.`rank_id`=1)
                ORDER BY `name`;',
                    [':id' => [$character['id'], 'int']], return: 'all'
                ));
            }
        }
        return $output_array;
    }
    
    /**
     * Change username
     *
     * @param string $new_name
     *
     * @return array|true[]
     */
    public function changeUsername(string $new_name): array
    {
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t modify system user'];
        }
        $sanitized_name = Sanitization::removeNonPrintable($new_name, true);
        if (!is_string($sanitized_name)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        $new_name = $sanitized_name;
        #Check if the new name is valid
        if (empty($new_name) || $this->bannedName($new_name) || $this->usedName($new_name)) {
            return ['http_error' => 403, 'reason' => 'Prohibited username provided'];
        }
        #Check if we have the current username and get it if we do not
        if (empty($this->username)) {
            $this->get();
        }
        if ($this->username === $new_name) {
            return ['response' => true];
        }
        try {
            $result = Query::query('UPDATE `uc__users` SET `username`=:username WHERE `user_id`=:user_id;', [
                ':user_id' => [$this->id, 'int'],
                ':username' => $new_name,
            ]);
            if ($result) {
                $_SESSION['username'] = $new_name;
            }
            if (\session_status() === \PHP_SESSION_ACTIVE) {
                Security::session_regenerate_id(true);
            }
            #Log the change
            Security::log(LogTypes::UserDetailsChanged->value, 'Changed name', ['name' => ['old' => $this->username, 'new' => $new_name]]);
            return ['response' => $result];
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to change the username'];
        }
    }
    
    /**
     * Update user profile data
     *
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
                            (empty($_POST['details']['name'][$field]) ? null : $_POST['details']['name'][$field]),
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
                        (empty($_POST['details']['dates']['birthday']) ? null : $_POST['details']['dates']['birthday']),
                        (empty($_POST['details']['dates']['birthday']) ? 'null' : 'date'),
                    ],
                ]
            ];
        }
        #Query for time zone
        $_POST['details']['timezone'] = Sanitization::removeNonPrintable($_POST['details']['timezone'] ?? 'UTC', true);
        if ($this->timezone !== $_POST['details']['timezone'] && in_array($_POST['details']['timezone'], \timezone_identifiers_list(), true)) {
            $log['timezone'] = ['old' => $this->timezone, 'new' => $_POST['details']['timezone']];
            $queries[] = [
                'UPDATE `uc__users` SET `timezone`=:timezone WHERE `user_id`=:user_id;',
                [
                    ':user_id' => [$this->id, 'int'],
                    ':timezone' => [
                        (empty($_POST['details']['timezone']) ? null : $_POST['details']['timezone']),
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
                            ($_POST['details']['sex'] === null ? 'null' : 'int'),
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
                            (empty($_POST['details']['timezone']) ? null : $_POST['details']['timezone']),
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
                            (empty($_POST['details'][$field]) ? null : $_POST['details'][$field]),
                            (empty($_POST['details'][$field]) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        if (\count($queries) === 0) {
            return ['http_error' => 400, 'reason' => 'No changes detected'];
        }
        $result = Query::query($queries);
        #Log the change
        Security::log(LogTypes::UserDetailsChanged->value, 'Changed details', $log);
        return ['response' => $result];
    }
    
    /**
     * Function to check if a name is already used
     *
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
     *
     * @param string $name
     *
     * @return bool
     */
    public function bannedName(string $name): bool
    {
        #Check the format
        if (\preg_match('/^[\p{L}\d.!$%&\'*+\/=?_`{|}~\- ^]{1,64}$/ui', $name) !== 1) {
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
     *
     * @param bool $after_registration Flag indicating if login is being done after initial registration
     *
     * @return array|true[]
     */
    public function login(bool $after_registration = false): array
    {
        #Check if already logged in and return early
        if ($_SESSION['user_id'] !== 1) {
            if ($after_registration) {
                return ['status' => 201, 'response' => true];
            }
            return ['response' => true];
        }
        #Validating data
        if (empty($_POST['signinup']['email'])) {
            Security::log(LogTypes::FailedLogin->value, 'No email provided');
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        if (empty($_POST['signinup']['password'])) {
            Security::log(LogTypes::FailedLogin->value, 'No password provided');
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        #Check if banned
        $is_email = \filter_var($_POST['signinup']['email'], \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE);
        if (!$is_email && $this->bannedName($_POST['signinup']['email'])) {
            Security::log(LogTypes::FailedLogin->value, 'Prohibited credentials provided: `'.$_POST['signinup']['email'].'`');
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (Query::$dbh === null) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get the password of the user while also checking if it exists
        try {
            $credentials = Query::query('SELECT `uc__users`.`user_id`, `uc__users`.`username`, `uc__users`.`password`, `uc__users`.`strikes` FROM `uc__emails` LEFT JOIN `uc__users` ON `uc__users`.`user_id`=`uc__emails`.`user_id` WHERE `uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail LIMIT 1',
                [':mail' => $_POST['signinup']['email']], return: 'row'
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if a password is set (means that a user does exist)
        if (empty($credentials['password'])) {
            Security::log(LogTypes::FailedLogin->value, 'No user found');
            return ['http_error' => 403, 'reason' => 'Wrong login or password'];
        }
        /** @noinspection UnusedFunctionResultInspection Needed to just update current ID */
        $this->setId($credentials['user_id']);
        #Get permissions
        $_SESSION['permissions'] = $this->getPermissions();
        if (!in_array('can_login', $_SESSION['permissions'], true)) {
            Security::log(LogTypes::FailedLogin->value, 'Attempt to login with account that can\'t login', user_id: (int)$this->id);
            return ['http_error' => 403, 'reason' => 'No `can_login` permission'];
        }
        #Check for strikes
        if ($credentials['strikes'] >= 5) {
            Security::log(LogTypes::FailedLogin->value, 'Too many failed login attempts', user_id: (int)$this->id);
            return ['http_error' => 403, 'reason' => 'Too many failed login attempts. Try password reset.'];
        }
        #Check the password
        if (!$this->passValid($_POST['signinup']['password'], $credentials['password'])) {
            Security::log(LogTypes::FailedLogin->value, 'Bad password', user_id: (int)$this->id);
            return ['http_error' => 403, 'reason' => 'Wrong login or password'];
        }
        #Add username and user_id to the session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['user_id'] = $credentials['user_id'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            $this->rememberMe();
            Security::log(LogTypes::Login->value, 'Successful login with cookie setup', 'Cookie ID is '.($_SESSION['cookie_id'] ?? 'NULL'), (int)$this->id);
        } else {
            Security::log(LogTypes::Login->value, 'Successful login', user_id: (int)$this->id);
        }
        if (\session_status() === \PHP_SESSION_ACTIVE) {
            Security::session_regenerate_id(true);
        }
        if ($after_registration) {
            return ['status' => 201, 'response' => true];
        }
        new LoginSuccess()->setEmail(true)->setPush(true)->setUser($this->id)->generate()->save()->send();
        return ['response' => true];
    }
    
    /**
     * Initiate password reset through email
     *
     * @return array
     */
    public function remind(): array
    {
        if (empty($_POST['signinup']['email'])) {
            Security::log(LogTypes::PasswordReset->value, 'No email/name provided');
            return ['http_error' => 400, 'reason' => 'No email/name provided'];
        }
        #Check DB
        if (Query::$dbh === null) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get the password of the user while also checking if it exists
        try {
            $credentials = Query::query('SELECT `uc__users`.`user_id`, `uc__users`.`username`, `uc__emails`.`email` FROM `uc__emails` LEFT JOIN `uc__users` ON `uc__users`.`user_id`=`uc__emails`.`user_id` WHERE (`uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail) AND `uc__emails`.`activation` IS NULL AND `uc__users`.`system`=0  LIMIT 1',
                [':mail' => $_POST['signinup']['email']], return: 'row'
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Process only if a user was found
        if ($credentials !== null && $credentials !== []) {
            /** @noinspection UnusedFunctionResultInspection Needed to just update current ID */
            $this->setId($credentials['user_id']);
            #Get permissions
            $_SESSION['permissions'] = $this->getPermissions();
            if (!in_array('can_login', $_SESSION['permissions'], true)) {
                Security::log(LogTypes::PasswordReset->value, 'Attempt to reset password for account that can\'t login', user_id: (int)$this->id);
                #Return "true" to prevent spoofing registered emails
                return ['response' => true];
            }
            $token = Security::genToken();
            try {
                #Write the reset token to DB
                Query::query('UPDATE `uc__users` SET `password_reset`=:token WHERE `user_id`=:user_id', [':user_id' => $credentials['user_id'], ':token' => Security::passHash($token)]);
                Security::log(LogTypes::PasswordReset->value, 'Attempt to reset password for account', user_id: (int)$this->id);
                new PasswordReset()->setEmail(true)->setPush(false)->setUser($this->id)->generate(['token' => $token, 'user_id' => $credentials['user_id']])->save()->send($credentials['email'], true);
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Password reset failed'];
            }
        }
        return ['response' => true];
    }
    
    /**
     * Setting cookie for remembering user
     *
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
            if ($cookie_id === '') {
                $cookie_id = \bin2hex(\random_bytes(64));
            }
            #Generate cookie password
            $pass = \bin2hex(\random_bytes(128));
            $hashed_pass = Security::passHash($pass);
            #Write cookie data to DB
            if (!($this->id === null || $this->id === '') || (!empty($_SESSION['user_id']) && !in_array($_SESSION['user_id'], [SystemUsers::Unknown->value, SystemUsers::System->value, SystemUsers::Deleted->value], true))) {
                #Check if a cookie exists and get its `validator`. This also helps with race conditions a bit
                $current_pass = Query::query('SELECT `validator` FROM `uc__cookies` WHERE `user_id`=:id AND `cookie_id`=:cookie',
                    [
                        ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                        ':cookie' => $cookie_id
                    ], return: 'value'
                );
                if (empty($current_pass)) {
                    $affected = Query::query('INSERT IGNORE INTO `uc__cookies` (`cookie_id`, `validator`, `user_id`) VALUES (:cookie, :pass, :id);',
                        [
                            ':cookie' => $cookie_id,
                            ':pass' => $hashed_pass,
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                        ]
                    );
                } else {
                    $affected = Query::query('UPDATE `uc__cookies` SET `validator`=:pass, `time`=CURRENT_TIMESTAMP(6) WHERE `user_id`=:id AND `cookie_id`=:cookie AND `validator`=:validator;',
                        [
                            ':cookie' => $cookie_id,
                            ':pass' => $hashed_pass,
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                            ':validator' => $current_pass,
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
                    $current_pass = Query::query('SELECT `validator` FROM `uc__cookies` WHERE `user_id`=:id AND `cookie_id`=:cookie',
                        [
                            ':id' => [$this->id ?? $_SESSION['user_id'], 'int'],
                            ':cookie' => $cookie_id
                        ], return: 'value'
                    );
                    #Another attempt to prevent race conditions
                    if ($current_pass === $hashed_pass) {
                        /** @noinspection SecureCookiesTransferInspection Necessary parameters are provided through the array */
                        setcookie('rememberme_'.Config::$http_host,
                            \json_encode(['cookie_id' => Security::encrypt($cookie_id), 'pass' => Security::encrypt($pass)], \JSON_THROW_ON_ERROR | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION),
                            \array_merge(Config::$cookie_settings, ['expires' => \time() + 2592000]),
                        );
                    }
                }
            }
            return;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            #Do nothing, since not critical
        }
    }
    
    /**
     * Function to validate password
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function passValid(#[\SensitiveParameter] string $password, #[\SensitiveParameter] string $hash): bool
    {
        if ($this->id === null || $this->id === '') {
            return false;
        }
        #Validate password
        try {
            if (\password_verify($password, $hash)) {
                #Check if it needs rehashing
                if (\password_needs_rehash($hash, \PASSWORD_ARGON2ID, Config::$argon_settings)) {
                    #Rehash password and reset strikes (if any)
                    $this->passChange($password);
                } else {
                    #Reset strikes (if any)
                    $this->resetStrikes();
                }
                return true;
            }
            #Increase strike count
            $this->strikes++;
            Query::query(
                'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `user_id`=:user_id',
                [':user_id' => [$this->id, 'string']]);
            Security::log(LogTypes::FailedLogin->value, 'Strike added');
            if ($this->strikes === 5) {
                new UserLock()->setEmail(true)->setPush(true)->setUser($this->id)->generate()->save()->send(force: true);
            } elseif ($this->strikes < 5) {
                new LoginFailed()->setEmail(true)->setPush(true)->setUser($this->id)->generate()->save()->send(force: true);
            }
            return false;
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Function to change the password
     *
     * @param string $password
     *
     * @return bool
     */
    public function passChange(#[\SensitiveParameter] string $password): bool
    {
        if ($this->id === null || $this->id === '') {
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
        if (\session_status() === \PHP_SESSION_ACTIVE) {
            Security::session_regenerate_id(true);
        }
        Security::log(LogTypes::PasswordChange->value, 'Attempted to change password', $result);
        new PasswordChange()->setEmail(true)->setPush(true)->setUser($this->id)->generate()->save()->send(force: true);
        return $result;
    }
    
    /**
     * Reset number of failed logins
     *
     * @return bool
     */
    public function resetStrikes(): bool
    {
        if ($this->id === null || $this->id === '') {
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
        if ($this->id === null || $this->id === '' || empty($_POST['cookie'])) {
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
            Security::log(LogTypes::Logout->value, $logout ? 'Cookie deleted during logout' : 'Manually deleted a cookie', 'Cookie ID deleted is '.$_POST['cookie']);
        }
        return true;
    }
    
    /**
     * Delete session
     *
     * @return bool
     */
    public function deleteSession(): bool
    {
        if ($this->id === null || $this->id === '' || empty($_POST['session'])) {
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
            Security::log(LogTypes::Logout->value, 'Manually deleted a session', 'Session ID deleted is '.$_POST['session']);
        }
        return true;
    }
    
    /**
     * Get threads created by a user
     *
     * @return array
     */
    public function getThreads(): array
    {
        $where = '`talks__threads`.`author`=:user_id';
        $bindings = [':user_id' => [$this->id, 'int'],];
        if (!in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP(6)';
        }
        if (!in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`author`=:author)';
            $bindings[':author'] = [$_SESSION['user_id'], 'int'];
        }
        $threads = new Threads($bindings, $where, '`talks__threads`.`created` DESC')->listEntities();
        #Clean any threads with empty `first_post` (means the thread is either empty or is in progress of creation)
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        if (is_array($threads) && is_array($threads['entities'])) {
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            foreach ($threads['entities'] as $key => $thread) {
                if (empty($thread['first_post'])) {
                    /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
                    unset($threads['entities'][$key]);
                }
            }
        } else {
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            $threads['entities'] = [];
        }
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        return $threads['entities'];
    }
    
    /**
     * Get posts created by the user
     *
     * @return array
     */
    public function getPosts(): array
    {
        $where = '`talks__posts`.`author`=:author';
        $bindings = [':author' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int'],];
        if (!$this->id !== $_SESSION['user_id'] && !in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP(6)';
        }
        if (!$this->id !== $_SESSION['user_id'] && !in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__threads`.`private`=0';
        }
        $posts = new Posts($bindings, $where, '`talks__posts`.`created` DESC')->listEntities();
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        if (!is_array($posts) || !is_array($posts['entities'])) {
            return [];
        }
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        return $posts['entities'];
    }
    
    /**
     * Similar to getPosts(), but only gets posts, that are the first posts in threads
     *
     * @param bool $only_with_banner
     *
     * @return array
     */
    public function getTalksStarters(bool $only_with_banner = false): array
    {
        #Can't think of a good way to get this in 1 query, thus first getting the latest threads
        $threads = $this->getThreads();
        #Now we get post's details
        if (\count($threads) !== 0) {
            #Keep only items with og_image
            if ($only_with_banner) {
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
                if (\count($threads) === 0) {
                    return [];
                }
            }
            #Convert regular 0, 1, ... n IDs to real thread IDs for later use
            $threads = Editors::digitToKey($threads, 'id');
            #Get the posts' IDs
            $ids = \array_column($threads, 'first_post');
        } else {
            return [];
        }
        #Get posts
        $where = '';
        $bindings = [':user_id' => [$_SESSION['user_id'], 'int']];
        if (!in_array('view_scheduled', $_SESSION['permissions'], true)) {
            $where .= ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP(6)';
        }
        if (!in_array('view_private', $_SESSION['permissions'], true)) {
            $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`author`=:author)';
            $bindings[':author'] = [$_SESSION['user_id'], 'int'];
        }
        $bindings[':postIDs'] = [$ids, 'in', 'int'];
        $posts = new Posts($bindings, '`talks__posts`.`post_id` IN (:postIDs)'.$where, '`talks__posts`.`created` DESC')->listEntities();
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        if (is_array($posts) && is_array($posts['entities'])) {
            #Get like value for each post if the current user has appropriate permission
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            foreach ($posts['entities'] as &$post) {
                if (!empty($threads[$post['thread_id']]['og_image'])) {
                    $post['og_image'] = $threads[$post['thread_id']]['og_image'];
                }
            }
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            return $posts['entities'];
        }
        return [];
    }
    
    /**
     * Function to log the user out
     *
     * @return bool
     */
    public function logout(): bool
    {
        Security::log(LogTypes::Logout->value, 'Logout');
        #Remove rememberme cookie
        #From browser
        /** @noinspection SecureCookiesTransferInspection Necessary parameters are provided through the array */
        setcookie('rememberme_'.Config::$http_host, '',
            \array_merge(Config::$cookie_settings, ['expires' => \time() - 3600])
        );
        #From DB
        if (!empty($_SESSION['cookie_id'])) {
            $_POST['cookie'] = $_SESSION['cookie_id'];
            $this->deleteCookie(true);
        }
        #Clean session (affects $_SESSION only)
        \session_unset();
        #Destroy session (destroys it storage)
        $result = \session_destroy();
        if (!\headers_sent()) {
            \header('Clear-Site-Data: "*"');
        }
        return $result;
    }
    
    /**
     * @return array
     */
    public function register(): array
    {
        #Validating data
        if (empty($_POST['signinup']['username'])) {
            return ['http_error' => 400, 'reason' => 'No username provided'];
        }
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        $email = (new Email($_POST['signinup']['email']));
        if (empty($_POST['signinup']['password'])) {
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        if (mb_strlen($_POST['signinup']['password'], 'UTF-8') < 8) {
            return ['http_error' => 400, 'reason' => 'Password is shorter than 8 symbols'];
        }
        #Get time zone
        $timezone = $_POST['signinup']['timezone'] ?? 'UTC';
        if (!in_array($timezone, \timezone_identifiers_list(), true)) {
            $timezone = 'UTC';
        }
        #Check if banned or in use
        if (
            $email->isBad() ||
            $this->bannedName($_POST['signinup']['username']) ||
            $this->usedName($_POST['signinup']['username'])
        ) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (Query::$dbh === null) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Check if registration is enabled
        if (!Query::query('SELECT `value` FROM `sys__settings` WHERE `setting`=\'registration\'', return: 'value')) {
            return ['http_error' => 503, 'reason' => 'Registration is currently disabled'];
        }
        #Generate password and activation strings
        $password = Security::passHash($_POST['signinup']['password']);
        $ff_token = Security::genToken();
        #Try to read country and city for IP
        try {
            $geoip = new Reader(Config::$geoip.'GeoLite2-City.mmdb')->city($_SESSION['ip']);
        } catch (\Throwable) {
            #Do nothing, not critical
        }
        $email_status = $email->add();
        if (!\array_key_exists('status', $email_status) || $email_status['status'] !== 201) {
            return $email_status;
        }
        try {
            $queries = [
                #Insert to the main database
                [
                    'INSERT INTO `uc__users`(`username`, `password`, `ff_token`, `timezone`, `country`, `city`) VALUES (:username, :password, :ff_token, :timezone, :country, :city)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':password' => $password,
                        ':ff_token' => $ff_token,
                        ':timezone' => $timezone,
                        ':country' => $geoip->country->name ?? '',
                        ':city' => $geoip->city->name ?? '',
                        ':ip' => $_SESSION['ip'] ?? '',
                    ],
                ],
                #Update the user ID
                [
                    'UPDATE `uc__emails` SET `user_id`=(SELECT `user_id` FROM `uc__users` WHERE `username`=:username) WHERE `email`=:mail',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':mail' => $_POST['signinup']['email'],
                    ]
                ],
                #Insert into the group table
                [
                    'INSERT INTO `uc__user_to_group` (`user_id`, `group_id`) VALUES ((SELECT `user_id` FROM `uc__users` WHERE `username`=:username), :group_id)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':group_id' => [Config::GROUP_IDS['Unverified'], 'int'],
                    ]
                ],
            ];
            Query::query($queries);
            $email->subscribe();
            $email->confirm();
            return $this->login(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Registration failed'];
        }
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
            @\session_write_close();
            #Check if hard removal or regular one was requested
            if ($hard) {
                #Hard removal means complete removal of the user entity, except for sections/threads/posts/files created, where we first update the user IDs
                #The rest will be dealt with through foreign key constraints
                $queries = [
                    [
                        'UPDATE `talks__sections` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__sections` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__threads` SET `last_poster`=:deleted WHERE `last_poster`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `author`=:deleted WHERE `author`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__posts` SET `editor`=:deleted WHERE `editor`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `talks__posts_history` SET `user_id`=:deleted WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'UPDATE `sys__files` SET `user_id`=:deleted WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int'], ':deleted' => [SystemUsers::Deleted->value, 'int']]
                    ],
                    [
                        'DELETE FROM `talks__likes` WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int']]
                    ],
                    [
                        'DELETE FROM `uc__avatars` WHERE `user_id`=:user_id;',
                        [':user_id' => [$this->id, 'int']]
                    ],
                    [
                        'DELETE FROM `uc__emails` WHERE `user_id`=:user_id;',
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
        Security::log(LogTypes::UserRemoval->value, 'Removal', ['user_id' => $this->id, 'hard' => $hard, 'result' => $result], ($hard ? SystemUsers::Deleted->value : $this->id));
        return $result;
    }
}