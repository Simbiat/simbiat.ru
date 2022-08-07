<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Abstracts\Entity;
use Simbiat\HomePage;
use Simbiat\Security;

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
    #Whether account is activated
    public bool $activated = false;
    #Whether account is marked as deleted
    public bool $deleted = false;
    #Whether account is banned
    public bool $banned = false;
    #Emails
    public array $emails = [];
    #Avatars
    public array $avatars = [];
    #Current avatar
    public ?string $currentAvatar = null;

    protected function getFromDB(): array
    {
        $dbData =  $this->dbController->selectRow('SELECT `username`, `phone`, `ff_token`, `registered`, `updated`, `parentid`, (IF(`parentid` IS NULL, NULL, (SELECT `username` FROM `uc__users` WHERE `userid`=:userid))) as `parentname`, `birthday`, `firstname`, `lastname`, `middlename`, `fathername`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website` FROM `uc__users` WHERE `userid`=:userid', ['userid'=>[$this->id, 'int']]);
        if (empty($dbData)) {
            return [];
        }
        #Get user's groups
        $dbData['groups'] = $this->dbController->selectColumn('SELECT `groupid` FROM `uc__user_to_group` WHERE `userid`=:userid', ['userid'=>[$this->id, 'int']]);
        if (in_array(2, $dbData['groups'], true)) {
            $dbData['activated'] = false;
        } else {
            $dbData['activated'] = true;
        }
        if (in_array(4, $dbData['groups'], true)) {
            $dbData['deleted'] = false;
        } else {
            $dbData['deleted'] = true;
        }
        if (in_array(5, $dbData['groups'], true)) {
            $dbData['banned'] = false;
        } else {
            $dbData['banned'] = true;
        }
        $dbData['currentAvatar'] = $this->dbController->selectValue('SELECT `url` FROM `uc__user_to_avatar` WHERE `userid`=:userid AND `current`=1 LIMIT 1', ['userid'=>[$this->id, 'int']]);
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

    public function getEmails(): array
    {
        try {
            return $this->dbController->selectAll('SELECT `email`, `subscribed`, `activation` FROM `uc__user_to_email` WHERE `userid`=:userid ORDER BY `email`;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }

    public function getAvatars(): array
    {
        try {
            return $this->dbController->selectAll('SELECT `url`, `current` FROM `uc__user_to_avatar` WHERE `userid`=:userid;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }

    public function getFF(): array
    {
        $outputArray = [];
        #Get token
        $outputArray['token'] = $this->dbController->selectValue('SELECT `ff_token` FROM `uc__users` WHERE `userid`=:userid;', [':userid' => $_SESSION['userid']]);
        #Get linked characters
        $outputArray['characters'] = $this->dbController->selectAll('SELECT `characterid`, `name`, `avatar` FROM `ffxiv__character` WHERE `userid`=:userid ORDER BY `name`;', [':userid' => $_SESSION['userid']]);
        #Get linked groups
        if (!empty($outputArray['characters'])) {
            foreach ($outputArray['characters'] as $character) {
                $outputArray['groups'][$character['characterid']] = $this->dbController->selectAll(
                    '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, COALESCE(`ffxiv__freecompany`.`crest`, `ffxiv__freecompany`.`grandcompanyid`) AS `icon` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__freecompany_character`.`rankid`=`ffxiv__freecompany_rank`.`rankid` WHERE `characterid`=:id AND `ffxiv__freecompany_character`.`current`=1 AND `ffxiv__freecompany_character`.`rankid`=0)
                UNION ALL
                (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, NULL AS `icon` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rankid`=`ffxiv__linkshell_rank`.`lsrankid` WHERE `characterid`=:id AND `ffxiv__linkshell_character`.`current`=1 AND `ffxiv__linkshell_character`.`rankid`=1)
                UNION ALL
                (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, `ffxiv__pvpteam`.`crest` AS `icon` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rankid`=`ffxiv__pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `ffxiv__pvpteam_character`.`current`=1 AND `ffxiv__pvpteam_character`.`rankid`=1)
                ORDER BY `name` ASC;',
                    [':id' => $character['characterid']]
                );
            }
        }
        return $outputArray;
    }

    public function changeUsername(string $newName): array
    {
        #Check if new name is valid
        if (empty($newName) && Checkers::bannedName($newName) || Checkers::usedName($newName)) {
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
            $result = $this->dbController->query('UPDATE `uc__users` SET `username`=:username WHERE `userid`=:userid;', [
                ':userid' => [$this->id, 'int'],
                ':username' => $newName,
            ]);
            if ($result) {
                $_SESSION['username'] = $newName;
            }
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
        #Generate queries
        $queries = [];
        #Queries for names
        foreach (['firstname', 'lastname', 'middlename', 'fathername', 'prefix', 'suffix'] as $field) {
            if (isset($_POST['details']['name'][$field]) && $this->name[$field] !== $_POST['details']['name'][$field]) {
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
            return ['response' => $this->dbController->query($queries)];
        }
    }

    public function login(bool $afterRegister = false): array
    {
        #Validating data
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        if (empty($_POST['signinup']['password'])) {
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        #Check if banned
        if (Checkers::bannedIP() ||
            Checkers::bannedMail($_POST['signinup']['email']) ||
            Checkers::bannedName($_POST['signinup']['email'])
        ) {
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get password of the user, while also checking if it exists
        try {
            $credentials = HomePage::$dbController->selectRow('SELECT `uc__users`.`userid`, `username`, `password`, `strikes` FROM `uc__user_to_email` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__user_to_email`.`userid` WHERE `uc__user_to_email`.`email`=:mail',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if password is set (means that user does exist)
        if (empty($credentials['password'])) {
            return ['http_error' => 403, 'reason' => 'No user found'];
        }
        #Check for strikes
        if ($credentials['strikes'] >= 5) {
            return ['http_error' => 403, 'reason' => 'Too many failed login attempts. Try password reset.'];
        }
        #Check the password
        if (Security::passValid($credentials['userid'], $_POST['signinup']['password'], $credentials['password']) === false) {
            return ['http_error' => 403, 'reason' => 'Bad password'];
        }
        #Add username and userid to session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['userid'] = $credentials['userid'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            Security::rememberMe();
        }
        session_regenerate_id(true);
        if ($afterRegister) {
            return ['status' => 201, 'response' => true];
        } else {
            return ['response' => true];
        }
    }
}
