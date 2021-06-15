<?php
#Functions used to get update data on tracker
declare(strict_types=1);
namespace Simbiat\FFTModules;

use Simbiat\Cron;
use Simbiat\Database\Controller;

trait Updater
{
    #Update data
    private function EntityUpdate(array $data): string|bool
    {
        return match(@$data['entitytype']) {
            'character' => $this->CharacterUpdate($data),
            'freecompany' => $this->CompanyUpdate($data),
            'linkshell' => $this->LinkshellUpdate($data),
            'crossworldlinkshell' => $this->LinkshellUpdate($data, true),
            'pvpteam' => $this->PVPUpdate($data),
            'achievement' => $this->AchievementUpdate($data),
            default => false,
        };
    }

    private function CharacterUpdate(array $data): string|bool
    {
        try {
            #Cache controller
            $dbController = (new Controller);
            #Try to get current values of Free Company or PvP Team
            $data['tracker_groups'] = $dbController->selectRow('SELECT `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches` FROM `ffxiv__character` WHERE `characterid` = :id', [':id' => $data['characterid']]);
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
                $data['freeCompany']['registered'] = $dbController->check('SELECT `freecompanyid` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id', [':id' => $data['freeCompany']['id']]);
            }
            if (empty($data['pvp']['id'])) {
                $data['pvp']['id'] = NULL;
                $data['pvp']['registered'] = false;
            } else {
                $data['pvp']['registered'] = $dbController->check('SELECT `pvpteamid` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id', [':id' => $data['pvp']['id']]);
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
                    'INSERT IGNORE INTO `ffxiv__freecompany` (`freecompanyid`, `name`, `serverid`, `updated`) VALUES (:fcId, :fcName, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':fcId' => $data['freeCompany']['id'],
                        ':fcName' => $data['freeCompany']['name'],
                        ':server'=>$data['server'],
                    ],
                ];
            }
            if ($data['pvp']['id'] !== NULL && $data['pvp']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__pvpteam` (`pvpteamid`, `name`, `datacenterid`, `updated`) VALUES (:pvpId, :pvpName, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':pvpId' => $data['pvp']['id'],
                        ':pvpName' => $data['pvp']['name'],
                        ':server'=>$data['server'],
                    ],
                ];
            }
            #Main query to insert or update a character
            $queries[] = [
                'INSERT INTO `ffxiv__character`(
                    `characterid`, `serverid`, `name`, `registered`, `updated`, `deleted`, `biography`, `titleid`, `avatar`, `clanid`, `genderid`, `namedayid`, `guardianid`, `cityid`, `gcrankid`, `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches`
                )
                VALUES (
                    :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), UTC_TIMESTAMP(), NULL, :biography, (SELECT `achievementid` as `titleid` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), :avatar, (SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan), :genderid, (SELECT `namedayid` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), (SELECT `guardianid` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), (SELECT `cityid` FROM `ffxiv__city` WHERE `city`=:city), `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :fcId, :fcDate, NULL, :pvpId, :pvpDate, NULL, 0
                )
                ON DUPLICATE KEY UPDATE
                    `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), `name`=:name, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `biography`=:biography, `titleid`=(SELECT `achievementid` as `titleid` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), `avatar`=:avatar, `clanid`=(SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan), `genderid`=:genderid, `namedayid`=(SELECT `namedayid` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), `guardianid`=(SELECT `guardianid` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), `cityid`=(SELECT `cityid` FROM `ffxiv__city` WHERE `city`=:city), `gcrankid`=(SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), `freecompanyid`=:fcId, `company_joined`=:fcDate, `company_rank`=:fcRank, `pvpteamid`=:pvpId, `pvp_joined`=:pvpDate, `pvp_rank`=:pvpRank;',
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
            #Add levels
            if (!empty($data['jobs'])) {
                foreach ($data['jobs'] as $job=>$level) {
                    #Insert job (we lose performance a tiny bit, but this allows to automatically add new jobs and avoid failures on next step)
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__job` (`name`) VALUES (:job);',
                        [
                            ':job' => [$job, 'string'],
                        ]
                    ];
                    #Insert actual level
                    $queries[] = [
                        'INSERT INTO `ffxiv__character_jobs`(`characterid`, `jobid`, `level`) VALUES (:characterid, (SELECT `jobid` FROM `ffxiv__job` WHERE `name`=:job LIMIT 1), :level) ON DUPLICATE KEY UPDATE `level`=:level;',
                        [
                            ':characterid' => $data['characterid'],
                            ':job' => [$job, 'string'],
                            ':level' => [(empty($level) ? 0 : intval($level)), 'int'],
                        ],
                    ];
                }
            }
            #Insert server, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__character_servers`(`characterid`, `serverid`) VALUES (:characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server));',
                [
                    ':characterid'=>$data['characterid'],
                    ':server'=>$data['server'],
                ],
            ];
            #Insert name, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__character_names`(`characterid`, `name`) VALUES (:characterid, :name);',
                [
                    ':characterid'=>$data['characterid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Insert race, clan and sex combination, if it has not been inserted yet
            if (!empty($data['clan'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__character_clans`(`characterid`, `genderid`, `clanid`) VALUES (:characterid, :genderid, (SELECT `clanid` FROM `ffxiv__clan` WHERE `clan`=:clan));',
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
                    'INSERT IGNORE INTO `ffxiv__freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
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
                    'INSERT IGNORE INTO `ffxiv__pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpId);',
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

    private function CompanyUpdate(array $data): string|bool
    {
        try {
            #Attempt to get crest
            $data['crest'] = $this->CrestMerge($data['freecompanyid'], $data['crest']);
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a Free Company
            $queries[] = [
                'INSERT INTO `ffxiv__freecompany` (
                    `freecompanyid`, `name`, `serverid`, `formed`, `registered`, `updated`, `deleted`, `grandcompanyid`, `tag`, `crest`, `rank`, `slogan`, `activeid`, `recruitment`, `communityid`, `estate_zone`, `estateid`, `estate_message`, `Role-playing`, `Leveling`, `Casual`, `Hardcore`, `Dungeons`, `Guildhests`, `Trials`, `Raids`, `PvP`, `Tank`, `Healer`, `DPS`, `Crafter`, `Gatherer`
                )
                VALUES (
                    :freecompanyid, :name, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_name`=:grandCompany ORDER BY `gcrankid` LIMIT 1), :tag, :crest, :rank, :slogan, (SELECT `activeid` FROM `ffxiv__timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), :recruitment, :communityid, :estate_zone, (SELECT `estateid` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), :estate_message, :rolePlaying, :leveling, :casual, :hardcore, :dungeons, :guildhests, :trials, :raids, :pvp, :tank, :healer, :dps, :crafter, :gatherer
                )
                ON DUPLICATE KEY UPDATE
                    `name`=:name, `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `tag`=:tag, `crest`=COALESCE(:crest, `crest`), `rank`=:rank, `slogan`=:slogan, `activeid`=(SELECT `activeid` FROM `ffxiv__timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), `recruitment`=:recruitment, `communityid`=:communityid, `estate_zone`=:estate_zone, `estateid`=(SELECT `estateid` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), `estate_message`=:estate_message, `Role-playing`=:rolePlaying, `Leveling`=:leveling, `Casual`=:casual, `Hardcore`=:hardcore, `Dungeons`=:dungeons, `Guildhests`=:guildhests, `Trials`=:trials, `Raids`=:raids, `PvP`=:pvp, `Tank`=:tank, `Healer`=:healer, `DPS`=:dps, `Crafter`=:crafter, `Gatherer`=:gatherer;',
                [
                    ':freecompanyid'=>$data['freecompanyid'],
                    ':name'=>$data['name'],
                    ':server'=>$data['server'],
                    ':formed'=>[$data['formed'], 'date'],
                    ':grandCompany'=>$data['grandCompany'],
                    ':tag'=>$data['tag'],
                    ':crest'=>[
                            (empty($data['crest']) ? NULL : $data['crest']),
                            (empty($data['crest']) ? 'null' : 'string'),
                    ],
                    ':rank'=>$data['rank'],
                    ':slogan'=>[
                            (empty($data['slogan']) ? NULL : $data['slogan']),
                            (empty($data['slogan']) ? 'null' : 'string'),
                    ],
                    ':active'=>[
                            (($data['active'] == 'Not specified') ? NULL : (empty($data['active']) ? NULL : $data['active'])),
                            (($data['active'] == 'Not specified') ? 'null' : (empty($data['active']) ? 'null' : 'string')),
                    ],
                    ':recruitment'=>(strcasecmp($data['recruitment'], 'Open') === 0 ? 1 : 0),
                    ':estate_zone'=>[
                            (empty($data['estate']['name']) ? NULL : $data['estate']['name']),
                            (empty($data['estate']['name']) ? 'null' : 'string'),
                    ],
                    ':estate_address'=>[
                            (empty($data['estate']['address']) ? NULL : $data['estate']['address']),
                            (empty($data['estate']['address']) ? 'null' : 'string'),
                    ],
                    ':estate_message'=>[
                            (empty($data['estate']['greeting']) ? NULL : $data['estate']['greeting']),
                            (empty($data['estate']['greeting']) ? 'null' : 'string'),
                    ],
                    ':rolePlaying'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Role-playing', array_column($data['focus'], 'name'))]['enabled']),
                    ':leveling'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Leveling', array_column($data['focus'], 'name'))]['enabled']),
                    ':casual'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Casual', array_column($data['focus'], 'name'))]['enabled']),
                    ':hardcore'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Hardcore', array_column($data['focus'], 'name'))]['enabled']),
                    ':dungeons'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Dungeons', array_column($data['focus'], 'name'))]['enabled']),
                    ':guildhests'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Guildhests', array_column($data['focus'], 'name'))]['enabled']),
                    ':trials'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Trials', array_column($data['focus'], 'name'))]['enabled']),
                    ':raids'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Raids', array_column($data['focus'], 'name'))]['enabled']),
                    ':pvp'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('PvP', array_column($data['focus'], 'name'))]['enabled']),
                    ':tank'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Tank', array_column($data['seeking'], 'name'))]['enabled']),
                    ':healer'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Healer', array_column($data['seeking'], 'name'))]['enabled']),
                    ':dps'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('DPS', array_column($data['seeking'], 'name'))]['enabled']),
                    ':crafter'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Crafter', array_column($data['seeking'], 'name'))]['enabled']),
                    ':gatherer'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Gatherer', array_column($data['seeking'], 'name'))]['enabled']),
                    ':communityid'=>[
                            (empty($data['communityid']) ? NULL : $data['communityid']),
                            (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Free Company name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__freecompany_names`(`freecompanyid`, `name`) VALUES (:freecompanyid, :name);',
                [
                    ':freecompanyid'=>$data['freecompanyid'],
                    ':name'=>$data['name'],
                ],
            ];
            if (!empty($data['members'])) {
                #Adding ranking
                if (!empty($data['weekly_rank']) && !empty($data['monthly_rank'])) {
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__freecompany_ranking` (`freecompanyid`, `date`, `weekly`, `monthly`, `members`) SELECT * FROM (SELECT :freecompanyid AS `freecompanyid`, UTC_DATE() AS `date`, :weekly AS `weekly`, :monthly AS `monthly`, :members AS `members` FROM DUAL WHERE :freecompanyid NOT IN (SELECT `freecompanyid` FROM (SELECT * FROM `ffxiv__freecompany_ranking` WHERE `freecompanyid`=:freecompanyid ORDER BY `date` DESC LIMIT 1) `lastrecord` WHERE `weekly`=:weekly AND `monthly`=:monthly) LIMIT 1) `actualinsert`;',
                        [
                            ':freecompanyid' => $data['freecompanyid'],
                            ':weekly' => $data['weekly_rank'],
                            ':monthly' => $data['monthly_rank'],
                            ':members' => count($data['members']),
                        ],
                    ];
                }
            }
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `ffxiv__character` WHERE `freecompanyid`=:fcId', [':fcId'=>$data['freecompanyid']]);
            #Process members, that left the company
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                        [
                            ':characterid'=>$member,
                            ':fcId'=>$data['freecompanyid'],
                        ],
                    ];
                    #Remove company details
                    $queries[] = [
                        'UPDATE `ffxiv__character` SET `freecompanyid`=NULL, `company_joined`=NULL, `company_rank`=NULL WHERE `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Register or update rank name
                    $queries[] = [
                        'INSERT INTO `ffxiv__freecompany_rank` (`freecompanyid`, `rankid`, `rankname`) VALUE (:freecompanyid, :rankid, :rankName) ON DUPLICATE KEY UPDATE `rankname`=:rankName',
                        [
                            ':freecompanyid'=>$data['freecompanyid'],
                            ':rankid'=>$details['rankid'],
                            ':rankName'=>(empty($details['rank']) ? '' : $details['rank']),
                        ],
                    ];
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if ($data['members'][$member]['registered']) {
                        #Update company status
                        $queries[] = [
                            'UPDATE `ffxiv__character` SET `freecompanyid`=:fcId, `company_joined`=COALESCE(`company_joined`, UTC_DATE()), `company_rank`=:rankid WHERE `characterid`=:characterid;',
                            [
                                ':characterid'=>$member,
                                ':fcId'=>$data['freecompanyid'],
                                ':rankid'=>$details['rankid'],
                            ],
                        ];
                    } else {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`, `freecompanyid`, `company_joined`, `company_rank`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :fcId, UTC_DATE(), :rankid
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                                ':fcId'=>$data['freecompanyid'],
                                ':rankid'=>$details['rankid'],
                            ]
                        ];
                    }
                }
            }
            #Running the queries we've accumulated
            $dbController->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($data['members'])) {
                $this->charMassCron($data['members']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    private function PVPUpdate(array $data): string|bool
    {
        try {
            #Attempt to get crest
            $data['crest'] = $this->CrestMerge($data['pvpteamid'], $data['crest']);
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a PvP Team
            $queries[] = [
                'INSERT INTO `ffxiv__pvpteam` (`pvpteamid`, `name`, `formed`, `registered`, `updated`, `deleted`, `datacenterid`, `communityid`, `crest`) VALUES (:pvpteamid, :name, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `ffxiv__server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), :communityid, :crest) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=:formed, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `datacenterid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), `communityid`=:communityid, `crest`=COALESCE(:crest, `crest`);',
                [
                    ':pvpteamid'=>$data['pvpteamid'],
                    ':datacenter'=>$data['dataCenter'],
                    ':name'=>$data['name'],
                    ':formed'=>[$data['formed'], 'date'],
                    ':communityid'=>[
                        (empty($data['communityid']) ? NULL : $data['communityid']),
                        (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                    ':crest'=>[
                        (empty($data['crest']) ? NULL : $data['crest']),
                        (empty($data['crest']) ? 'null' : 'string'),
                    ]
                ],
            ];
            #Register PvP Team name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__pvpteam_names`(`pvpteamid`, `name`) VALUES (:pvpteamid, :name);',
                [
                    ':pvpteamid'=>$data['pvpteamid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `ffxiv__character` WHERE `pvpteamid`=:pvpteamid', [':pvpteamid'=>$data['pvpteamid']]);
            #Process members, that left the team
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpteamid);',
                        [
                            ':characterid'=>$member,
                            ':pvpteamid'=>$data['pvpteamid'],
                        ],
                    ];
                    #Remove team details
                    $queries[] = [
                        'UPDATE `ffxiv__character` SET `pvpteamid`=NULL, `pvp_joined`=NULL, `pvp_rank`=NULL WHERE `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if ($data['members'][$member]['registered']) {
                        #Update team status
                        $queries[] = [
                            'UPDATE `ffxiv__character` SET `pvpteamid`=:pvpteamid, `pvp_joined`=COALESCE(`pvp_joined`, UTC_DATE()), `pvp_rank`=(SELECT `pvprankid` FROM `ffxiv__pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), `pvp_matches`=:matches WHERE `characterid`=:characterid;',
                            [
                                ':characterid'=>$member,
                                ':pvpteamid'=>$data['pvpteamid'],
                                ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank']),
                                ':matches'=>(empty($details['feasts']) ? 0 : $details['feasts']),
                            ],
                        ];
                    } else {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :pvpteamid, UTC_DATE(), (SELECT `pvprankid` FROM `ffxiv__pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), :matches
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                                ':pvpteamid'=>$data['pvpteamid'],
                                ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank']),
                                ':matches'=>(empty($details['feasts']) ? 0 : $details['feasts']),
                            ]
                        ];
                    }
                }
            }
            #Running the queries we've accumulated
            $dbController->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($data['members'])) {
                $this->charMassCron($data['members']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    private function LinkshellUpdate(array $data, bool $crossworld = false): string|bool
    {
        try {
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a Linkshell
            $queries[] = [
                'INSERT INTO `ffxiv__linkshell`(`linkshellid`, `name`, `crossworld`, `formed`, `registered`, `updated`, `deleted`, `serverid`) VALUES (:linkshellid, :name, :crossworld, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1)) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=NULL, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1), `communityid`=:communityid;',
                [
                    ':linkshellid'=>$data['linkshellid'],
                    ':server'=>$data['server'] ?? $data['dataCenter'],
                    ':name'=>$data['name'],
                    ':crossworld'=>[intval($crossworld), 'int'],
                    ':formed'=>[
                        (empty($data['formed']) ? NULL : $data['formed']),
                        (empty($data['formed']) ? 'null' : 'date'),
                    ],
                    ':communityid'=>[
                            (empty($data['communityid']) ? NULL : $data['communityid']),
                            (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Linkshell name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__linkshell_names`(`linkshellid`, `name`) VALUES (:linkshellid, :name);',
                [
                    ':linkshellid'=>$data['linkshellid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `ffxiv__linkshell_character` WHERE `linkshellid`=:linkshellid', [':linkshellid'=>$data['linkshellid']]);
            #Process members, that left the linkshell
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__linkshell_x_character` (`characterid`, `linkshellid`) VALUES (:characterid, :linkshellid);',
                        [
                            ':characterid'=>$member,
                            ':linkshellid'=>$data['linkshellid'],
                        ],
                    ];
                    #Remove team details
                    $queries[] = [
                        'DELETE FROM `ffxiv__linkshell_character` WHERE `characterid`=:characterid AND `linkshellid`=:linkshellid;',
                        [
                            ':characterid'=>$member,
                            ':linkshellid'=>$data['linkshellid'],
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if (!$data['members'][$member]['registered']) {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1)
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                            ]
                        ];
                    }
                    #Insert/update character relationship with linkshell
                    $queries[] = [
                        'INSERT INTO `ffxiv__linkshell_character` (`linkshellid`, `characterid`, `rankid`) VALUES (:linkshellid, :memberid, (SELECT `lsrankid` FROM `ffxiv__linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1)) ON DUPLICATE KEY UPDATE `rankid`=(SELECT `lsrankid` FROM `ffxiv__linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1);',
                        [
                            ':linkshellid'=>$data['linkshellid'],
                            ':memberid'=>$member,
                            ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank'])
                        ],
                    ];
                }
            }
            #(new \Simbiat\HomeTests)->testDump($queries, false);
            #exit;
            #Running the queries we've accumulated
            (new Controller)->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($data['members'])) {
                $this->charMassCron($data['members']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Update statistics
    public function UpdateStatistics(): bool|string
    {
        try {
            foreach (['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other'] as $type) {
                $this->Statistics($type, '', true);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    public function AchievementUpdate(array $data): bool|string
    {
        try {
            #Unset entitytype
            unset($data['entitytype']);
            return (new Controller)->query('INSERT INTO `ffxiv__achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid ON DUPLICATE KEY UPDATE `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid, `updated`=UTC_TIMESTAMP()', $data);
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update old entities
    public function UpdateOld(int $limit = 1): bool|string
    {
        #Sanitize entities number
        if ($limit < 1) {
            $limit = 1;
        }
        try {
            $dbCon = (new Controller);
            $entities = $dbCon->selectAll('
                    SELECT `type`, `id`, `charid` FROM (
                        SELECT * FROM (
                            SELECT \'character\' AS `type`, `characterid` AS `id`, \'\' AS `charid`, `updated`, `deleted` FROM `ffxiv__character`
                            UNION ALL
                            SELECT \'freecompany\' AS `type`, `freecompanyid` AS `id`, \'\' AS `charid`, `updated`, `deleted` FROM `ffxiv__freecompany`
                            UNION ALL
                            SELECT \'pvpteam\' AS `type`, `pvpteamid` AS `id`, \'\' AS `charid`, `updated`, `deleted` FROM `ffxiv__pvpteam`
                            UNION ALL
                            SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `linkshellid`, \'\' AS `charid`, `updated`, `deleted` AS `id` FROM `ffxiv__linkshell`
                            WHERE `deleted` IS NULL
                        ) `nonach`
                        UNION ALL
                        SELECT \'achievement\' AS `type`, `ffxiv__achievement`.`achievementid` AS `id`, (SELECT `characterid` FROM `ffxiv__character_achievement` WHERE `ffxiv__character_achievement`.`achievementid` = `ffxiv__achievement`.`achievementid` LIMIT 1) AS `charid`, `updated`, NULL AS `deleted` FROM `ffxiv__achievement` HAVING `charid` IS NOT NULL
                    ) `allentities`
                    ORDER BY `updated` LIMIT :maxLines',
                [
                    ':maxLines'=>[$limit, 'int'],
                ]
            );
            foreach ($entities as $entity) {
                $result = $this->Update(strval($entity['id']), $entity['type'], $entity['charid']);
                if (!in_array($result, ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam', 'achievement'])) {
                    return $result;
                }
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    /**
     * @throws \Exception
     */
    #Function to "delete" entities
    private function DeleteEntity(string $id, string $type): bool
    {
        #Cache DB Controller
        $dbController = (new Controller);
        if ($type === 'freecompany') {
            #Remove characters from group
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__freecompany_x_character` (`characterid`, `freecompanyid`) SELECT `ffxiv__character`.`characterid`, `ffxiv__character`.`freecompanyid` FROM `ffxiv__character` WHERE `ffxiv__character`.`freecompanyid`=:groupId;',
                [':groupId' => $id,]
            ];
            #Update characters
            $queries[] = [
                'UPDATE `ffxiv__character` SET `freecompanyid`=NULL, `company_joined`=NULL, `company_rank`=NULL WHERE `freecompanyid`=:groupId;', [':groupId' => $id,]
            ];
            #Remove ranks (not ranking!)
            $queries[] = [
                'DELETE FROM `ffxiv__freecompany_rank` WHERE `freecompanyid` = :id',
                [':id' => $id],
            ];
            #Update Free Company
            $queries[] = [
                'UPDATE `ffxiv__freecompany` SET `deleted` = UTC_DATE() WHERE `freecompanyid` = :id',
                [':id' => $id],
            ];
        } elseif ($type === 'pvpteam') {
            #Remove characters from group
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__pvpteam_x_character` (`characterid`, `pvpteamid`) SELECT `ffxiv__character`.`characterid`, `ffxiv__character`.`pvpteamid` FROM `ffxiv__character` WHERE `ffxiv__character`.`pvpteamid`=:groupId;',
                [':groupId'=>$id,]
            ];
            #Update characters
            $queries[] = [
                'UPDATE `ffxiv__character` SET `pvpteamid`=NULL, `pvp_joined`=NULL, `pvp_rank`=NULL WHERE `pvpteamid`=:groupId;', [':groupId'=>$id,]
            ];
            #Update PvP Team
            $queries[] = [
                'UPDATE `ffxiv__pvpteam` SET `deleted` = UTC_DATE() WHERE `pvpteamid` = :id', [':id'=>$id],
            ];
        } elseif ($type === 'character') {
            #Try to get current values of Free Company or PvP Team
            $groups = $dbController->selectRow('SELECT `freecompanyid`, `pvpteamid` FROM `ffxiv__character` WHERE `characterid` = :id', [':id' => $id]);
            #Remove from Free Company
            if (!empty($groups['freecompanyid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                    [
                        ':characterid'=>$id,
                        ':fcId'=>$groups['freecompanyid'],
                    ],
                ];
            }
            #Remove from PvP Team
            if (!empty($groups['pvpteamid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpteamid);',
                    [
                        ':characterid'=>$id,
                        ':pvpteamid'=>$groups['pvpteamid'],
                    ],
                ];
            }
            #Remove from Linkshells
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__linkshell_x_character` (`characterid`, `linkshellid`) SELECT `ffxiv__linkshell_character`.`characterid`, `ffxiv__linkshell_character`.`linkshellid` FROM `ffxiv__linkshell_character` WHERE `ffxiv__linkshell_character`.`characterid`=:characterid;',
                [
                    ':characterid'=>$id,
                ],
            ];
            $queries[] = [
                'DELETE FROM `ffxiv__linkshell_character` WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$id,
                ],
            ];
            #Update character
            $queries[] = [
                'UPDATE `ffxiv__character` SET `deleted` = UTC_DATE(), `freecompanyid` = NULL, `company_joined` = NULL, `company_rank` = NULL, `pvpteamid` = NULL, `pvp_joined` = NULL, `pvp_rank` = NULL WHERE `characterid` = :id',
                [':id'=>$id],
            ];
        } else {
            #Remove characters from group
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__linkshell_x_character` (`characterid`, `linkshellid`) SELECT `ffxiv__linkshell_character`.`characterid`, `ffxiv__linkshell_character`.`linkshellid` FROM `ffxiv__linkshell_character` WHERE `ffxiv__linkshell_character`.`linkshellid`=:groupId;',
                [
                    ':groupId'=>$id,
                ]
            ];
            #Update characters
            $queries[] = [
                'DELETE FROM `ffxiv__linkshell_character` WHERE `linkshellid`=:groupId;',
                [
                    ':groupId'=>$id,
                ]
            ];
            #Update linkshell
            $queries[] = [
                'UPDATE `ffxiv__linkshell` SET `deleted` = UTC_DATE() WHERE `linkshellid` = :id',
                [':id'=>$id],
            ];
        }
        return $dbController->query($queries);
    }

    /**
     * @throws \Exception
     */
    #Helper function to add new characters to Cron en mass
    private function charMassCron(array $members): void
    {
        #Cache CRON object
        $cron = (new Cron);
        foreach ($members as $member=>$details) {
            if (!$details['registered']) {
                #Priority is higher, since they are missing a lot of data.
                $cron->add('ffentityupdate', [$member, 'character'], priority: 2, message: 'Updating character with ID ' . $member);
            }
        }
    }

    #Function to merge 1 to 3 images making up a crest on Lodestone into 1 stored on tracker side
    private function CrestMerge(string $groupId, array $images): string
    {
        try {
            $imgFolder = $GLOBALS['siteconfig']['merged_crests'];
            #Checking if directory exists
            if (!is_dir($imgFolder)) {
                #Creating directory
                @mkdir($imgFolder, recursive: true);
            }
            #Preparing set of layers, since Lodestone stores crests as 3 (or less) separate images
            $layers = array();
            foreach ($images as $key=>$image) {
                $layers[$key] = @imagecreatefrompng($image);
                if ($layers[$key] === false) {
                    #This means that we failed to get the image thus final crest will either fail or be corrupt, thus exiting early
                    throw new \RuntimeException('Failed to download '.$image.' used as layer '.$key.' for '.$groupId.' crest');
                }
            }
            #Create image object
            $image = imagecreatetruecolor(128, 128);
            #Set transparency
            imagealphablending($image, true);
            imagesavealpha($image, true);
            imagecolortransparent($image, imagecolorallocatealpha($image, 255, 0, 0, 127));
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 255, 0, 0, 127));
            #Copy each Lodestone image onto the image object
            for ($i = 0; $i < count($layers); $i++) {
                imagecopy($image, $layers[$i], 0, 0, 0, 0, 128, 128);
                #Destroy layer to free some memory
                imagedestroy($layers[$i]);
            }
            #Saving temporary file
            imagepng($image, $imgFolder.$groupId.'.png', 9, PNG_ALL_FILTERS);
            #Explicitely destroy image object
            imagedestroy($image);
            #Get hash of the file
            if (!file_exists($imgFolder.$groupId.'.png')) {
                #Failed to save the image
                throw new \RuntimeException('Failed to save crest '.$imgFolder.$groupId.'.png');
            }
            $hash = hash_file('sha3-256', $imgFolder.$groupId.'.png');
            #Get final path based on hash
            $finalPath = $imgFolder.substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/';
            #Check if path exists
            if (!is_dir($finalPath)) {
                #Create it recursively
                mkdir($finalPath, recursive: true);
            }
            #Check if file with hash name exists
            if (!file_exists($finalPath.$hash.'.png')) {
                #Copy the file to new path
                copy($imgFolder.$groupId.'.png', $finalPath.$hash.'.png');
            }
            return $hash;
        } catch (\Exception $e) {
            #Ensure that error is rethrown
            throw $e;
        } finally {
            #Remove temporary file
            @unlink($imgFolder . $groupId . '.png');
        }
    }
}
