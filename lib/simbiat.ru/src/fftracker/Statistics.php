<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\ArrayHelpers;
use Simbiat\Caching;
use Simbiat\Config\FFTracker;
use Simbiat\Cron;
use Simbiat\HomePage;
use Simbiat\LodestoneModules\Converters;

class Statistics
{
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function update(string $type = 'raw'): array
    {
        $data = [];
        #Sanitize type
        if (!in_array($type, ['raw', 'characters', 'groups', 'achievements', 'timelines', 'other', 'bugs'])) {
            $type = 'genetics';
        }
        #Create path if missing
        if (!is_dir(FFTracker::$statistics) && !mkdir(FFTracker::$statistics) && !is_dir(FFTracker::$statistics)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', FFTracker::$statistics));
        }
        $cachePath = FFTracker::$statistics.$type.'.json';
        #Get cache
        $json = (new Caching())->getArrayFromFile($cachePath);
        #Get Lodestone object for optimization
        $Lodestone = (new Converters);
        #Get ArrayHelpers object for optimization
        $ArrayHelpers = (new ArrayHelpers);
        #Get connection object for slight optimization
        $dbCon = HomePage::$dbController;
        $data['time'] = time();
        switch ($type) {
            case 'raw':
                $data['raw'] = $dbCon->selectAll(
                    'SELECT COUNT(*) as `count`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`genderid`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gcName` FROM `ffxiv__character`
                                LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid`=`ffxiv__clan`.`clanid`
                                LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid`=`ffxiv__city`.`cityid`
                                LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid`=`ffxiv__guardian`.`guardianid`
                                LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid`
                                LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gcId`=`ffxiv__grandcompany`.`gcId`
                                WHERE `ffxiv__character`.`clanid` IS NOT NULL GROUP BY `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`genderid`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`cityid`, `ffxiv__grandcompany_rank`.`gcId` ORDER BY `count` DESC;
                    ');
                break;
            case 'characters':
                #Jobs popularity
                $data['characters']['jobs'] = $dbCon->selectRow(
                    'SELECT
                                SUM(`Alchemist`) AS `Alchemist`,
                                SUM(`Armorer`) AS `Armorer`,
                                SUM(`Astrologian`) AS `Astrologian`,
                                SUM(`Bard`) AS `Bard`,
                                SUM(`BlackMage`) AS `Black Mage`,
                                SUM(`Blacksmith`) AS `Blacksmith`,
                                SUM(`BlueMage`) AS `Blue Mage`,
                                SUM(`Botanist`) AS `Botanist`,
                                SUM(`Carpenter`) AS `Carpenter`,
                                SUM(`Culinarian`) AS `Culinarian`,
                                SUM(`Dancer`) AS `Dancer`,
                                SUM(`DarkKnight`) AS `Dark Knight`,
                                SUM(`Dragoon`) AS `Dragoon`,
                                SUM(`Fisher`) AS `Fisher`,
                                SUM(`Goldsmith`) AS `Goldsmith`,
                                SUM(`Gunbreaker`) AS `Gunbreaker`,
                                SUM(`Leatherworker`) AS `Leatherworker`,
                                SUM(`Machinist`) AS `Machinist`,
                                SUM(`Miner`) AS `Miner`,
                                SUM(`Monk`) AS `Monk`,
                                SUM(`Ninja`) AS `Ninja`,
                                SUM(`Paladin`) AS `Paladin`,
                                SUM(`Pictomancer`) AS `Pictomancer`,
                                SUM(`Reaper`) AS `Reaper`,
                                SUM(`RedMage`) AS `Red Mage`,
                                SUM(`Sage`) AS `Sage`,
                                SUM(`Samurai`) AS `Samurai`,
                                SUM(`Scholar`) AS `Scholar`,
                                SUM(`Summoner`) AS `Summoner`,
                                SUM(`Viper`) AS `Viper`,
                                SUM(`Warrior`) AS `Warrior`,
                                SUM(`Weaver`) AS `Weaver`,
                                SUM(`WhiteMage`) AS `White Mage`
                            FROM `ffxiv__character`;'
                );
                #Sort array
                arsort($data['characters']['jobs']);
                #Most name changes
                $data['characters']['changes']['name'] = $dbCon->countUnique('ffxiv__character_names', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['changes']['name'], 'value', 'name');
                #Most reincarnation
                $data['characters']['changes']['clan'] = $dbCon->countUnique('ffxiv__character_clans', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['changes']['clan'], 'value', 'name');
                #Most servers
                $data['characters']['changes']['server'] = $dbCon->countUnique('ffxiv__character_servers', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['changes']['server'], 'value', 'name');
                #Most companies
                $data['characters']['groups']['Free Companies'] = $dbCon->countUnique('ffxiv__freecompany_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['groups']['Free Companies'], 'value', 'name');
                #Most PvP teams
                $data['characters']['groups']['PvP Teams'] = $dbCon->countUnique('ffxiv__pvpteam_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['groups']['PvP Teams'], 'value', 'name');
                #Most x-linkshells
                $data['characters']['groups']['Linkshells'] = $dbCon->countUnique('ffxiv__linkshell_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['groups']['Linkshells'], 'value', 'name');
                #Most linkshells
                $data['characters']['groups']['simLinkshells'] = $dbCon->countUnique('ffxiv__linkshell_character', 'characterid', '`ffxiv__linkshell_character`.`current`=1', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['groups']['simLinkshells'], 'value', 'name');
                #Groups affiliation
                $data['characters']['groups']['participation'] = $dbCon->SelectAll('
                        SELECT COUNT(*) as `count`,
                            (CASE
                                WHEN (`fc`=1 AND `pvp`=0 AND `ls`=0) THEN \'Free Company only\'
                                WHEN (`fc`=0 AND `pvp`=1 AND `ls`=0) THEN \'PvP Team only\'
                                WHEN (`fc`=0 AND `pvp`=0 AND `ls`=1) THEN \'Linkshell only\'
                                WHEN (`fc`=1 AND `pvp`=1 AND `ls`=0) THEN \'Free Company and PvP Team\'
                                WHEN (`fc`=1 AND `pvp`=0 AND `ls`=1) THEN \'Free Company and Linkshell\'
                                WHEN (`fc`=0 AND `pvp`=1 AND `ls`=1) THEN \'PvP Team and Linkshell\'
                                WHEN (`fc`=1 AND `pvp`=1 AND `ls`=1) THEN \'Free Company, PvP Team and Linkshell\'
                                ELSE \'No groups\'
                            END) AS `affiliation`
                        FROM (
                            SELECT `characterid`,
                                EXISTS(SELECT `characterid` FROM `ffxiv__freecompany_character` WHERE `characterid`=`main`.`characterid` AND `current`=1) as `fc`,
                                EXISTS(SELECT `characterid` FROM `ffxiv__pvpteam_character` WHERE `characterid`=`main`.`characterid` AND `current`=1) as `pvp`,
                                EXISTS(SELECT `characterid` FROM `ffxiv__linkshell_character` WHERE `characterid`=`main`.`characterid` AND `current`=1) as `ls`
                            FROM `ffxiv__character` AS `main` WHERE `deleted` IS NULL
                        ) as `temp`
                        GROUP BY `affiliation`;
                    ');
                #Get characters with most PvP matches. Using regular SQL since we do not count unique values, but rather use the regular column values
                $data['characters']['most_pvp'] = $dbCon->SelectAll('SELECT `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`, `pvp_matches` AS `count` FROM `ffxiv__character` ORDER BY `ffxiv__character`.`pvp_matches` DESC LIMIT 20');
                #Characters
                $data['servers']['characters'] = $dbCon->countUnique('ffxiv__character', 'serverid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__character`.`genderid`, `ffxiv__server`.`server`', 'DESC', 0, ['`ffxiv__character`.`genderid`']);
                break;
            case 'groups':
                #Get most popular estate locations
                $data['freecompany']['estate'] = ArrayHelpers::topAndBottom($dbCon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`, `ffxiv__estate`.`plot`, CONCAT(`ffxiv__estate`.`area`, \', plot \', `ffxiv__estate`.`plot`)'), 20);
                #Get statistics by activity time
                $data['freecompany']['active'] = $dbCon->sumUnique('ffxiv__freecompany', 'activeid', [1, 2, 3], ['Always', 'Weekdays', 'Weekends'], '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__timeactive', 'INNER', 'activeid', 'IF(`ffxiv__freecompany`.`recruitment`=1, \'Recruiting\', \'Not recruiting\') AS `recruiting`');
                #Get statistics by activities
                $data['freecompany']['activities'] = $dbCon->SelectRow('SELECT  SUM(`Role-playing`)/COUNT(`freecompanyid`)*100 AS `Role-playing`, SUM(`Leveling`)/COUNT(`freecompanyid`)*100 AS `Leveling`, SUM(`Casual`)/COUNT(`freecompanyid`)*100 AS `Casual`, SUM(`Hardcore`)/COUNT(`freecompanyid`)*100 AS `Hardcore`, SUM(`Dungeons`)/COUNT(`freecompanyid`)*100 AS `Dungeons`, SUM(`Guildhests`)/COUNT(`freecompanyid`)*100 AS `Guildhests`, SUM(`Trials`)/COUNT(`freecompanyid`)*100 AS `Trials`, SUM(`Raids`)/COUNT(`freecompanyid`)*100 AS `Raids`, SUM(`PvP`)/COUNT(`freecompanyid`)*100 AS `PvP` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL');
                arsort($data['freecompany']['activities']);
                #Get statistics by job search
                $data['freecompany']['jobDemand'] = $dbCon->SelectRow('SELECT SUM(`Tank`)/COUNT(`freecompanyid`)*100 AS `Tank`, SUM(`Healer`)/COUNT(`freecompanyid`)*100 AS `Healer`, SUM(`DPS`)/COUNT(`freecompanyid`)*100 AS `DPS`, SUM(`Crafter`)/COUNT(`freecompanyid`)*100 AS `Crafter`, SUM(`Gatherer`)/COUNT(`freecompanyid`)*100 AS `Gatherer` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL');
                arsort($data['freecompany']['jobDemand']);
                #Get statistics for grand companies for characters
                $data['gc_characters'] = $dbCon->selectAll(
                    'SELECT COUNT(*) as `count`, `ffxiv__character`.`genderid`, `ffxiv__grandcompany`.`gcName`, `ffxiv__grandcompany_rank`.`gc_rank` FROM `ffxiv__character`
                                LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid`
                                LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gcId`=`ffxiv__grandcompany`.`gcId`
                                WHERE `ffxiv__character`.`gcrankid` IS NOT NULL GROUP BY `ffxiv__character`.`genderid`, `ffxiv__grandcompany`.`gcName`, `ffxiv__grandcompany_rank`.`gc_rank` ORDER BY `count` DESC;
                    ');
                #Get statistics for grand companies for free companies
                $data['gc_companies'] = $dbCon->countUnique('ffxiv__freecompany', 'grandcompanyid', '', 'ffxiv__grandcompany', 'INNER', 'gcId', '`ffxiv__grandcompany`.`gcName`');
                #City by free company
                $data['cities']['free_company'] = $dbCon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`');
                #Grand companies distribution (free companies)
                $data['cities']['gc_fc'] = $dbCon->SelectAll('SELECT `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gcName` AS `value`, COUNT(`ffxiv__freecompany`.`freecompanyid`) AS `count` FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estateid`=`ffxiv__estate`.`estateid` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany_rank`.`gcrankid` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany`.`gcId` WHERE `ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__grandcompany`.`gcName` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                #Free companies
                $data['servers']['Free Companies'] = $dbCon->countUnique('ffxiv__freecompany', 'serverid', '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`');
                #Linkshells
                $data['servers']['Linkshells'] = $dbCon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 0 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`');
                #Crossworld linkshells
                $data['servers']['crossworldlinkshell'] = $dbCon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 1 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                #PvP teams
                $data['servers']['pvpteam'] = $dbCon->countUnique('ffxiv__pvpteam', 'datacenterid', '`ffxiv__pvpteam`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                #Get most popular crests for companies
                $data['freecompany']['crests'] = Entity::cleanCrestResults($dbCon->selectAll('SELECT COUNT(*) AS `count`, `crest_part_1`, `crest_part_2`, `crest_part_3` FROM `ffxiv__freecompany` GROUP BY `crest_part_1`, `crest_part_2`, `crest_part_3` ORDER BY `count` DESC LIMIT 20;'));
                #Get most popular crests for PvP Teams
                $data['pvpteam']['crests'] = Entity::cleanCrestResults($dbCon->selectAll('SELECT COUNT(*) AS `count`, `crest_part_1`, `crest_part_2`, `crest_part_3` FROM `ffxiv__pvpteam` GROUP BY `crest_part_1`, `crest_part_2`, `crest_part_3` ORDER BY `count` DESC LIMIT 20;'));
                break;
            case 'achievements':
                #Get achievements statistics
                $data['achievements'] = $dbCon->SelectAll('SELECT \'achievement\' as `type`, `ffxiv__achievement`.`category`, `ffxiv__achievement`.`achievementid` AS `id`, `ffxiv__achievement`.`icon`, `ffxiv__achievement`.`name` AS `name`, `count` FROM (SELECT `ffxiv__character_achievement`.`achievementid`, count(`ffxiv__character_achievement`.`achievementid`) AS `count` from `ffxiv__character_achievement` GROUP BY `ffxiv__character_achievement`.`achievementid` ORDER BY `count`) `tempresult` INNER JOIN `ffxiv__achievement` ON `tempresult`.`achievementid`=`ffxiv__achievement`.`achievementid` WHERE `ffxiv__achievement`.`category` IS NOT NULL ORDER BY `count`');
                #Split achievements by categories
                $data['achievements'] = ArrayHelpers::splitByKey($data['achievements'], 'category');
                #Get only top 20 for each category
                foreach ($data['achievements'] as $key=>$category) {
                    $data['achievements'][$key] = array_slice($category, 0, 20);
                }
                #Get most and least popular titles
                $data['titles'] = ArrayHelpers::topAndBottom($dbCon->selectAll('SELECT COUNT(*) as `count`, `ffxiv__achievement`.`title`, `ffxiv__achievement`.`achievementid` FROM `ffxiv__character` LEFT JOIN `ffxiv__achievement` ON `ffxiv__achievement`.`achievementid`=`ffxiv__character`.`titleid` WHERE `ffxiv__character`.`titleid` IS NOT NULL GROUP BY `titleid` ORDER BY `count` DESC;'), 20);
                break;
            case 'timelines':
                #Get namedays timeline. Using custom SQL, since need special order by `namedayid`, instead of by `count`
                $data['namedays'] = $dbCon->SelectAll('SELECT `ffxiv__nameday`.`nameday` AS `value`, COUNT(`ffxiv__character`.`namedayid`) AS `count` FROM `ffxiv__character` INNER JOIN `ffxiv__nameday` ON `ffxiv__character`.`namedayid`=`ffxiv__nameday`.`namedayid` GROUP BY `ffxiv__nameday`.`namedayid` ORDER BY `ffxiv__nameday`.`namedayid`');
                #Timeline of entities formation, updates, etc.
                $data['timelines'] = $dbCon->SelectAll(
                    'SELECT
                                DATE(`updated`) AS `date`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__character\' THEN `registered` END) AS `characters_registered`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__character\' THEN `deleted` END) AS `characters_deleted`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__character\' THEN `updated` END) AS `characters_updated`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__freecompany\' THEN `registered` END) AS `free_companies_registered`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__freecompany\' THEN `deleted` END) AS `free_companies_deleted`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__freecompany\' THEN `updated` END) AS `free_companies_updated`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__freecompany\' THEN `formed` END) AS `free_companies_formed`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__linkshell\' THEN `registered` END) AS `linkshells_registered`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__linkshell\' THEN `deleted` END) AS `linkshells_deleted`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__linkshell\' THEN `updated` END) AS `linkshells_updated`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__linkshell\' THEN `formed` END) AS `linkshells_formed`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__pvpteam\' THEN `registered` END) AS `pvp_teams_registered`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__pvpteam\' THEN `deleted` END) AS `pvp_teams_deleted`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__pvpteam\' THEN `updated` END) AS `pvp_teams_updated`,
                                COUNT(CASE WHEN `table_name` = \'ffxiv__pvpteam\' THEN `formed` END) AS `pvp_teams_formed`
                            FROM (
                                SELECT \'ffxiv__character\' AS `table_name`, `updated`, `registered`, `deleted`, NULL AS `formed` FROM `ffxiv__character`
                                UNION ALL
                                SELECT \'ffxiv__freecompany\' AS `table_name`, `updated`, `registered`, `deleted`, `formed` FROM `ffxiv__freecompany`
                                UNION ALL
                                SELECT \'ffxiv__linkshell\' AS `table_name`, `updated`, `registered`, `deleted`, `formed` FROM `ffxiv__linkshell`
                                UNION ALL
                                SELECT \'ffxiv__pvpteam\' AS `table_name`, `updated`, `registered`, `deleted`, `formed` FROM `ffxiv__pvpteam`
                            ) AS `all_data`
                            GROUP BY `date` DESC LIMIT 5000;'
                );
                break;
            case 'bugs':
                #Characters with no clan/race
                $data['bugs']['noClan'] = $dbCon->SelectAll('SELECT `characterid` AS `id`, `name`, `avatar` AS `icon`, \'character\' AS `type` FROM `ffxiv__character` WHERE `clanid` IS NULL AND `deleted` IS NULL ORDER BY `updated`, `name` LIMIT 100;');
                #Groups with no members
                $data['bugs']['noMembers'] = Entity::cleanCrestResults($dbCon->SelectAll(
                    'SELECT `freecompanyid` AS `id`, `name`, \'freecompany\' AS `type`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `grandcompanyid` FROM `ffxiv__freecompany` as `fc` WHERE `deleted` IS NULL AND `freecompanyid` NOT IN (SELECT `freecompanyid` FROM `ffxiv__freecompany_character` WHERE `freecompanyid`=`fc`.`freecompanyid` AND `current`=1)
                        UNION
                        SELECT `linkshellid` AS `id`, `name`, IF(`crossworld`=1, \'crossworldlinkshell\', \'linkshell\') AS `type`, null as `crest_part_1`, null as `crest_part_2`, null as `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__linkshell` as `ls` WHERE `deleted` IS NULL AND `linkshellid` NOT IN (SELECT `linkshellid` FROM `ffxiv__linkshell_character` WHERE `linkshellid`=`ls`.`linkshellid` AND `current`=1)
                        UNION
                        SELECT `pvpteamid` AS `id`, `name`, \'pvpteam\' AS `type`, `crest_part_1`, `crest_part_2`, `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__pvpteam` as `pvp` WHERE `deleted` IS NULL AND `pvpteamid` NOT IN (SELECT `pvpteamid` FROM `ffxiv__pvpteam_character` WHERE `pvpteamid`=`pvp`.`pvpteamid` AND `current`=1)
                        ORDER BY `name`;'
                ));
                #Get entities with duplicate names
                $duplicateNames = $dbCon->SelectAll(
                    'SELECT \'character\' AS `type`, `characterid` AS `id`, `name`, `avatar` as `icon`, `userid`, NULL as `crest_part_1`, NULL as `crest_part_2`, NULL as `crest_part_3`, `server`, `datacenter` FROM `ffxiv__character` as `chartable` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`chartable`.`serverid` WHERE `deleted` is NULL AND (SELECT COUNT(*) FROM `ffxiv__character` WHERE `ffxiv__character`.`name`=`chartable`.`name` AND `ffxiv__character`.`serverid`=`chartable`.`serverid` AND `deleted` is NULL)>1
                            UNION ALL
                            SELECT \'freecompany\' AS `type`, `freecompanyid` AS `id`, `name`, NULL as `icon`, NULL as `userid`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `server`, `datacenter`  FROM `ffxiv__freecompany` as `fctable` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`fctable`.`serverid` WHERE `deleted` is NULL AND (SELECT COUNT(*) FROM `ffxiv__freecompany` WHERE `ffxiv__freecompany`.`name`= BINARY `fctable`.`name` AND `ffxiv__freecompany`.`serverid`=`fctable`.`serverid` AND `deleted` is NULL)>1
                            UNION ALL
                            SELECT \'pvpteam\' AS `type`, `pvpteamid` AS `id`, `name`, NULL as `icon`, NULL as `userid`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `server`, `datacenter`  FROM `ffxiv__pvpteam` as `pvptable` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`pvptable`.`datacenterid` WHERE `deleted` is NULL AND (SELECT COUNT(*) FROM `ffxiv__pvpteam` WHERE `ffxiv__pvpteam`.`name`= BINARY `pvptable`.`name` AND `ffxiv__pvpteam`.`datacenterid`=`pvptable`.`datacenterid` AND `deleted` is NULL)>1
                            UNION ALL
                            SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `linkshellid` AS `id`, `name`, NULL as `icon`, NULL as `userid`, NULL as `crest_part_1`, NULL as `crest_part_2`, NULL as `crest_part_3`, `server`, `datacenter`  FROM `ffxiv__linkshell` as `lstable` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`lstable`.`serverid` WHERE `deleted` is NULL AND (SELECT COUNT(*) FROM `ffxiv__linkshell` WHERE `ffxiv__linkshell`.`name`= BINARY `lstable`.`name` AND `ffxiv__linkshell`.`serverid`=`lstable`.`serverid` AND `deleted` is NULL AND `ffxiv__linkshell`.`crossworld`=`lstable`.`crossworld`)>1;'
                );
                #Split by entity type
                $data['bugs']['duplicateNames'] = ArrayHelpers::splitByKey($duplicateNames, 'type', keepKey: true);
                foreach ($data['bugs']['duplicateNames'] as $entityType=>$namesData) {
                    #Split by server/datacenter
                    $data['bugs']['duplicateNames'][$entityType] = ArrayHelpers::splitByKey($namesData, (in_array($entityType, ['pvpteam', 'crosswordlinkshell']) ? 'datacenter' : 'server'));
                    foreach ($data['bugs']['duplicateNames'][$entityType] as $server=>$serverData) {
                        #Split by name
                        $data['bugs']['duplicateNames'][$entityType][$server] = ArrayHelpers::splitByKey($serverData, 'name', keepKey: true, caseInsensitive: true);
                        foreach ($data['bugs']['duplicateNames'][$entityType][$server] as $name=>$nameData) {
                            if (in_array($entityType, ['freecompany', 'pvpteam'])) {
                                $nameData = Entity::cleanCrestResults($nameData);
                            }
                            foreach ($nameData as $key=>$duplicates) {
                                #Clean up
                                unset($duplicates['crest_part_1'], $duplicates['crest_part_2'], $duplicates['crest_part_3']);
                                if (in_array($entityType, ['crossworldlinkshell', 'pvpteam'])) {
                                    unset($duplicates['server']);
                                } else {
                                    unset($duplicates['datacenter']);
                                }
                                #Update array
                                $data['bugs']['duplicateNames'][$entityType][$server][$name][$key] = $duplicates;
                            }
                        }
                    }
                }
                break;
            case 'other':
                #Communities
                $data['other']['communities'] = $dbCon->SelectAll('
                        SELECT `type`, IF(`has_community`=0, \'No community\', \'Community\') AS `value`, count(`has_community`) AS `count` FROM (
                            SELECT \'Free Company\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `type`, `value`
                    ');
                #Deleted entities statistics
                $data['other']['entities'] = $dbCon->SelectAll('
                        SELECT CONCAT(IF(`deleted`=0, \'Active\', \'Deleted\'), \' \', `type`) AS `value`, count(`deleted`) AS `count` FROM (
                            SELECT \'Character\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__character`
                            UNION ALL
                            SELECT \'Free Company\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__freecompany`
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__pvpteam`
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__linkshell`
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `count` DESC
                    ');
                break;
        }
        unset($dbCon, $ArrayHelpers, $Lodestone);
        #Attempt to write to cache
        file_put_contents($cachePath, json_encode(array_merge($json, $data), JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
        if ($type === 'bugs') {
            #These may be because of temporary issues on parser or Lodestone side, so schedule them for update
            $cron = (new Cron);
            foreach ($data['bugs']['noClan'] as $character) {
                $cron->add('ffUpdateEntity', [(string)$character['id'], 'character'], message: 'Updating character with ID '.$character['id']);
            }
            foreach ($data['bugs']['noMembers'] as $group) {
                $cron->add('ffUpdateEntity', [(string)$group['id'], $group['type']], message: 'Updating group with ID '.$group['id']);
            }
            foreach ($data['bugs']['duplicateNames'] as $servers) {
                foreach ($servers as $server) {
                    foreach ($server as $names) {
                        foreach ($names as $duplicate) {
                            $cron->add('ffUpdateEntity', [(string)$duplicate['id'], $duplicate['type']], message: 'Updating entity with ID '.$duplicate['id']);
                        }
                    }
                }
            }
        }
        return $data;
    }
}
