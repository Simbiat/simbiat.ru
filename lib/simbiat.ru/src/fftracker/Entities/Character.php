<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Errors;
use Simbiat\fftracker\Entity;
use Simbiat\HomePage;
use Simbiat\Lodestone;
use Simbiat\Security;
use Simbiat\usercontrol\User;

class Character extends Entity
{
    #Custom properties
    public ?string $avatarID = '';
    public array $dates = [];
    public array $biology = [];
    public array $location = [];
    public ?string $biography = null;
    public array $title = [];
    public array $grandCompany = [];
    public int $pvp = 0;
    public array $groups = [];
    public array $jobs = [];
    public array $achievements = [];
    public array $owned = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information. Using *, but add name, because otherwise Achievement name overrides Character name, and we do not want that
        $data = HomePage::$dbController->selectRow('SELECT *, `ffxiv__achievement`.`icon` AS `titleIcon`, `ffxiv__character`.`name`, `ffxiv__character`.`registered`, `ffxiv__character`.`updated`, `ffxiv__enemy`.`name` AS `killedby` FROM `ffxiv__character` LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid` = `ffxiv__clan`.`clanid` LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid` = `ffxiv__guardian`.`guardianid` LEFT JOIN `ffxiv__nameday` ON `ffxiv__character`.`namedayid` = `ffxiv__nameday`.`namedayid` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid` = `ffxiv__city`.`cityid` LEFT JOIN `ffxiv__server` ON `ffxiv__character`.`serverid` = `ffxiv__server`.`serverid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid` = `ffxiv__grandcompany_rank`.`gcrankid` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gcId` = `ffxiv__grandcompany`.`gcId` LEFT JOIN `ffxiv__achievement` ON `ffxiv__character`.`titleid` = `ffxiv__achievement`.`achievementid` LEFT JOIN `ffxiv__enemy` ON `ffxiv__character`.`enemyid` = `ffxiv__enemy`.`enemyid` WHERE `ffxiv__character`.`characterid` = :id;', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }
        #Get username, if character is linked to a user
        if (!empty($data['userid'])) {
            $data['username'] = HomePage::$dbController->selectValue('SELECT `username` FROM `uc__users` WHERE `userid`=:userid;', [':userid' => $data['userid']]);
        } else {
            $data['username'] = null;
        }
        #Get old names. For now returning only the count due to cases of bullying, when the old names are learnt. They are still being collected, though for statistical purposes.
        $data['oldNames'] = HomePage::$dbController->Count('SELECT COUNT(*) FROM `ffxiv__character_names` WHERE `characterid`=:id AND `name`!=:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get previous known incarnations (combination of gender and race/clan)
        $data['incarnations'] = HomePage::$dbController->selectAll('SELECT `genderid`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan` FROM `ffxiv__character_clans` LEFT JOIN `ffxiv__clan` ON `ffxiv__character_clans`.`clanid` = `ffxiv__clan`.`clanid` WHERE `ffxiv__character_clans`.`characterid`=:id AND (`ffxiv__character_clans`.`clanid`!=:clanid AND `ffxiv__character_clans`.`genderid`!=:genderid) ORDER BY `genderid` , `race` , `clan` ', [':id'=>$this->id, ':clanid'=>$data['clanid'], ':genderid'=>$data['genderid']]);
        #Get old servers
        $data['servers'] = HomePage::$dbController->selectAll('SELECT `ffxiv__server`.`datacenter`, `ffxiv__server`.`server` FROM `ffxiv__character_servers` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`ffxiv__character_servers`.`serverid` WHERE `ffxiv__character_servers`.`characterid`=:id AND `ffxiv__character_servers`.`serverid` != :serverid ORDER BY `datacenter` , `server` ', [':id'=>$this->id, ':serverid'=>$data['serverid']]);
        #Get achievements
        $data['achievements'] = HomePage::$dbController->selectAll('SELECT \'achievement\' AS `type`, `ffxiv__achievement`.`achievementid` AS `id`, `ffxiv__achievement`.`category`, `ffxiv__achievement`.`subcategory`, `ffxiv__achievement`.`name`, `time`, `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__achievement` ON `ffxiv__character_achievement`.`achievementid`=`ffxiv__achievement`.`achievementid` WHERE `ffxiv__character_achievement`.`characterid` = :id AND `ffxiv__achievement`.`category` IS NOT NULL AND `ffxiv__achievement`.`achievementid` IS NOT NULL ORDER BY `time` DESC, `name` LIMIT 10', [':id'=>$this->id]);
        #Get affiliated groups' details
        $data['groups'] = HomePage::$dbController->selectAll(
            '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, `current`, `ffxiv__freecompany_character`.`rankid`, `ffxiv__freecompany_rank`.`rankname` AS `rank`, COALESCE(`ffxiv__freecompany`.`crest`, `ffxiv__freecompany`.`grandcompanyid`) AS `icon` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__freecompany_character`.`rankid`=`ffxiv__freecompany_rank`.`rankid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, `current`, `ffxiv__linkshell_character`.`rankid`, `ffxiv__linkshell_rank`.`rank` AS `rank`, NULL AS `icon` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rankid`=`ffxiv__linkshell_rank`.`lsrankid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, `current`, `ffxiv__pvpteam_character`.`rankid`, `ffxiv__pvpteam_rank`.`rank` AS `rank`, `ffxiv__pvpteam`.`crest` AS `icon` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rankid`=`ffxiv__pvpteam_rank`.`pvprankid` WHERE `characterid`=:id)
            ORDER BY `current` DESC, `name` ASC;',
            [':id'=>$this->id]
        );
        #Clean up the data from unnecessary (technical) clutter
        unset($data['manual'], $data['clanid'], $data['namedayid'], $data['achievementid'], $data['category'], $data['subcategory'], $data['howto'], $data['points'], $data['icon'], $data['item'], $data['itemicon'], $data['itemid'], $data['serverid']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot'])) {
            if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400) {
                (new Cron)->add('ffUpdateEntity', [$this->id, 'character'], priority: 1, message: 'Updating character with ID ' . $this->id);
            }
        }
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getCharacter($this->id)->getCharacterJobs($this->id)->getCharacterAchievements($this->id, false, 0, false, false, true)->getResult();
        if (empty($data['characters'][$this->id]['server'])) {
            if (!empty($data['characters'][$this->id]) && $data['characters'][$this->id] == 404) {
                return ['404' => true];
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Character '.$this->id;
                } else {
                    return 'Failed to get all necessary data for Character '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['characters'][$this->id];
        $data['id'] = $this->id;
        $data['404'] = false;
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->avatarID = $fromDB['avatar'];
        $this->dates = [
            'registered' => strtotime($fromDB['registered']),
            'updated' => strtotime($fromDB['updated']),
            'deleted' => (empty($fromDB['deleted']) ? null : strtotime($fromDB['deleted'])),
        ];
        $this->biology = [
            'gender' => intval($fromDB['genderid']),
            'race' => $fromDB['race'],
            'clan' => $fromDB['clan'],
            'nameday' => $fromDB['nameday'],
            'guardian' => $fromDB['guardian'],
            'guardianid' => $fromDB['guardianid'],
            'incarnations' => $fromDB['incarnations'],
            'oldNames' => intval($fromDB['oldNames']),
            'killedby' => $fromDB['killedby'],
        ];
        $this->location = [
            'datacenter' => $fromDB['datacenter'],
            'server' => $fromDB['server'],
            'region' => $fromDB['region'],
            'city' => $fromDB['city'],
            'cityid' => $fromDB['cityid'],
            'previousServers' => $fromDB['servers'],
        ];
        $this->biography = $fromDB['biography'];
        $this->title = [
            'title' => $fromDB['title'],
            'icon' => $fromDB['titleIcon'],
            'id' => $fromDB['titleid'],
        ];
        $this->grandCompany = [
            'name' => $fromDB['gcName'],
            'rank' => $fromDB['gc_rank'],
            'gcId' => $fromDB['gcId'],
            'gcrankid' => $fromDB['gcrankid'],
        ];
        $this->pvp = intval($fromDB['pvp_matches']);
        $this->groups = $fromDB['groups'];
        $this->owned = [
            'id' => $fromDB['userid'],
            'name' => $fromDB['username']
        ];
        foreach ($this->groups as $key=>$group) {
            $this->groups[$key]['current'] = boolval($group['current']);
        }
        $this->achievements = $fromDB['achievements'];
        foreach ($this->achievements as $key=>$achievement) {
            $this->achievements[$key]['time'] = (empty($achievement['time']) ? null : strtotime($achievement['time']));
        }
        #Remove all already processed elements to converts the rest to jobs array
        unset(
            $fromDB['characterid'], $fromDB['userid'], $fromDB['username'], $fromDB['name'], $fromDB['avatar'], $fromDB['biography'], $fromDB['genderid'], $fromDB['datacenter'],
            $fromDB['registered'], $fromDB['updated'],$fromDB['deleted'], $fromDB['clan'], $fromDB['race'], $fromDB['server'], $fromDB['titleIcon'],
            $fromDB['region'], $fromDB['city'], $fromDB['cityid'], $fromDB['nameday'], $fromDB['guardian'], $fromDB['guardianid'],
            $fromDB['servers'], $fromDB['incarnations'], $fromDB['title'], $fromDB['titleid'], $fromDB['dbid'], $fromDB['gcId'],
            $fromDB['gcrankid'], $fromDB['gc_rank'], $fromDB['gcName'], $fromDB['oldNames'], $fromDB['killedby'],
            $fromDB['groups'], $fromDB['achievements'],  $fromDB['pvp_matches'], $fromDB['enemyid'],  $fromDB['raceid'],
        );
        $this->jobs = $fromDB;
        foreach ($this->jobs as $key=>$job) {
            $this->jobs[$key] = intval($job);
        }
    }

    #Function to update the entity
    protected function updateDB(bool $manual = false): string|bool
    {
        try {
            #If character on Lodestone is not registered in Free Company or PvP Team, add their IDs as NULL for consistency
            if (empty($this->lodestone['freeCompany']['id'])) {
                $this->lodestone['freeCompany']['id'] = NULL;
                $this->lodestone['freeCompany']['registered'] = false;
            } else {
                $this->lodestone['freeCompany']['registered'] = HomePage::$dbController->check('SELECT `freecompanyid` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id', [':id' => $this->lodestone['freeCompany']['id']]);
            }
            if (empty($this->lodestone['pvp']['id'])) {
                $this->lodestone['pvp']['id'] = NULL;
                $this->lodestone['pvp']['registered'] = false;
            } else {
                $this->lodestone['pvp']['registered'] = HomePage::$dbController->check('SELECT `pvpteamid` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id', [':id' => $this->lodestone['pvp']['id']]);
            }
            #Insert Free Companies and PvP Team if they are not registered
            if ($this->lodestone['freeCompany']['id'] !== NULL && $this->lodestone['freeCompany']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__freecompany` (`freecompanyid`, `name`, `serverid`, `updated`) VALUES (:fcId, :fcName, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':fcId' => $this->lodestone['freeCompany']['id'],
                        ':fcName' => $this->lodestone['freeCompany']['name'],
                        ':server'=>$this->lodestone['server'],
                    ],
                ];
            }
            if ($this->lodestone['pvp']['id'] !== NULL && $this->lodestone['pvp']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__pvpteam` (`pvpteamid`, `name`, `datacenterid`, `updated`) VALUES (:pvpId, :pvpName, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':pvpId' => $this->lodestone['pvp']['id'],
                        ':pvpName' => $this->lodestone['pvp']['name'],
                        ':server'=>$this->lodestone['server'],
                    ],
                ];
            }
            #Reduce number of <br>s in biography
            $this->lodestone['bio'] = Security::sanitizeHTML($this->lodestone['bio'] ?? '');
            #Main query to insert or update a character
            $queries[] = [
                'INSERT INTO `ffxiv__character`(
                    `characterid`, `serverid`, `name`, `manual`, `registered`, `updated`, `deleted`, `enemyid`, `biography`, `titleid`, `avatar`, `clanid`, `genderid`, `namedayid`, `guardianid`, `cityid`, `gcrankid`, `pvp_matches`
                )
                VALUES (
                    :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, :manual, UTC_DATE(), UTC_TIMESTAMP(), NULL, NULL, :biography, (SELECT `achievementid` as `titleid` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), :avatar, (SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan), :genderid, (SELECT `namedayid` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), (SELECT `guardianid` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), (SELECT `cityid` FROM `ffxiv__city` WHERE `city`=:city), `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), 0
                )
                ON DUPLICATE KEY UPDATE
                    `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), `name`=:name, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `enemyid`=NULL, `biography`=:biography, `titleid`=(SELECT `achievementid` as `titleid` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), `avatar`=:avatar, `clanid`=(SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan), `genderid`=:genderid, `namedayid`=(SELECT `namedayid` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), `guardianid`=(SELECT `guardianid` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), `cityid`=(SELECT `cityid` FROM `ffxiv__city` WHERE `city`=:city), `gcrankid`=(SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1);',
                [
                    ':characterid'=>$this->id,
                    ':server'=>$this->lodestone['server'],
                    ':name'=>$this->lodestone['name'],
                    ':manual'=>[$manual, 'bool'],
                    ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $this->lodestone['avatar']),
                    ':biography'=>[
                        (($this->lodestone['bio'] == '-') ? NULL : $this->lodestone['bio']),
                        (empty($this->lodestone['bio']) ? 'null' : (($this->lodestone['bio'] == '-') ? 'null' : 'string')),
                    ],
                    ':title'=>(empty($this->lodestone['title']) ? '' : $this->lodestone['title']),
                    ':clan'=>$this->lodestone['clan'],
                    ':genderid'=>($this->lodestone['gender']==='male' ? '1' : '0'),
                    ':nameday'=>$this->lodestone['nameday'],
                    ':guardian'=>$this->lodestone['guardian']['name'],
                    ':city'=>$this->lodestone['city']['name'],
                    ':gcRank'=>(empty($this->lodestone['grandCompany']['rank']) ? '' : $this->lodestone['grandCompany']['rank']),
                ],
            ];
            #Update levels. Doing this in cycle since columns can vary. This can reduce performance, but so far this is the best idea I have to make it as automated as possible
            if (!empty($this->lodestone['jobs'])) {
                foreach ($this->lodestone['jobs'] as $job=>$level) {
                    #Remove spaces from the job name
                    $jobNoSpace = preg_replace('/\s*/', '', $job);
                    #Check if column exists in order to avoid errors. Checking that level is not empty to not waste time on updating zeros
                    if (HomePage::$dbController->checkColumn('ffxiv__character', $jobNoSpace) && !empty($level['level'])) {
                        #Update level
                        /** @noinspection SqlResolve */
                        $queries[] = [
                            'UPDATE `ffxiv__character` SET `'.$jobNoSpace.'`=:level WHERE `characterid`=:characterid;',
                            [
                                ':characterid' => $this->id,
                                ':level' => [intval($level['level']), 'int'],
                            ],
                        ];
                    }
                }
            }
            #Insert server, if it has not been inserted yet. If server is registered at all.
            if (HomePage::$dbController->check('SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server;', [':server' => $this->lodestone['server']]) === true) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__character_servers`(`characterid`, `serverid`) VALUES (:characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server));',
                    [
                        ':characterid' => $this->id,
                        ':server' => $this->lodestone['server'],
                    ],
                ];
            }
            #Insert name, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__character_names`(`characterid`, `name`) VALUES (:characterid, :name);',
                [
                    ':characterid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                ],
            ];
            #Insert race, clan and sex combination, if it has not been inserted yet
            if (!empty($this->lodestone['clan'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__character_clans`(`characterid`, `genderid`, `clanid`) VALUES (:characterid, :genderid, (SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan));',
                    [
                        ':characterid'=>$this->id,
                        ':genderid'=>($this->lodestone['gender']==='male' ? '1' : '0'),
                        ':clan'=>$this->lodestone['clan'],
                    ],
                ];
            }
            #Update company information
            $queries[] = [
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `characterid`=:characterid AND `freecompanyid` '.(empty($this->lodestone['freeCompany']['id']) ? 'IS NOT ' : '!= ').' :fcId;',
                [
                    ':characterid'=>$this->id,
                    ':fcId'=>[
                        $this->lodestone['freeCompany']['id'],
                        (empty($this->lodestone['freeCompany']['id']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Update PvP Team information
            $queries[] = [
                'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `characterid`=:characterid AND `pvpteamid` '.(empty($this->lodestone['pvp']['id']) ? 'IS NOT ' : '!= ').' :pvpId;',
                [
                    ':characterid'=>$this->id,
                    ':pvpId'=>[
                        $this->lodestone['pvp']['id'],
                        (empty($this->lodestone['pvp']['id']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Achievements
            if (!empty($this->lodestone['achievements']) && is_array($this->lodestone['achievements'])) {
                foreach ($this->lodestone['achievements'] as $achievementid=>$item) {
                    $queries[] = [
                        'INSERT INTO `ffxiv__achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points ON DUPLICATE KEY UPDATE `updated`=`updated`, `name`=:name, `icon`=:icon, `points`=:points;',
                        [
                            ':achievementid'=>$achievementid,
                            ':name'=>$item['name'],
                            ':icon'=>str_replace("https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/", "", $item['icon']),
                            ':points'=>$item['points'],
                        ],
                    ];
                    $queries[] = [
                        'INSERT INTO `ffxiv__character_achievement` SET `characterid`=:characterid, `achievementid`=:achievementid, `time`=UTC_DATE() ON DUPLICATE KEY UPDATE `time`=:time;',
                        [
                            ':characterid'=>$this->id,
                            ':achievementid'=>$achievementid,
                            ':time'=>[$item['time'], 'time'],
                        ],
                    ];
                }
            }
            HomePage::$dbController->query($queries);
            #Register Free Company update if change was detected
            if (!empty($this->lodestone['freeCompany']['id']) && HomePage::$dbController->check('SELECT `characterid` FROM `ffxiv__freecompany_character` WHERE `characterid`=:characterid AND `freecompanyid`=:fcID;', [':characterid'=>$this->id, ':fcID'=>$this->lodestone['freeCompany']['id']]) === false) {
                if ((new FreeCompany($this->lodestone['freeCompany']['id']))->update() !== true) {
                    (new Cron)->add('ffUpdateEntity', [$this->lodestone['freeCompany']['id'], 'freecompany'], priority: 1, message: 'Updating free company with ID ' . $this->lodestone['freeCompany']['id']);
                }
            }
            #Register PvP Team update if change was detected
            if (!empty($this->lodestone['pvp']['id']) && HomePage::$dbController->check('SELECT `characterid` FROM `ffxiv__pvpteam_character` WHERE `characterid`=:characterid AND `pvpteamid`=:pvpID;', [':characterid'=>$this->id, ':pvpID'=>$this->lodestone['pvp']['id']]) === false) {
                if ((new PvPTeam($this->lodestone['pvp']['id']))->update() !== true) {
                    (new Cron)->add('ffUpdateEntity', [$this->lodestone['pvp']['id'], 'pvpteam'], priority: 1, message: 'Updating PvP team with ID ' . $this->lodestone['pvp']['id']);
                }
            }
            #Check if character is linked to a user
            $character = HomePage::$dbController->selectRow('SELECT `characterid`, `userid` FROM `ffxiv__character` WHERE `characterid`=:id;', [':id' => $this->id]);
            if ($character['userid']) {
                #Download avatar
                (new User($character['userid']))->addAvatar(false, $this->lodestone['avatar']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    protected function delete(): bool
    {
        try {
            $queries = [];
            #Remove from Free Company
            $queries[] = [
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            #Remove from PvP Team
            $queries[] = [
                'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            #Remove from Linkshells
            $queries[] = [
                'UPDATE `ffxiv__linkshell_character` SET `current`=0 WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            #Update character
            $queries[] = [
                'UPDATE `ffxiv__character` SET `deleted` = UTC_DATE(), `enemyid` = (SELECT `enemyid` FROM `ffxiv__enemy` ORDER BY RAND() LIMIT 1) WHERE `characterid` = :id',
                [':id'=>$this->id],
            ];
            return HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return false;
        }
    }

    #Link user to character
    public function linkUser(): array
    {
        try {
            #Check if character exists and is linked already
            $character = HomePage::$dbController->selectRow('SELECT `characterid`, `userid` FROM `ffxiv__character` WHERE `characterid`=:id;', [':id' => $this->id]);
            if ($character['userid']) {
                return ['http_error' => 409, 'reason' => 'Character already linked'];
            }
            #Register or update the character
            $this->update();
            if (!empty($this->lodestone['id'])) {
                #Something went wrong with getting data
                if (!empty($this->lodestone['404'])) {
                    return ['http_error' => 400, 'reason' => 'No character found with id `'.$this->id.'`'];
                } else {
                    return ['http_error' => 500, 'reason' => 'Failed to get fresh data for character with id `'.$this->id.'`'];
                }
            }
            #Check if biography is set
            if (empty($this->lodestone['bio'])) {
                return ['http_error' => 424, 'reason' => 'No biography found for character with id `'.$this->id.'`'];
            }
            #Check if biography contains the respected text
            $token = preg_replace('/(.*)(fftracker:([a-z\d]{64}))(.*)/uis', '$3', $this->lodestone['bio']);
            if (empty($token)) {
                return ['http_error' => 424, 'reason' => 'No tracker token found for character with id `'.$this->id.'`'];
            }
            #Check if ID of the current user is the same as the user who has this token
            if (!HomePage::$dbController->check('SELECT `userid` FROM `uc__users` WHERE `userid`=:userid AND `ff_token`=:token;', [':userid'=>$_SESSION['userid'], ':token'=>$token])) {
                return ['http_error' => 403, 'reason' => 'Wrong token or user provided'];
            }
            #Link character to user
            $result = HomePage::$dbController->query([
                'UPDATE `ffxiv__character` SET `userid`=:userid WHERE `characterid`=:characterid;', [':userid'=>$_SESSION['userid'], ':characterid'=>$this->id],
                'INSERT IGNORE INTO `uc__user_to_permission` (`userid`, `permission`) VALUES (:userid, \'postFF\'), (:userid, \'refreshOwnedFF\');', [':userid'=>$_SESSION['userid']],
            ]);
            Security::log('User details change', 'Attempted to link FFXIV character', ['id' => $this->id, 'result' => $result]);
            #Download avatar
            (new User($_SESSION['userid']))->addAvatar(false, 'https://img2.finalfantasyxiv.com/f/'.$this->avatarID.'c0_96x96.jpg');
            return ['response' => $result];
        } catch (\Throwable $exception) {
            return ['http_error' => 500, 'reason' => $exception->getMessage()];
        }
    }
    
    #To be called from API to allow update only for owned character
    public function updateFromApi(): bool|array|string
    {
        if ($_SESSION['userid'] === 1) {
            return ['http_error' => 403, 'reason' => 'Authentication required'];
        }
        #Check if any character currently registered in a group is linked to the user
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            /** @noinspection SqlResolve */
            $check = HomePage::$dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid` = :id AND `userid`=:userid', [':id' => $this->id, ':userid' => $_SESSION['userid']]);
            if(!$check) {
                return ['http_error' => 403, 'reason' => 'Character not linked to user'];
            } else {
                return $this->update();
            }
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return ['http_error' => 503, 'reason' => 'Failed to validate linkage'];
        }
    }
}
