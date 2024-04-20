<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\ArrayHelpers;
use Simbiat\Caching;
use Simbiat\Config\FFTracker;
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
        if (!in_array($type, ['raw', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'])) {
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
        switch ($type) {
            case 'raw':
                $data['raw']['data'] = $dbCon->selectAll(
                    'SELECT COUNT(*) as `count`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`genderid`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gcName` FROM `ffxiv__character`
                                LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid`=`ffxiv__clan`.`clanid`
                                LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid`=`ffxiv__city`.`cityid`
                                LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid`=`ffxiv__guardian`.`guardianid`
                                LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid`
                                LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gcId`=`ffxiv__grandcompany`.`gcId`
                                WHERE `ffxiv__character`.`clanid` IS NOT NULL GROUP BY `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`genderid`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`cityid`, `ffxiv__grandcompany_rank`.`gcId` ORDER BY `count` DESC;
                    ');
                $data['raw']['time'] = time();
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
                $data['characters']['groups']['linkshell'] = $dbCon->countUnique('ffxiv__linkshell_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true);
                ArrayHelpers::renameColumn($data['characters']['groups']['linkshell'], 'value', 'name');
                #Groups affiliation
                $data['characters']['groups']['participation'] = $dbCon->SelectAll('
                        SELECT `affiliation` AS `value`, COUNT(`affiliation`) AS `count`FROM (
                            SELECT
                                (CASE
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NOT NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company only\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'PvP Team only\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Linkshell only\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NOT NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company and PvP Team\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NOT NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company and Linkshell\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'PvP Team and Linkshell\'
                                    WHEN (`ffxiv__freecompany_character`.`freecompanyid` IS NOT NULL AND `ffxiv__pvpteam_character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company, PvP Team and Linkshell\'
                                    ELSE \'No groups\'
                                END) AS `affiliation`
                            FROM `ffxiv__character`
                            LEFT JOIN `ffxiv__linkshell_character` ON `ffxiv__linkshell_character`.`characterid` = `ffxiv__character`.`characterid`
                            LEFT JOIN `ffxiv__freecompany_character` ON `ffxiv__freecompany_character`.`characterid` = `ffxiv__character`.`characterid`
                            LEFT JOIN `ffxiv__pvpteam_character` ON `ffxiv__pvpteam_character`.`characterid` = `ffxiv__character`.`characterid`
                            WHERE `ffxiv__character`.`deleted` IS NULL AND (`ffxiv__linkshell_character`.`current`=1 OR `ffxiv__freecompany_character`.`current`=1 OR `ffxiv__pvpteam_character`.`current`=1) GROUP BY `ffxiv__character`.`characterid`) `tempresult`
                        GROUP BY `affiliation` ORDER BY `count` DESC;
                    ');
                #Move count of loners to separate key
                foreach ($data['characters']['groups']['participation'] as $key=>$row) {
                    if ($row['value'] === 'No groups') {
                        $data['characters']['no_groups'] = $row['count'];
                        unset($data['characters']['groups']['participation'][$key]);
                        break;
                    }
                }
                #Get characters with most PvP matches. Using regular SQL since we do not count unique values, but rather use the regular column values
                $data['characters']['most_pvp'] = $dbCon->SelectAll('SELECT `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`, `pvp_matches` AS `count` FROM `ffxiv__character` ORDER BY `ffxiv__character`.`pvp_matches` DESC LIMIT 20');
                break;
            case 'freecompanies':
                #Get most popular estate locations
                $data['freecompany']['estate'] = ArrayHelpers::topAndBottom($dbCon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`, `ffxiv__estate`.`plot`, CONCAT(`ffxiv__estate`.`area`, \', plot \', `ffxiv__estate`.`plot`)'), 20);
                #Get statistics by activity time
                $data['freecompany']['active'] = $dbCon->sumUnique('ffxiv__freecompany', 'activeid', [1, 2, 3], ['Always', 'Weekdays', 'Weekends'], '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__timeactive', 'INNER', 'activeid', 'IF(`ffxiv__freecompany`.`recruitment`=1, \'Recruiting\', \'Not recruiting\') AS `recruiting`');
                #Get statistics by activities
                $data['freecompany']['activities'] = $dbCon->SelectRow('SELECT SUM(`Tank`)/COUNT(`freecompanyid`)*100 AS `Tank`, SUM(`Healer`)/COUNT(`freecompanyid`)*100 AS `Healer`, SUM(`DPS`)/COUNT(`freecompanyid`)*100 AS `DPS`, SUM(`Crafter`)/COUNT(`freecompanyid`)*100 AS `Crafter`, SUM(`Gatherer`)/COUNT(`freecompanyid`)*100 AS `Gatherer`, SUM(`Role-playing`)/COUNT(`freecompanyid`)*100 AS `Role-playing`, SUM(`Leveling`)/COUNT(`freecompanyid`)*100 AS `Leveling`, SUM(`Casual`)/COUNT(`freecompanyid`)*100 AS `Casual`, SUM(`Hardcore`)/COUNT(`freecompanyid`)*100 AS `Hardcore`, SUM(`Dungeons`)/COUNT(`freecompanyid`)*100 AS `Dungeons`, SUM(`Guildhests`)/COUNT(`freecompanyid`)*100 AS `Guildhests`, SUM(`Trials`)/COUNT(`freecompanyid`)*100 AS `Trials`, SUM(`Raids`)/COUNT(`freecompanyid`)*100 AS `Raids`, SUM(`PvP`)/COUNT(`freecompanyid`)*100 AS `PvP` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL');
                #Get statistics by monthly ranks
                $data['freecompany']['ranking']['monthly'] = Entity::cleanCrestResults($dbCon->SelectAll('SELECT `tempresult`.*, `ffxiv__freecompany`.`name`, `ffxiv__freecompany`.`crest_part_1`, `ffxiv__freecompany`.`crest_part_2`, `ffxiv__freecompany`.`crest_part_3`, `ffxiv__freecompany`.`grandcompanyid`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`monthly`)*100 AS `ratio` FROM `ffxiv__freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `ffxiv__freecompany_ranking` `sub`)) `tempresult` INNER JOIN `ffxiv__freecompany` ON `ffxiv__freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC'));
                if (count($data['freecompany']['ranking']['monthly']) > 1) {
                    $data['freecompany']['ranking']['monthly'] = ArrayHelpers::topAndBottom($data['freecompany']['ranking']['monthly'], 20);
                } else {
                    $data['freecompany']['ranking']['monthly'] = [];
                }
                #Get statistics by weekly ranks
                $data['freecompany']['ranking']['weekly'] = Entity::cleanCrestResults($dbCon->SelectAll('SELECT `tempresult`.*, `ffxiv__freecompany`.`name`, `ffxiv__freecompany`.`crest_part_1`, `ffxiv__freecompany`.`crest_part_2`, `ffxiv__freecompany`.`crest_part_3`, `ffxiv__freecompany`.`grandcompanyid`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`weekly`)*100 AS `ratio` FROM `ffxiv__freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `ffxiv__freecompany_ranking` `sub`)) `tempresult` INNER JOIN `ffxiv__freecompany` ON `ffxiv__freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC'));
                if (count($data['freecompany']['ranking']['weekly']) > 1) {
                    $data['freecompany']['ranking']['weekly'] = ArrayHelpers::topAndBottom($data['freecompany']['ranking']['weekly'], 20);
                } else {
                    $data['freecompany']['ranking']['weekly'] = [];
                }
                #Get most popular crests
                #$data['freecompany']['crests'] = $dbCon->countUnique('ffxiv__freecompany', 'crest', '`ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                break;
            case 'cities':
                #City by free company
                $data['cities']['free_company'] = $dbCon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`');
                #Add colors to cities
                foreach ($data['cities']['free_company'] as $key=>$city) {
                    $data['cities']['free_company'][$key]['color'] = $Lodestone->colorCities($city['value']);
                }
                #Grand companies distribution (free companies)
                $data['cities']['gc_fc'] = $dbCon->SelectAll('SELECT `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gcName` AS `value`, COUNT(`ffxiv__freecompany`.`freecompanyid`) AS `count` FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estateid`=`ffxiv__estate`.`estateid` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany_rank`.`gcrankid` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany`.`gcId` WHERE `ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__grandcompany`.`gcName` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                #Add colors to companies
                foreach ($data['cities']['gc_fc'] as $key=>$company) {
                    $data['cities']['gc_fc'][$key]['color'] = $Lodestone->colorGC((string)$company['value']);
                }
                $data['cities']['gc_fc'] = ArrayHelpers::splitByKey($data['cities']['gc_fc'], 'city', [], []);
                break;
            case 'grandcompanies':
                #Get statistics for grand companies
                $data['grand_companies']['population'] = $dbCon->countUnique('ffxiv__character', 'gcrankid', '`ffxiv__character`.`deleted` IS NULL AND `ffxiv__character`.`gcrankid` IS NOT NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__character`.`genderid`, `ffxiv__grandcompany_rank`.`gcId`', 'DESC', 0, ['`ffxiv__character`.`genderid`']);
                #Add colors to companies
                foreach ($data['grand_companies']['population'] as $key=>$company) {
                    $data['grand_companies']['population'][$key]['color'] = $Lodestone->colorGC((string)$company['value']);
                }
                #Split companies by gender
                $data['grand_companies']['population'] = ArrayHelpers::splitByKey($data['grand_companies']['population'], 'genderid', ['female', 'male'], [0, 1]);
                $data['grand_companies']['population']['free_company'] = $dbCon->countUnique('ffxiv__freecompany', 'grandcompanyid', '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__grandcompany_rank`.`gcId`');
                #Add colors to cities
                foreach ($data['grand_companies']['population']['free_company'] as $key=>$company) {
                    $data['grand_companies']['population']['free_company'][$key]['color'] = $Lodestone->colorGC((string)$company['value']);
                }
                #Grand companies ranks
                $data['grand_companies']['ranks'] = ArrayHelpers::splitByKey($dbCon->countUnique('ffxiv__character', 'gcrankid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__character`.`genderid`, `ffxiv__grandcompany_rank`.`gcId`, `ffxiv__grandcompany_rank`.`gc_rank`', 'DESC', 0, ['`ffxiv__character`.`genderid`', '`ffxiv__grandcompany_rank`.`gcId`']), 'gcId', [], []);
                #Split by gender
                foreach ($data['grand_companies']['ranks'] as $key=>$company) {
                    $data['grand_companies']['ranks'][$key] = ArrayHelpers::splitByKey($company, 'genderid', ['female', 'male'], [0, 1]);
                }
                break;
            case 'servers':
                #Characters
                $data['servers']['characters'] = ArrayHelpers::splitByKey($dbCon->countUnique('ffxiv__character', 'serverid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__character`.`genderid`, `ffxiv__server`.`server`', 'DESC', 0, ['`ffxiv__character`.`genderid`']), 'genderid', ['female', 'male'], [0, 1]);
                $data['servers']['female population'] = ArrayHelpers::topAndBottom($data['servers']['characters']['female'], 20);
                $data['servers']['male population'] = ArrayHelpers::topAndBottom($data['servers']['characters']['male'], 20);
                unset($data['servers']['characters']);
                #Free companies
                $data['servers']['Free Companies'] = ArrayHelpers::topAndBottom($dbCon->countUnique('ffxiv__freecompany', 'serverid', '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`'), 20);
                #Linkshells
                $data['servers']['Linkshells'] = ArrayHelpers::topAndBottom($dbCon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 0 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`'), 20);
                #Crossworld linkshells
                $data['servers']['crossworldlinkshell'] = $dbCon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 1 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                #PvP teams
                $data['servers']['pvpteam'] = $dbCon->countUnique('ffxiv__pvpteam', 'datacenterid', '`ffxiv__pvpteam`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                break;
            case 'achievements':
                #Get achievements statistics
                $data['other']['achievements'] = $dbCon->SelectAll('SELECT \'achievement\' as `type`, `ffxiv__achievement`.`category`, `ffxiv__achievement`.`achievementid` AS `id`, `ffxiv__achievement`.`icon`, `ffxiv__achievement`.`name` AS `name`, `count` FROM (SELECT `ffxiv__character_achievement`.`achievementid`, count(`ffxiv__character_achievement`.`achievementid`) AS `count` from `ffxiv__character_achievement` GROUP BY `ffxiv__character_achievement`.`achievementid` ORDER BY `count`) `tempresult` INNER JOIN `ffxiv__achievement` ON `tempresult`.`achievementid`=`ffxiv__achievement`.`achievementid` WHERE `ffxiv__achievement`.`category` IS NOT NULL ORDER BY `count`');
                #Split achievements by categories
                $data['other']['achievements'] = ArrayHelpers::splitByKey($data['other']['achievements'], 'category', [], []);
                #Get only top 20 for each category
                foreach ($data['other']['achievements'] as $key=>$category) {
                    $data['other']['achievements'][$key] = array_slice($category, 0, 20);
                }
                break;
            case 'timelines':
                #Get namedays timeline. Using custom SQL, since need special order by `namedayid`, instead of by `count`
                #PHPStorm complains about `namedayid` for no reason
                /** @noinspection SqlAggregates */
                $data['timelines']['nameday'] = $dbCon->SelectAll('SELECT `ffxiv__nameday`.`nameday` AS `value`, COUNT(`ffxiv__character`.`namedayid`) AS `count` FROM `ffxiv__character` INNER JOIN `ffxiv__nameday` ON `ffxiv__character`.`namedayid`=`ffxiv__nameday`.`namedayid` GROUP BY `value` ORDER BY `ffxiv__nameday`.`namedayid`');
                #Timeline of groups formations
                $data['timelines']['formed'] = $dbCon->SelectAll(
                    'SELECT `formed` AS `value`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `formed`, COUNT(`formed`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, COUNT(`formed`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` WHERE `formed` IS NOT NULL GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`formed`) AS `pvpteams` FROM `ffxiv__pvpteam` WHERE `formed` IS NOT NULL GROUP BY `formed`
                        ) `tempResults`
                        GROUP BY `formed` ORDER BY `formed`'
                );
                #Timeline of entities registration
                $data['timelines']['registered'] = $dbCon->SelectAll(
                    'SELECT `registered` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `registered`, COUNT(`registered`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__character` GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, COUNT(`registered`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`registered`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`registered`) AS `pvpteams` FROM `ffxiv__pvpteam` GROUP BY `registered`
                        ) `tempResults`
                        GROUP BY `registered` ORDER BY `registered` '
                );
                #Timeline of entities deletion
                $data['timelines']['deleted'] = $dbCon->SelectAll(
                    'SELECT `deleted` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `deleted`, COUNT(`deleted`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__character` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, COUNT(`deleted`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`deleted`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`deleted`) AS `pvpteams` FROM `ffxiv__pvpteam` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                        ) `tempResults`
                        GROUP BY `deleted` ORDER BY `deleted` '
                );
                break;
            case 'bugs':
                #Characters with no clan/race
                $data['bugs']['noClan'] = $dbCon->SelectAll('SELECT `characterid` AS `id`, `name`, `avatar` AS `icon`, \'character\' AS `type` FROM `ffxiv__character` WHERE `clanid` IS NULL AND `deleted` IS NULL ORDER BY `updated`, `name` LIMIT 100;');
                #Groups with no members
                $data['bugs']['noMembers'] = Entity::cleanCrestResults($dbCon->SelectAll(
                    'SELECT `freecompanyid` AS `id`, `name`, \'freecompany\' AS `type`, `ffxiv__freecompany`.`crest_part_1`, `ffxiv__freecompany`.`crest_part_2`, `ffxiv__freecompany`.`crest_part_3`, `ffxiv__freecompany`.`grandcompanyid` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL AND `freecompanyid` NOT IN (SELECT `freecompanyid` FROM `ffxiv__freecompany_character`)
                        UNION
                        SELECT `linkshellid` AS `id`, `name`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, null as `crest_part_1`, null as `crest_part_2`, null as `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `linkshellid` NOT IN (SELECT `linkshellid` FROM `ffxiv__linkshell_character`)
                        UNION
                        SELECT `pvpteamid` AS `id`, `name`, \'pvpteam\' AS `type`, `crest_part_1`, `crest_part_2`, `crest_part_3`, null as `grandcompanyid` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL AND `pvpteamid` NOT IN (SELECT `pvpteamid` FROM `ffxiv__pvpteam_character`)
                        ORDER BY `name`;'
                ));
                break;
            case 'other':
                #Communities
                $data['other']['communities'] = ArrayHelpers::splitByKey($dbCon->SelectAll('
                        SELECT `type`, IF(`has_community`=0, \'No community\', \'Community\') AS `value`, count(`has_community`) AS `count` FROM (
                            SELECT \'Free Company\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `count` DESC
                    '), 'type', [], []);
                #Sanitize results
                foreach ($data['other']['communities'] as $key=>$row) {
                    if (!empty($row[0])) {
                        $data['other']['communities'][$key][$row[0]['value']] = $row[0]['count'];
                    }
                    if (!empty($row[1])) {
                        $data['other']['communities'][$key][$row[1]['value']] = $row[1]['count'];
                    }
                    if (empty($data['other']['communities'][$key]['Community'])) {
                        $data['other']['communities'][$key]['Community'] = '0';
                    }
                    if (empty($data['other']['communities'][$key]['No community'])) {
                        $data['other']['communities'][$key]['No community'] = '0';
                    }
                    unset($data['other']['communities'][$key][0], $data['other']['communities'][$key][1]);
                }
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
                #$data['pvpteam']['crests'] = $dbCon->countUnique('ffxiv__pvpteam', 'crest', '`ffxiv__pvpteam`.`deleted` IS NULL AND `ffxiv__pvpteam`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                break;
        }
        unset($dbCon, $ArrayHelpers, $Lodestone);
        #Attempt to write to cache
        file_put_contents($cachePath, json_encode(array_merge($json, $data), JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
        return $data;
    }
}
