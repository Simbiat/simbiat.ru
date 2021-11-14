<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\fftracker\Entity;
use Simbiat\Lodestone;

class Character extends Entity
{
    #Custom properties

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        $dbController = (new Controller);
        #Get general information. Using *, but add name, because otherwise Achievement name overrides Character name, and we do not want that
        $data = $dbController->selectRow('SELECT *, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`updated` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character`.`clanid` = `'.self::dbPrefix.'clan`.`clanid` LEFT JOIN `'.self::dbPrefix.'guardian` ON `'.self::dbPrefix.'character`.`guardianid` = `'.self::dbPrefix.'guardian`.`guardianid` LEFT JOIN `'.self::dbPrefix.'nameday` ON `'.self::dbPrefix.'character`.`namedayid` = `'.self::dbPrefix.'nameday`.`namedayid` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'character`.`cityid` = `'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'character`.`serverid` = `'.self::dbPrefix.'server`.`serverid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'character`.`gcrankid` = `'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'achievement` ON `'.self::dbPrefix.'character`.`titleid` = `'.self::dbPrefix.'achievement`.`achievementid` WHERE `'.self::dbPrefix.'character`.`characterid` = :id;', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names. For now this is commented out due to cases of bullying, when the old names are learnt. They are still being collected, though for statistical purposes.
        #$data['oldnames'] = $dbController->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'character_names` WHERE `characterid`=:id AND `name`!=:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get previous known incarnations (combination of gender and race/clan)
        $data['incarnations'] = $dbController->selectAll('SELECT `genderid`, `'.self::dbPrefix.'clan`.`race`, `'.self::dbPrefix.'clan`.`clan` FROM `'.self::dbPrefix.'character_clans` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character_clans`.`clanid` = `'.self::dbPrefix.'clan`.`clanid` WHERE `'.self::dbPrefix.'character_clans`.`characterid`=:id AND (`'.self::dbPrefix.'character_clans`.`clanid`!=:clanid AND `'.self::dbPrefix.'character_clans`.`genderid`!=:genderid) ORDER BY `genderid` , `race` , `clan` ', [':id'=>$this->id, ':clanid'=>$data['clanid'], ':genderid'=>$data['genderid']]);
        #Get old servers
        $data['servers'] = $dbController->selectAll('SELECT `'.self::dbPrefix.'server`.`datacenter`, `'.self::dbPrefix.'server`.`server` FROM `'.self::dbPrefix.'character_servers` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'server`.`serverid`=`'.self::dbPrefix.'character_servers`.`serverid` WHERE `'.self::dbPrefix.'character_servers`.`characterid`=:id AND `'.self::dbPrefix.'character_servers`.`serverid` != :serverid ORDER BY `datacenter` , `server` ', [':id'=>$this->id, ':serverid'=>$data['serverid']]);
        #Get achievements
        $data['achievements'] = $dbController->selectAll('SELECT `'.self::dbPrefix.'achievement`.`achievementid`, `'.self::dbPrefix.'achievement`.`category`, `'.self::dbPrefix.'achievement`.`subcategory`, `'.self::dbPrefix.'achievement`.`name`, `time`, `icon` FROM `'.self::dbPrefix.'character_achievement` LEFT JOIN `'.self::dbPrefix.'achievement` ON `'.self::dbPrefix.'character_achievement`.`achievementid`=`'.self::dbPrefix.'achievement`.`achievementid` WHERE `'.self::dbPrefix.'character_achievement`.`characterid` = :id AND `'.self::dbPrefix.'achievement`.`category` IS NOT NULL AND `'.self::dbPrefix.'achievement`.`achievementid` IS NOT NULL ORDER BY `time` DESC, `name` ', [':id'=>$this->id]);
        #Get affiliated groups' details
        $data['groups'] = $dbController->selectAll(
            '(SELECT \'freecompany\' AS `type`, `'.self::dbPrefix.'character`.`freecompanyid` AS `id`, `'.self::dbPrefix.'freecompany`.`name` as `name`, 1 AS `current`, `'.self::dbPrefix.'character`.`company_joined` AS `join`, `'.self::dbPrefix.'character`.`company_rank` AS `rankid`, `'.self::dbPrefix.'freecompany_rank`.`rankname`, `'.self::dbPrefix.'freecompany`.`crest` AS `icon` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'character`.`freecompanyid`=`'.self::dbPrefix.'freecompany`.`freecompanyid` LEFT JOIN `'.self::dbPrefix.'freecompany_rank` ON `'.self::dbPrefix.'character`.`freecompanyid`=`'.self::dbPrefix.'freecompany_rank`.`freecompanyid` AND `'.self::dbPrefix.'character`.`company_rank`=`'.self::dbPrefix.'freecompany_rank`.`rankid` WHERE `characterid`=:id AND `'.self::dbPrefix.'character`.`freecompanyid` IS NOT NULL)
            UNION ALL
            (SELECT \'freecompany\' AS `type`, `'.self::dbPrefix.'freecompany_x_character`.`freecompanyid` AS `id`, `'.self::dbPrefix.'freecompany`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `'.self::dbPrefix.'freecompany`.`crest` AS `icon` FROM `'.self::dbPrefix.'freecompany_x_character` LEFT JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'freecompany_x_character`.`freecompanyid`=`'.self::dbPrefix.'freecompany`.`freecompanyid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `'.self::dbPrefix.'linkshell_character`.`linkshellid` AS `id`, `'.self::dbPrefix.'linkshell`.`name` as `name`, 1 AS `current`, NULL AS `join`, `'.self::dbPrefix.'linkshell_character`.`rankid`, `'.self::dbPrefix.'linkshell_rank`.`rank` AS `rankname`, NULL AS `icon` FROM `'.self::dbPrefix.'linkshell_character` LEFT JOIN `'.self::dbPrefix.'linkshell` ON `'.self::dbPrefix.'linkshell_character`.`linkshellid`=`'.self::dbPrefix.'linkshell`.`linkshellid` LEFT JOIN `'.self::dbPrefix.'linkshell_rank` ON `'.self::dbPrefix.'linkshell_character`.`rankid`=`'.self::dbPrefix.'linkshell_rank`.`lsrankid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `'.self::dbPrefix.'linkshell_x_character`.`linkshellid` AS `id`, `'.self::dbPrefix.'linkshell`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, NULL AS `icon` FROM `'.self::dbPrefix.'linkshell_x_character` LEFT JOIN `'.self::dbPrefix.'linkshell` ON `'.self::dbPrefix.'linkshell_x_character`.`linkshellid`=`'.self::dbPrefix.'linkshell`.`linkshellid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `'.self::dbPrefix.'character`.`pvpteamid` AS `id`, `'.self::dbPrefix.'pvpteam`.`name` as `name`, 1 AS `current`, NULL AS `join`, `'.self::dbPrefix.'character`.`pvp_rank` AS `rankid`, `'.self::dbPrefix.'pvpteam_rank`.`rank` AS `rankname`, `'.self::dbPrefix.'pvpteam`.`crest` AS `icon` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'pvpteam` ON `'.self::dbPrefix.'character`.`pvpteamid`=`'.self::dbPrefix.'pvpteam`.`pvpteamid` LEFT JOIN `'.self::dbPrefix.'pvpteam_rank` ON `'.self::dbPrefix.'character`.`pvp_rank`=`'.self::dbPrefix.'pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `'.self::dbPrefix.'character`.`pvpteamid` IS NOT NULL)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `'.self::dbPrefix.'pvpteam_x_character`.`pvpteamid` AS `id`, `'.self::dbPrefix.'pvpteam`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `'.self::dbPrefix.'pvpteam`.`crest` AS `icon` FROM `'.self::dbPrefix.'pvpteam_x_character` LEFT JOIN `'.self::dbPrefix.'pvpteam` ON `'.self::dbPrefix.'pvpteam_x_character`.`pvpteamid`=`'.self::dbPrefix.'pvpteam`.`pvpteamid` WHERE `characterid`=:id)
            ORDER BY `current` DESC, `name` ASC;',
            [':id'=>$this->id]
        );
        #Clean up the data from unnecessary (technical) clutter
        unset($data['clanid'], $data['namedayid'], $data['achievementid'], $data['category'], $data['subcategory'], $data['howto'], $data['points'], $data['icon'], $data['item'], $data['itemicon'], $data['itemid'], $data['serverid']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$this->id, 'character'], priority: 1, message: 'Updating character with ID '.$this->id);
        }
        unset($dbController);
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getCharacter($this->id)->getCharacterJobs($this->id)->getCharacterAchievements($this->id, false, 0, false, false, true)->getResult();
        if (empty($data['characters'][$this->id]['server'])) {
            if (@$data['characters'][$this->id] == 404) {
                $data['entitytype'] = 'character';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Character '.$this->id;
                } else {
                    return 'Failed to get all necessary data for Character '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['characters'][$this->id];
        $data['characterid'] = $this->id;
        $data['entitytype'] = 'character';
        $data['404'] = false;
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {

    }

    #Function to update the entity
    public function update(): string|bool
    {
        try {
            $data = $this->getFromLodestone();
            #Cache controller
            $dbController = (new Controller);
            #Try to get current values of Free Company or PvP Team
            $data['tracker_groups'] = $dbController->selectRow('SELECT `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches` FROM `'.self::dbPrefix.'character` WHERE `characterid` = :id', [':id' => $data['characterid']]);
            #If this is a new character, ensure that array is properly populated
            if (empty($data['tracker_groups'])) {
                $data['tracker_groups'] = ['freecompanyid' => NULL, 'company_joined' => NULL, 'company_rank' => NULL, 'pvpteamid' => NULL, 'pvp_joined' => NULL, 'pvp_rank' => NULL, 'pvp_matches' => NULL];
            }
            #If character on Lodestone is registered in Free Company or PvP Team - check if they are registered on Tracker as well
            #If character on Lodestone is not registered in Free Company or PvP Team, add their IDs as NULL for consistency
            if (empty($data['freeCompany']['id'])) {
                $data['freeCompany']['id'] = NULL;
                $data['freeCompany']['registered'] = false;
            } else {
                $data['freeCompany']['registered'] = $dbController->check('SELECT `freecompanyid` FROM `'.self::dbPrefix.'freecompany` WHERE `freecompanyid` = :id', [':id' => $data['freeCompany']['id']]);
            }
            if (empty($data['pvp']['id'])) {
                $data['pvp']['id'] = NULL;
                $data['pvp']['registered'] = false;
            } else {
                $data['pvp']['registered'] = $dbController->check('SELECT `pvpteamid` FROM `'.self::dbPrefix.'pvpteam` WHERE `pvpteamid` = :id', [':id' => $data['pvp']['id']]);
            }
            #Flags to schedule Free Company or PvPTeam updates
            if ($data['tracker_groups']['freecompanyid'] !== $data['freeCompany']['id'] && $data['freeCompany']['id'] !== NULL) {
                $fcCron = true;
            } else {
                $fcCron = false;
            }
            if ($data['tracker_groups']['pvpteamid'] !== $data['pvp']['id'] && $data['pvp']['id'] !== NULL) {
                $pvpCron = true;
            } else {
                $pvpCron = false;
            }
            #Insert Free Companies and PvP Team if they are not registered
            if ($data['freeCompany']['id'] !== NULL && $data['freeCompany']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany` (`freecompanyid`, `name`, `serverid`, `updated`) VALUES (:fcId, :fcName, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':fcId' => $data['freeCompany']['id'],
                        ':fcName' => $data['freeCompany']['name'],
                        ':server'=>$data['server'],
                    ],
                ];
            }
            if ($data['pvp']['id'] !== NULL && $data['pvp']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam` (`pvpteamid`, `name`, `datacenterid`, `updated`) VALUES (:pvpId, :pvpName, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':pvpId' => $data['pvp']['id'],
                        ':pvpName' => $data['pvp']['name'],
                        ':server'=>$data['server'],
                    ],
                ];
            }
            #Main query to insert or update a character
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'character`(
                    `characterid`, `serverid`, `name`, `registered`, `updated`, `deleted`, `biography`, `titleid`, `avatar`, `clanid`, `genderid`, `namedayid`, `guardianid`, `cityid`, `gcrankid`, `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches`
                )
                VALUES (
                    :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), UTC_TIMESTAMP(), NULL, :biography, (SELECT `achievementid` as `titleid` FROM `'.self::dbPrefix.'achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), :avatar, (SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan), :genderid, (SELECT `namedayid` FROM `'.self::dbPrefix.'nameday` WHERE `nameday`=:nameday), (SELECT `guardianid` FROM `'.self::dbPrefix.'guardian` WHERE `guardian`=:guardian), (SELECT `cityid` FROM `'.self::dbPrefix.'city` WHERE `city`=:city), `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :fcId, :fcDate, NULL, :pvpId, :pvpDate, NULL, 0
                )
                ON DUPLICATE KEY UPDATE
                    `serverid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), `name`=:name, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `biography`=:biography, `titleid`=(SELECT `achievementid` as `titleid` FROM `'.self::dbPrefix.'achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), `avatar`=:avatar, `clanid`=(SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan), `genderid`=:genderid, `namedayid`=(SELECT `namedayid` FROM `'.self::dbPrefix.'nameday` WHERE `nameday`=:nameday), `guardianid`=(SELECT `guardianid` FROM `'.self::dbPrefix.'guardian` WHERE `guardian`=:guardian), `cityid`=(SELECT `cityid` FROM `'.self::dbPrefix.'city` WHERE `city`=:city), `gcrankid`=(SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), `freecompanyid`=:fcId, `company_joined`=:fcDate, `company_rank`=:fcRank, `pvpteamid`=:pvpId, `pvp_joined`=:pvpDate, `pvp_rank`=:pvpRank;',
                [
                    ':characterid'=>$data['characterid'],
                    ':server'=>$data['server'],
                    ':name'=>$data['name'],
                    ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $data['avatar']),
                    ':biography'=>[
                        (($data['bio'] == '-') ? NULL : $data['bio']),
                        (empty($data['bio']) ? 'null' : (($data['bio'] == '-') ? 'null' : 'string')),
                    ],
                    ':title'=>(empty($data['title']) ? '' : $data['title']),
                    ':clan'=>$data['clan'],
                    ':genderid'=>($data['gender']==='male' ? '1' : '0'),
                    ':nameday'=>$data['nameday'],
                    ':guardian'=>$data['guardian']['name'],
                    ':city'=>$data['city']['name'],
                    ':gcRank'=>(empty($data['grandCompany']['rank']) ? '' : $data['grandCompany']['rank']),
                    ':fcId' => [$data['freeCompany']['id'], ($data['freeCompany']['id'] === NULL ? 'null' : 'string')],
                    ':fcDate' => [
                        ($data['freeCompany']['id'] === NULL ? NULL : time()),
                        ($data['freeCompany']['id'] === NULL ? 'null' : 'date'),
                    ],
                    ':fcRank' => [
                        ($data['freeCompany']['id'] === NULL ? NULL : ($data['tracker_groups']['company_rank'] === NULL ? NULL : $data['tracker_groups']['company_rank'])),
                        ($data['freeCompany']['id'] === NULL ? 'null' : ($data['tracker_groups']['company_rank'] === NULL ? 'null' : 'int')),
                    ],
                    ':pvpId' => [$data['pvp']['id'], ($data['pvp']['id'] === NULL ? 'null' : 'string')],
                    ':pvpDate' => [
                        ($data['pvp']['id'] === NULL ? NULL : time()),
                        ($data['pvp']['id'] === NULL ? 'null' : 'date'),
                    ],
                    ':pvpRank' => [
                        ($data['pvp']['id'] === NULL ? NULL : ($data['tracker_groups']['pvp_rank'] === NULL ? NULL : $data['tracker_groups']['pvp_rank'])),
                        ($data['pvp']['id'] === NULL ? 'null' : ($data['tracker_groups']['pvp_rank'] === NULL ? 'null' : 'int')),
                    ],
                ],
            ];
            #Update levels. Doing this in cycle since columns can vary. This can reduce performance, but so far this is the best idea I have to make it as automated as possible
            if (!empty($data['jobs'])) {
                foreach ($data['jobs'] as $job=>$level) {
                    #Remove spaces from the job name
                    $jobNoSpace = preg_replace('/\s*/', '', $job);
                    #Check if column exists in order to avoid errors. Checking that level is not empty to not waste time on updating zeros
                    if ($dbController->checkColumn(''.self::dbPrefix.'character', $jobNoSpace) && !empty($level['level'])) {
                        #Update level
                        /** @noinspection SqlResolve */
                        $queries[] = [
                            'UPDATE `'.self::dbPrefix.'character` SET `'.$jobNoSpace.'`=:level WHERE `characterid`=:characterid;',
                            [
                                ':characterid' => $data['characterid'],
                                ':level' => [intval($level['level']), 'int'],
                            ],
                        ];
                    }
                }
            }
            #Insert server, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'character_servers`(`characterid`, `serverid`) VALUES (:characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server));',
                [
                    ':characterid'=>$data['characterid'],
                    ':server'=>$data['server'],
                ],
            ];
            #Insert name, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'character_names`(`characterid`, `name`) VALUES (:characterid, :name);',
                [
                    ':characterid'=>$data['characterid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Insert race, clan and sex combination, if it has not been inserted yet
            if (!empty($data['clan'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'character_clans`(`characterid`, `genderid`, `clanid`) VALUES (:characterid, :genderid, (SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan));',
                    [
                        ':characterid'=>$data['characterid'],
                        ':genderid'=>($data['gender']==='male' ? '1' : '0'),
                        ':clan'=>$data['clan'],
                    ],
                ];
            }
            #Check if change of Free Company has been detected
            if ($fcCron || ($data['tracker_groups']['freecompanyid'] !== NULL && $data['freeCompany']['id'] === NULL)) {
                #Register previous Free Company
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                    [
                        ':characterid'=>$data['characterid'],
                        ':fcId'=>$data['tracker_groups']['freecompanyid'],
                    ],
                ];
            }
            #Check if change of PvP Team has been detected
            if ($fcCron || ($data['tracker_groups']['pvpteamid'] !== NULL && $data['pvp']['id'] === NULL)) {
                #Register previous PvP Team
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpId);',
                    [
                        ':characterid'=>$data['characterid'],
                        ':pvpId'=>$data['tracker_groups']['pvpteamid'],
                    ],
                ];
            }
            #Achievements
            if (!empty($data['achievements']) && is_array($data['achievements'])) {
                foreach ($data['achievements'] as $achievementid=>$item) {
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points ON DUPLICATE KEY UPDATE `updated`=`updated`, `name`=:name, `icon`=:icon, `points`=:points;',
                        [
                            ':achievementid'=>$achievementid,
                            ':name'=>$item['name'],
                            ':icon'=>str_replace("https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/", "", $item['icon']),
                            ':points'=>$item['points'],
                        ],
                    ];
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'character_achievement` SET `characterid`=:characterid, `achievementid`=:achievementid, `time`=UTC_DATE() ON DUPLICATE KEY UPDATE `time`=:time;',
                        [
                            ':characterid'=>$data['characterid'],
                            ':achievementid'=>$achievementid,
                            ':time'=>[$item['time'], 'date'],
                        ],
                    ];
                }
            }
            $dbController->query($queries);
            #Register Free Company update if change was detected
            if ($fcCron === true || $pvpCron === true) {
                #Cache CRON object
                $cron = (new Cron);
            }
            if ($fcCron) {
                $cron->add('ffentityupdate', [$data['freeCompany']['id'], 'freecompany'], priority: 1, message: 'Updating free company with ID '.$data['freeCompany']['id']);
            }
            #Register PvP Team update if change was detected
            if ($pvpCron) {
                $cron->add('ffentityupdate', [$data['pvp']['id'], 'pvpteam'], priority: 1, message: 'Updating PvP team with ID '.$data['pvp']['id']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    public function delete(): bool
    {
        try {
            #Cache DB Controller
            $dbController = (new Controller);
            $queries = [];
            #Try to get current values of Free Company or PvP Team
            $groups = $dbController->selectRow('SELECT `freecompanyid`, `pvpteamid` FROM `'.self::dbPrefix.'character` WHERE `characterid` = :id', [':id' => $this->id]);
            #Remove from Free Company
            if (!empty($groups['freecompanyid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                    [
                        ':characterid'=>$this->id,
                        ':fcId'=>$groups['freecompanyid'],
                    ],
                ];
            }
            #Remove from PvP Team
            if (!empty($groups['pvpteamid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpteamid);',
                    [
                        ':characterid'=>$this->id,
                        ':pvpteamid'=>$groups['pvpteamid'],
                    ],
                ];
            }
            #Remove from Linkshells
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_x_character` (`characterid`, `linkshellid`) SELECT `'.self::dbPrefix.'linkshell_character`.`characterid`, `'.self::dbPrefix.'linkshell_character`.`linkshellid` FROM `'.self::dbPrefix.'linkshell_character` WHERE `'.self::dbPrefix.'linkshell_character`.`characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            $queries[] = [
                'DELETE FROM `'.self::dbPrefix.'linkshell_character` WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            #Update character
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'character` SET `deleted` = UTC_DATE(), `freecompanyid` = NULL, `company_joined` = NULL, `company_rank` = NULL, `pvpteamid` = NULL, `pvp_joined` = NULL, `pvp_rank` = NULL WHERE `characterid` = :id',
                [':id'=>$this->id],
            ];
            return $dbController->query($queries);
        } catch (\Throwable $e) {
            error_log($e->getMessage()."\r\n".$e->getTraceAsString());
            return false;
        }
    }
}
