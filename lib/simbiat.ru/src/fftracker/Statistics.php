<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\ArrayHelpers;
use Simbiat\Database\Controller;
use Simbiat\HomePage;
use Simbiat\LodestoneModules\Converters;

class Statistics
{
    private const dbPrefix = 'ffxiv__';
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function get(string $type = 'genetics', string $cachePath = '', bool $nocache = false): array
    {
        $data = [];
        #Sanitize type
        $type = strtolower($type);
        if (!in_array($type, ['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'])) {
            $type = 'genetics';
        }
        #Sanitize cachePath
        if (empty($cachePath)) {
            #Create path if missing
            if (!is_dir($GLOBALS['siteconfig']['ffstatitics_cache'])) {
                mkdir($GLOBALS['siteconfig']['ffstatitics_cache']);
            }
            $cachePath = $GLOBALS['siteconfig']['ffstatitics_cache'].$type.'.json';
        }
        #Check if cache file exists
        if (is_file($cachePath)) {
            #Read the cache
            $json = file_get_contents($cachePath);
            if ($json !== false && $json !== '') {
                $json = json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
                if ($json !== NULL) {
                    if (!is_array($json)) {
                        $json = [];
                    }
                } else {
                    $json = [];
                }
            } else {
                $json = [];
            }
        } else {
            $json = [];
        }
        #Get Lodestone object for optimization
        $Lodestone = (new Converters);
        #Get ArrayHelpers object for optimization
        $ArrayHelpers = (new ArrayHelpers);
        #Get connection object for slight optimization
        $dbCon = HomePage::$dbController;
        switch ($type) {
            case 'genetics':
                #Get statistics by clan
                if (!$nocache && !empty($json['characters']['clans'])) {
                    $data['characters']['clans'] = $json['characters']['clans'];
                } else {
                    $data['characters']['clans'] = $ArrayHelpers->splitByKey($dbCon->countUnique(''.self::dbPrefix.'character', 'clanid', '`'.self::dbPrefix.'character`.`deleted` IS NULL', ''.self::dbPrefix.'clan', 'INNER', 'clanid', '`'.self::dbPrefix.'character`.`genderid`, CONCAT(`'.self::dbPrefix.'clan`.`race`, \' of \', `'.self::dbPrefix.'clan`.`clan`, \' clan\')', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`']), 'genderid', ['female', 'male'], [0, 1]);
                }
                #Clan distribution by city
                if (!$nocache && !empty($json['cities']['clans'])) {
                    $data['cities']['clans'] = $json['cities']['clans'];
                } else {
                    $data['cities']['clans'] = $ArrayHelpers->splitByKey($dbCon->SelectAll('SELECT `'.self::dbPrefix.'city`.`city`, CONCAT(`'.self::dbPrefix.'clan`.`race`, \' of \', `'.self::dbPrefix.'clan`.`clan`, \' clan\') AS `value`, COUNT(`'.self::dbPrefix.'character`.`characterid`) AS `count` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'character`.`cityid`=`'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character`.`clanid`=`'.self::dbPrefix.'clan`.`clanid` GROUP BY `city`, `value` ORDER BY `count` DESC'), 'city', [$Lodestone->getCityName(2, 'na'), $Lodestone->getCityName(4, 'na'), $Lodestone->getCityName(5, 'na')], []);
                }
                #Clan distribution by grand company
                if (!$nocache && !empty($json['grand_companies']['clans'])) {
                    $data['grand_companies']['clans'] = $json['grand_companies']['clans'];
                } else {
                    $data['grand_companies']['clans'] = $ArrayHelpers->splitByKey($dbCon->SelectAll('SELECT `'.self::dbPrefix.'grandcompany`.`gcName`, CONCAT(`'.self::dbPrefix.'clan`.`race`, \' of \', `'.self::dbPrefix.'clan`.`clan`, \' clan\') AS `value`, COUNT(`'.self::dbPrefix.'character`.`characterid`) AS `count` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character`.`clanid`=`'.self::dbPrefix.'clan`.`clanid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'character`.`gcrankid`=`'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'grandcompany` ON `'.self::dbPrefix.'grandcompany_rank`.`gcId` = `'.self::dbPrefix.'grandcompany`.`gcId` WHERE `'.self::dbPrefix.'character`.`deleted` IS NULL AND `'.self::dbPrefix.'grandcompany`.`gcName` IS NOT NULL GROUP BY `gcName`, `value` ORDER BY `count` DESC'), 'gcName', [], []);
                }
                break;
            case 'astrology':
                #Get statistics by guardian
                if (!$nocache && !empty($json['characters']['guardians'])) {
                    $data['characters']['guardians'] = $json['characters']['guardians'];
                } else {
                    $data['characters']['guardians'] = $dbCon->countUnique(''.self::dbPrefix.'character', 'guardianid', '`'.self::dbPrefix.'character`.`deleted` IS NULL',''.self::dbPrefix.'guardian', 'INNER', 'guardianid', '`'.self::dbPrefix.'character`.`genderid`, `'.self::dbPrefix.'guardian`.`guardian`', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`']);
                    #Add colors to guardians
                    foreach ($data['characters']['guardians'] as $key=>$guardian) {
                        $data['characters']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    #Split guardians by gender
                    $data['characters']['guardians'] = $ArrayHelpers->splitByKey($data['characters']['guardians'], 'genderid', ['female', 'male'], [0, 1]);
                }
                #Guardian distribution by city
                if (!$nocache && !empty($json['cities']['guardians'])) {
                    $data['cities']['guardians'] = $json['cities']['guardians'];
                } else {
                    $data['cities']['guardians'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'city`.`city`, `'.self::dbPrefix.'guardian`.`guardian` AS `value`, COUNT(`'.self::dbPrefix.'character`.`characterid`) AS `count` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'character`.`cityid`=`'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'guardian` ON `'.self::dbPrefix.'character`.`guardianid`=`'.self::dbPrefix.'guardian`.`guardianid` GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to guardians
                    foreach ($data['cities']['guardians'] as $key=>$guardian) {
                        $data['cities']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    $data['cities']['guardians'] = $ArrayHelpers->splitByKey($data['cities']['guardians'], 'city', [], []);
                }
                #Guardians distribution by grand company
                if (!$nocache && !empty($json['grand_companies']['guardians'])) {
                    $data['grand_companies']['guardians'] = $json['grand_companies']['guardians'];
                } else {
                    $data['grand_companies']['guardians'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'grandcompany`.`gcName`, `'.self::dbPrefix.'guardian`.`guardian` AS `value`, COUNT(`'.self::dbPrefix.'character`.`characterid`) AS `count` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'guardian` ON `'.self::dbPrefix.'character`.`guardianid`=`'.self::dbPrefix.'guardian`.`guardianid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'character`.`gcrankid`=`'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'grandcompany` ON `'.self::dbPrefix.'grandcompany_rank`.`gcId` = `'.self::dbPrefix.'grandcompany`.`gcId` WHERE `'.self::dbPrefix.'character`.`deleted` IS NULL AND `'.self::dbPrefix.'grandcompany`.`gcName` IS NOT NULL GROUP BY `gcName`, `value` ORDER BY `count` DESC');
                    #Add colors to guardians
                    foreach ($data['grand_companies']['guardians'] as $key=>$guardian) {
                        $data['grand_companies']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    $data['grand_companies']['guardians'] = $ArrayHelpers->splitByKey($data['grand_companies']['guardians'], 'gcName', [], []);
                }
                break;
            case 'characters':
                #Jobs popularity
                if (!$nocache && !empty($json['characters']['jobs'])) {
                    $data['characters']['jobs'] = $json['characters']['jobs'];
                } else {
                    $jobs = $dbCon->selectRow('SELECT SUM(`Alchemist`) AS `Alchemist`, SUM(`Armorer`) AS `Armorer`, SUM(`Astrologian`) AS `Astrologian`, SUM(`Bard`) AS `Bard`, SUM(`BlackMage`) AS `BlackMage`, SUM(`Blacksmith`) AS `Blacksmith`, SUM(`BlueMage`) AS `BlueMage`, SUM(`Botanist`) AS `Botanist`, SUM(`Carpenter`) AS `Carpenter`, SUM(`Culinarian`) AS `Culinarian`, SUM(`Dancer`) AS `Dancer`, SUM(`DarkKnight`) AS `DarkKnight`, SUM(`Dragoon`) AS `Dragoon`, SUM(`Fisher`) AS `Fisher`, SUM(`Goldsmith`) AS `Goldsmith`, SUM(`Gunbreaker`) AS `Gunbreaker`, SUM(`Leatherworker`) AS `Leatherworker`, SUM(`Machinist`) AS `Machinist`, SUM(`Miner`) AS `Miner`, SUM(`Monk`) AS `Monk`, SUM(`Ninja`) AS `Ninja`, SUM(`Paladin`) AS `Paladin`, SUM(`RedMage`) AS `RedMage`, SUM(`Samurai`) AS `Samurai`, SUM(`Scholar`) AS `Scholar`, SUM(`Summoner`) AS `Summoner`, SUM(`Warrior`) AS `Warrior`, SUM(`Weaver`) AS `Weaver`, SUM(`WhiteMage`) AS `WhiteMage` FROM `'.self::dbPrefix.'character`;');
                    #Sort array
                    arsort($jobs);
                    #Add spaces to job names
                    foreach ($jobs as $job=>$level) {
                        $data['characters']['jobs'][preg_replace('/(\B[A-Z])/', ' $1', $job)] = $level;
                    }
                }
                #Most name changes
                if (!$nocache && !empty($json['characters']['changes']['name'])) {
                    $data['characters']['changes']['name'] = $json['characters']['changes']['name'];
                } else {
                    $data['characters']['changes']['name'] = $dbCon->countUnique(''.self::dbPrefix.'character_names', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['changes']['name'], 'value', 'name');
                }
                #Most reincarnation
                if (!$nocache && !empty($json['characters']['changes']['clan'])) {
                    $data['characters']['changes']['clan'] = $json['characters']['changes']['clan'];
                } else {
                    $data['characters']['changes']['clan'] = $dbCon->countUnique(''.self::dbPrefix.'character_clans', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['changes']['clan'], 'value', 'name');
                }
                #Most servers
                if (!$nocache && !empty($json['characters']['changes']['server'])) {
                    $data['characters']['changes']['server'] = $json['characters']['changes']['server'];
                } else {
                    $data['characters']['changes']['server'] = $dbCon->countUnique(''.self::dbPrefix.'character_servers', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['changes']['server'], 'value', 'name');
                }
                #Most companies
                if (!$nocache && !empty($json['characters']['groups']['Free Companies'])) {
                    $data['characters']['groups']['Free Companies'] = $json['characters']['groups']['Free Companies'];
                } else {
                    $data['characters']['groups']['Free Companies'] = $dbCon->countUnique(''.self::dbPrefix.'freecompany_character', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['groups']['Free Companies'], 'value', 'name');
                }
                #Most PvP teams
                if (!$nocache && !empty($json['characters']['groups']['PvP Teams'])) {
                    $data['characters']['groups']['PvP Teams'] = $json['characters']['groups']['PvP Teams'];
                } else {
                    $data['characters']['groups']['PvP Teams'] = $dbCon->countUnique(''.self::dbPrefix.'pvpteam_character', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['groups']['PvP Teams'], 'value', 'name');
                }
                #Most x-linkshells
                if (!$nocache && !empty($json['characters']['groups']['Linkshells'])) {
                    $data['characters']['groups']['Linkshells'] = $json['characters']['groups']['Linkshells'];
                } else {
                    $data['characters']['groups']['Linkshells'] = $dbCon->countUnique(''.self::dbPrefix.'linkshell_character', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['groups']['Linkshells'], 'value', 'name');
                }
                #Most linkshells
                if (!$nocache && !empty($json['characters']['groups']['linkshell'])) {
                    $data['characters']['groups']['linkshell'] = $json['characters']['groups']['linkshell'];
                } else {
                    $data['characters']['groups']['linkshell'] = $dbCon->countUnique(''.self::dbPrefix.'linkshell_character', 'characterid', '', ''.self::dbPrefix.'character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`', 'DESC', 20, [], true);
                    $ArrayHelpers->renameColumn($data['characters']['groups']['linkshell'], 'value', 'name');
                }
                #Groups affiliation
                if (!$nocache && !empty($json['characters']['groups']['participation'])) {
                    $data['characters']['groups']['participation'] = $json['characters']['groups']['participation'];
                } else {
                    $data['characters']['groups']['participation'] = $dbCon->SelectAll('
                        SELECT `affiliation` AS `value`, COUNT(`affiliation`) AS `count`FROM (
                            SELECT
                                (CASE
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NOT NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company only\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NOT NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NULL) THEN \'PvP Team only\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Linkshell only\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NOT NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NOT NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company and PvP Team\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NOT NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company and Linkshell\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NOT NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NOT NULL) THEN \'PvP Team and Linkshell\'
                                    WHEN (`'.self::dbPrefix.'freecompany_character`.`freecompanyid` IS NOT NULL AND `'.self::dbPrefix.'pvpteam_character`.`pvpteamid` IS NOT NULL AND `'.self::dbPrefix.'linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company, PvP Team and Linkshell\'
                                    ELSE \'No groups\'
                                END) AS `affiliation`
                            FROM `'.self::dbPrefix.'character`
                            LEFT JOIN `'.self::dbPrefix.'linkshell_character` ON `'.self::dbPrefix.'linkshell_character`.`characterid` = `'.self::dbPrefix.'character`.`characterid`
                            LEFT JOIN `'.self::dbPrefix.'freecompany_character` ON `'.self::dbPrefix.'freecompany_character`.`characterid` = `'.self::dbPrefix.'character`.`characterid`
                            LEFT JOIN `'.self::dbPrefix.'pvpteam_character` ON `'.self::dbPrefix.'pvpteam_character`.`characterid` = `'.self::dbPrefix.'character`.`characterid`
                            WHERE `'.self::dbPrefix.'character`.`deleted` IS NULL AND (`'.self::dbPrefix.'linkshell_character`.`current`=1 OR `'.self::dbPrefix.'freecompany_character`.`current`=1 OR `'.self::dbPrefix.'pvpteam_character`.`current`=1) GROUP BY `'.self::dbPrefix.'character`.`characterid`) `tempresult`
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
                }
                #Get characters with most PvP matches. Using regular SQL since we do not count unique values, but rather use the regular column values
                if (!$nocache && !empty($json['characters']['most_pvp'])) {
                    $data['characters']['most_pvp'] = $json['characters']['most_pvp'];
                } else {
                    $data['characters']['most_pvp'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'character`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, \'character\' AS `type`, `'.self::dbPrefix.'character`.`name`, `pvp_matches` AS `count` FROM `'.self::dbPrefix.'character` ORDER BY `'.self::dbPrefix.'character`.`pvp_matches` DESC LIMIT 20');
                }
                break;
            case 'freecompanies':
                #Get most popular estate locations
                if (!$nocache && !empty($json['freecompany']['estate'])) {
                    $data['freecompany']['estate'] = $json['freecompany']['estate'];
                } else {
                    $data['freecompany']['estate'] = $ArrayHelpers->topAndBottom($dbCon->countUnique(''.self::dbPrefix.'freecompany', 'estateid', '`'.self::dbPrefix.'freecompany`.`deleted` IS NULL AND `'.self::dbPrefix.'freecompany`.`estateid` IS NOT NULL', ''.self::dbPrefix.'estate', 'INNER', 'estateid', '`'.self::dbPrefix.'estate`.`area`, `'.self::dbPrefix.'estate`.`plot`, CONCAT(`'.self::dbPrefix.'estate`.`area`, \', plot \', `'.self::dbPrefix.'estate`.`plot`)'), 20);
                }
                #Get statistics by activity time
                if (!$nocache && !empty($json['freecompany']['active'])) {
                    $data['freecompany']['active'] = $json['freecompany']['active'];
                } else {
                    $data['freecompany']['active'] = $dbCon->sumUnique(''.self::dbPrefix.'freecompany', 'activeid', [1, 2, 3], ['Always', 'Weekdays', 'Weekends'], '`'.self::dbPrefix.'freecompany`.`deleted` IS NULL', ''.self::dbPrefix.'timeactive', 'INNER', 'activeid', 'IF(`'.self::dbPrefix.'freecompany`.`recruitment`=1, \'Recruiting\', \'Not recruiting\') AS `recruiting`');
                }
                #Get statistics by activities
                if (!$nocache && !empty($json['freecompany']['activities'])) {
                    $data['freecompany']['activities'] = $json['freecompany']['activities'];
                } else {
                    $data['freecompany']['activities'] = $dbCon->SelectRow('SELECT SUM(`Tank`)/COUNT(`freecompanyid`)*100 AS `Tank`, SUM(`Healer`)/COUNT(`freecompanyid`)*100 AS `Healer`, SUM(`DPS`)/COUNT(`freecompanyid`)*100 AS `DPS`, SUM(`Crafter`)/COUNT(`freecompanyid`)*100 AS `Crafter`, SUM(`Gatherer`)/COUNT(`freecompanyid`)*100 AS `Gatherer`, SUM(`Role-playing`)/COUNT(`freecompanyid`)*100 AS `Role-playing`, SUM(`Leveling`)/COUNT(`freecompanyid`)*100 AS `Leveling`, SUM(`Casual`)/COUNT(`freecompanyid`)*100 AS `Casual`, SUM(`Hardcore`)/COUNT(`freecompanyid`)*100 AS `Hardcore`, SUM(`Dungeons`)/COUNT(`freecompanyid`)*100 AS `Dungeons`, SUM(`Guildhests`)/COUNT(`freecompanyid`)*100 AS `Guildhests`, SUM(`Trials`)/COUNT(`freecompanyid`)*100 AS `Trials`, SUM(`Raids`)/COUNT(`freecompanyid`)*100 AS `Raids`, SUM(`PvP`)/COUNT(`freecompanyid`)*100 AS `PvP` FROM `'.self::dbPrefix.'freecompany` WHERE `deleted` IS NULL');
                }
                #Get statistics by monthly ranks
                if (!$nocache && !empty($json['freecompany']['ranking']['monthly'])) {
                    $data['freecompany']['ranking']['monthly'] = $json['freecompany']['ranking']['monthly'];
                } else {
                    $data['freecompany']['ranking']['monthly'] = $dbCon->SelectAll('SELECT `tempresult`.*, `'.self::dbPrefix.'freecompany`.`name`, COALESCE(`'.self::dbPrefix.'freecompany`.`crest`, `'.self::dbPrefix.'freecompany`.`grandcompanyid`) AS `icon`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`monthly`)*100 AS `ratio` FROM `'.self::dbPrefix.'freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `'.self::dbPrefix.'freecompany_ranking` `sub`)) `tempresult` INNER JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC');
                    if (count($data['freecompany']['ranking']['monthly']) > 1) {
                        $data['freecompany']['ranking']['monthly'] = $ArrayHelpers->topAndBottom($data['freecompany']['ranking']['monthly'], 20);
                    } else {
                        $data['freecompany']['ranking']['monthly'] = [];
                    }
                }
                #Get statistics by weekly ranks
                if (!$nocache && !empty($json['freecompany']['ranking']['weekly'])) {
                    $data['freecompany']['ranking']['weekly'] = $json['freecompany']['ranking']['weekly'];
                } else {
                    $data['freecompany']['ranking']['weekly'] = $dbCon->SelectAll('SELECT `tempresult`.*, `'.self::dbPrefix.'freecompany`.`name`, COALESCE(`'.self::dbPrefix.'freecompany`.`crest`, `'.self::dbPrefix.'freecompany`.`grandcompanyid`) AS `icon`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`weekly`)*100 AS `ratio` FROM `'.self::dbPrefix.'freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `'.self::dbPrefix.'freecompany_ranking` `sub`)) `tempresult` INNER JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC');
                    if (count($data['freecompany']['ranking']['weekly']) > 1) {
                        $data['freecompany']['ranking']['weekly'] = $ArrayHelpers->topAndBottom($data['freecompany']['ranking']['weekly'], 20);
                    } else {
                        $data['freecompany']['ranking']['weekly'] = [];
                    }
                }
                #Get most popular crests
                if (!$nocache && !empty($json['freecompany']['crests'])) {
                    $data['freecompany']['crests'] = $json['freecompany']['crests'];
                } else {
                    $data['freecompany']['crests'] = $dbCon->countUnique(''.self::dbPrefix.'freecompany', 'crest', '`'.self::dbPrefix.'freecompany`.`deleted` IS NULL AND `'.self::dbPrefix.'freecompany`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                }
                break;
            case 'cities':
                #Get statistics by city
                if (!$nocache && !empty($json['cities']['gender'])) {
                    $data['cities']['gender'] = $json['cities']['gender'];
                } else {
                    $data['cities']['gender'] = $dbCon->countUnique(''.self::dbPrefix.'character', 'cityid', '`'.self::dbPrefix.'character`.`deleted` IS NULL',''.self::dbPrefix.'city', 'INNER', 'cityid', '`'.self::dbPrefix.'character`.`genderid`, `'.self::dbPrefix.'city`.`city`', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`']);
                    #Add colors to cities
                    foreach ($data['cities']['gender'] as $key=>$city) {
                        $data['cities']['gender'][$key]['color'] = $Lodestone->colorCities($city['value']);
                    }
                    #Split cities by gender
                    $data['cities']['gender'] = $ArrayHelpers->splitByKey($data['cities']['gender'], 'genderid', ['female', 'male'], [0, 1]);
                }
                #City by free company
                if (!$nocache && !empty($json['cities']['free_company'])) {
                    $data['cities']['free_company'] = $json['cities']['free_company'];
                } else {
                    $data['cities']['free_company'] = $dbCon->countUnique(''.self::dbPrefix.'freecompany', 'estateid', '`'.self::dbPrefix.'freecompany`.`estateid` IS NOT NULL AND `'.self::dbPrefix.'freecompany`.`deleted` IS NULL', ''.self::dbPrefix.'estate', 'INNER', 'estateid', '`'.self::dbPrefix.'estate`.`area`');
                    #Add colors to cities
                    foreach ($data['cities']['free_company'] as $key=>$city) {
                        $data['cities']['free_company'][$key]['color'] = $Lodestone->colorCities($city['value']);
                    }
                }
                #Grand companies' distribution (characters)
                if (!$nocache && !empty($json['cities']['gc_characters'])) {
                    $data['cities']['gc_characters'] = $json['cities']['gc_characters'];
                } else {
                    $data['cities']['gc_characters'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'city`.`city`, `'.self::dbPrefix.'grandcompany`.`gcName` AS `value`, COUNT(`'.self::dbPrefix.'character`.`characterid`) AS `count` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'character`.`cityid`=`'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'character`.`gcrankid`=`'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'grandcompany` ON `'.self::dbPrefix.'grandcompany_rank`.`gcId` = `'.self::dbPrefix.'grandcompany`.`gcId` WHERE `'.self::dbPrefix.'character`.`deleted` IS NULL AND `'.self::dbPrefix.'grandcompany`.`gcName` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to companies
                    foreach ($data['cities']['gc_characters'] as $key=>$company) {
                        $data['cities']['gc_characters'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    $data['cities']['gc_characters'] = $ArrayHelpers->splitByKey($data['cities']['gc_characters'], 'city', [], []);
                }
                #Grand companies distribution (free companies)
                if (!$nocache && !empty($json['cities']['gc_fc'])) {
                    $data['cities']['gc_fc'] = $json['cities']['gc_fc'];
                } else {
                    $data['cities']['gc_fc'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'city`.`city`, `'.self::dbPrefix.'grandcompany`.`gcName` AS `value`, COUNT(`'.self::dbPrefix.'freecompany`.`freecompanyid`) AS `count` FROM `'.self::dbPrefix.'freecompany` LEFT JOIN `'.self::dbPrefix.'estate` ON `'.self::dbPrefix.'freecompany`.`estateid`=`'.self::dbPrefix.'estate`.`estateid` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'estate`.`cityid`=`'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'freecompany`.`grandcompanyid`=`'.self::dbPrefix.'grandcompany_rank`.`gcrankid` WHERE `'.self::dbPrefix.'freecompany`.`deleted` IS NULL AND `'.self::dbPrefix.'freecompany`.`estateid` IS NOT NULL AND `'.self::dbPrefix.'grandcompany`.`gcName` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to companies
                    foreach ($data['cities']['gc_fc'] as $key=>$company) {
                        $data['cities']['gc_fc'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    $data['cities']['gc_fc'] = $ArrayHelpers->splitByKey($data['cities']['gc_fc'], 'city', [], []);
                }
                break;
            case 'grandcompanies':
                #Get statistics for grand companies
                if (!$nocache && !empty($json['grand_companies']['population'])) {
                    $data['grand_companies']['population'] = $json['grand_companies']['population'];
                } else {
                    $data['grand_companies']['population'] = $dbCon->countUnique(''.self::dbPrefix.'character', 'gcrankid', '`'.self::dbPrefix.'character`.`deleted` IS NULL AND `'.self::dbPrefix.'character`.`gcrankid` IS NOT NULL', ''.self::dbPrefix.'grandcompany_rank', 'INNER', 'gcrankid', '`'.self::dbPrefix.'character`.`genderid`, `'.self::dbPrefix.'grandcompany_rank`.`gcId`', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`']);
                    #Add colors to companies
                    foreach ($data['grand_companies']['population'] as $key=>$company) {
                        $data['grand_companies']['population'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    #Split companies by gender
                    $data['grand_companies']['population'] = $ArrayHelpers->splitByKey($data['grand_companies']['population'], 'genderid', ['female', 'male'], [0, 1]);
                    $data['grand_companies']['population']['free_company'] = $dbCon->countUnique(''.self::dbPrefix.'freecompany', 'grandcompanyid', '`'.self::dbPrefix.'freecompany`.`deleted` IS NULL', ''.self::dbPrefix.'grandcompany_rank', 'INNER', 'gcrankid', '`'.self::dbPrefix.'grandcompany_rank`.`gcId`');
                    #Add colors to cities
                    foreach ($data['grand_companies']['population']['free_company'] as $key=>$company) {
                        $data['grand_companies']['population']['free_company'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                }
                #Grand companies ranks
                if (!$nocache && !empty($json['grand_companies']['ranks'])) {
                    $data['grand_companies']['ranks'] = $json['grand_companies']['ranks'];
                } else {
                    $data['grand_companies']['ranks'] = $ArrayHelpers->splitByKey($dbCon->countUnique(''.self::dbPrefix.'character', 'gcrankid', '`'.self::dbPrefix.'character`.`deleted` IS NULL', ''.self::dbPrefix.'grandcompany_rank', 'INNER', 'gcrankid', '`'.self::dbPrefix.'character`.`genderid`, `'.self::dbPrefix.'grandcompany_rank`.`gcId`, `'.self::dbPrefix.'grandcompany_rank`.`gc_rank`', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`', '`'.self::dbPrefix.'grandcompany_rank`.`gcId`']), 'gcId', [], []);
                    #Split by gender
                    foreach ($data['grand_companies']['ranks'] as $key=>$company) {
                        $data['grand_companies']['ranks'][$key] = $ArrayHelpers->splitByKey($company, 'genderid', ['female', 'male'], [0, 1]);
                    }
                }
                break;
            case 'servers':
                #Characters
                if (!$nocache && !empty($json['servers']['female population']) && !empty($json['servers']['male population'])) {
                    $data['servers']['female population'] = $json['servers']['female population'];
                    $data['servers']['male population'] = $json['servers']['male population'];
                } else {
                    $data['servers']['characters'] = $ArrayHelpers->splitByKey($dbCon->countUnique(''.self::dbPrefix.'character', 'serverid', '`'.self::dbPrefix.'character`.`deleted` IS NULL', ''.self::dbPrefix.'server', 'INNER', 'serverid', '`'.self::dbPrefix.'character`.`genderid`, `'.self::dbPrefix.'server`.`server`', 'DESC', 0, ['`'.self::dbPrefix.'character`.`genderid`']), 'genderid', ['female', 'male'], [0, 1]);
                    $data['servers']['female population'] = $ArrayHelpers->topAndBottom($data['servers']['characters']['female'], 20);
                    $data['servers']['male population'] = $ArrayHelpers->topAndBottom($data['servers']['characters']['male'], 20);
                    unset($data['servers']['characters']);
                }
                #Free companies
                if (!$nocache && !empty($json['servers']['Free Companies'])) {
                    $data['servers']['Free Companies'] = $json['servers']['Free Companies'];
                } else {
                    $data['servers']['Free Companies'] = $ArrayHelpers->topAndBottom($dbCon->countUnique(''.self::dbPrefix.'freecompany', 'serverid', '`'.self::dbPrefix.'freecompany`.`deleted` IS NULL', ''.self::dbPrefix.'server', 'INNER', 'serverid', '`'.self::dbPrefix.'server`.`server`'), 20);
                }
                #Linkshells
                if (!$nocache && !empty($json['servers']['Linkshells'])) {
                    $data['servers']['Linkshells'] = $json['servers']['Linkshells'];
                } else {
                    $data['servers']['Linkshells'] = $ArrayHelpers->topAndBottom($dbCon->countUnique(''.self::dbPrefix.'linkshell', 'serverid', '`'.self::dbPrefix.'linkshell`.`crossworld` = 0 AND `'.self::dbPrefix.'linkshell`.`deleted` IS NULL', ''.self::dbPrefix.'server', 'INNER', 'serverid', '`'.self::dbPrefix.'server`.`server`'), 20);
                }
                #Crossworld linkshells
                if (!$nocache && !empty($json['servers']['crossworldlinkshell'])) {
                    $data['servers']['crossworldlinkshell'] = $json['servers']['crossworldlinkshell'];
                } else {
                    $data['servers']['crossworldlinkshell'] = $dbCon->countUnique(''.self::dbPrefix.'linkshell', 'serverid', '`'.self::dbPrefix.'linkshell`.`crossworld` = 1 AND `'.self::dbPrefix.'linkshell`.`deleted` IS NULL', ''.self::dbPrefix.'server', 'INNER', 'serverid', '`'.self::dbPrefix.'server`.`datacenter`');
                }
                #PvP teams
                if (!$nocache && !empty($json['servers']['pvpteam'])) {
                    $data['servers']['pvpteam'] = $json['servers']['pvpteam'];
                } else {
                    $data['servers']['pvpteam'] = $dbCon->countUnique(''.self::dbPrefix.'pvpteam', 'datacenterid', '`'.self::dbPrefix.'pvpteam`.`deleted` IS NULL', ''.self::dbPrefix.'server', 'INNER', 'serverid', '`'.self::dbPrefix.'server`.`datacenter`');
                }
                break;
            case 'achievements':
                #Get achievements statistics
                if (!$nocache && !empty($json['other']['achievements'])) {
                    $data['other']['achievements'] = $json['other']['achievements'];
                } else {
                    $data['other']['achievements'] = $dbCon->SelectAll('SELECT \'achievement\' as `type`, `'.self::dbPrefix.'achievement`.`category`, `'.self::dbPrefix.'achievement`.`achievementid` AS `id`, `'.self::dbPrefix.'achievement`.`icon`, `'.self::dbPrefix.'achievement`.`name` AS `name`, `count` FROM (SELECT `'.self::dbPrefix.'character_achievement`.`achievementid`, count(`'.self::dbPrefix.'character_achievement`.`achievementid`) AS `count` from `'.self::dbPrefix.'character_achievement` GROUP BY `'.self::dbPrefix.'character_achievement`.`achievementid` ORDER BY `count`) `tempresult` INNER JOIN `'.self::dbPrefix.'achievement` ON `tempresult`.`achievementid`=`'.self::dbPrefix.'achievement`.`achievementid` WHERE `'.self::dbPrefix.'achievement`.`category` IS NOT NULL ORDER BY `count`');
                    #Split achievements by categories
                    $data['other']['achievements'] = $ArrayHelpers->splitByKey($data['other']['achievements'], 'category', [], []);
                    #Get only top 20 for each category
                    foreach ($data['other']['achievements'] as $key=>$category) {
                        $data['other']['achievements'][$key] = array_slice($category, 0, 20);
                    }
                }
                break;
            case 'timelines':
                #Get namedays timeline. Using custom SQL, since need special order by `namedayid`, instead of by `count`
                if (!$nocache && !empty($json['timelines']['nameday'])) {
                    $data['timelines']['nameday'] = $json['timelines']['nameday'];
                } else {
                    #PHPStorm complains about `namedayid` for no reason
                    /** @noinspection SqlAggregates */
                    $data['timelines']['nameday'] = $dbCon->SelectAll('SELECT `'.self::dbPrefix.'nameday`.`nameday` AS `value`, COUNT(`'.self::dbPrefix.'character`.`namedayid`) AS `count` FROM `'.self::dbPrefix.'character` INNER JOIN `'.self::dbPrefix.'nameday` ON `'.self::dbPrefix.'character`.`namedayid`=`'.self::dbPrefix.'nameday`.`namedayid` GROUP BY `value` ORDER BY `'.self::dbPrefix.'nameday`.`namedayid`');
                }
                #Timeline of groups formations
                if (!$nocache && !empty($json['timelines']['formed'])) {
                    $data['timelines']['formed'] = $json['timelines']['formed'];
                } else {
                    $data['timelines']['formed'] = $dbCon->SelectAll(
                        'SELECT `formed` AS `value`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `formed`, COUNT(`formed`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'freecompany` WHERE `formed` IS NOT NULL GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, COUNT(`formed`) AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'linkshell` WHERE `formed` IS NOT NULL GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`formed`) AS `pvpteams` FROM `'.self::dbPrefix.'pvpteam` WHERE `formed` IS NOT NULL GROUP BY `formed`
                        ) `tempResults`
                        GROUP BY `formed` ORDER BY `formed`'
                    );
                }
                #Timeline of entities registration
                if (!$nocache && !empty($json['timelines']['registered'])) {
                    $data['timelines']['registered'] = $json['timelines']['registered'];
                } else {
                    $data['timelines']['registered'] = $dbCon->SelectAll(
                        'SELECT `registered` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `registered`, COUNT(`registered`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'character` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, COUNT(`registered`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'freecompany` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`registered`) AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'linkshell` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`registered`) AS `pvpteams` FROM `'.self::dbPrefix.'pvpteam` WHERE `registered` IS NOT NULL GROUP BY `registered`
                        ) `tempResults`
                        GROUP BY `registered` ORDER BY `registered` '
                    );
                }
                #Timeline of entities deletion
                if (!$nocache && !empty($json['timelines']['deleted'])) {
                    $data['timelines']['deleted'] = $json['timelines']['deleted'];
                } else {
                    $data['timelines']['deleted'] = $dbCon->SelectAll(
                        'SELECT `deleted` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `deleted`, COUNT(`deleted`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'character` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, COUNT(`deleted`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'freecompany` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`deleted`) AS `linkshells`, 0 AS `pvpteams` FROM `'.self::dbPrefix.'linkshell` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`deleted`) AS `pvpteams` FROM `'.self::dbPrefix.'pvpteam` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                        ) `tempResults`
                        GROUP BY `deleted` ORDER BY `deleted` '
                    );
                }
                break;
            case 'bugs':
                #Characters with no clan/race
                if (!$nocache && !empty($json['bugs']['noClan'])) {
                    $data['bugs']['noClan'] = $json['bugs']['noClan'];
                } else {
                    $data['bugs']['noClan'] = $dbCon->SelectAll('SELECT `characterid` AS `id`, `name`, `characterid` AS `icon`, \'character\' AS `type` FROM `'.self::dbPrefix.'character` WHERE `clanid` IS NULL AND `deleted` IS NULL ORDER BY `name`;');
                }
                #Groups with no members
                if (!$nocache && !empty($json['bugs']['noMembers'])) {
                    $data['bugs']['noMembers'] = $json['bugs']['noMembers'];
                } else {
                    $data['bugs']['noMembers'] = $dbCon->SelectAll(
                        'SELECT `freecompanyid` AS `id`, `name`, \'freecompany\' AS `type`, COALESCE(`crest`, `grandcompanyid`) AS `icon` FROM `'.self::dbPrefix.'freecompany` WHERE `deleted` IS NULL AND `freecompanyid` NOT IN (SELECT `freecompanyid` FROM `'.self::dbPrefix.'character` WHERE `freecompanyid` IS NOT NULL)
                        UNION
                        SELECT `linkshellid` AS `id`, `name`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, NULL AS `icon` FROM `'.self::dbPrefix.'linkshell` WHERE `deleted` IS NULL AND `linkshellid` NOT IN (SELECT `linkshellid` FROM `'.self::dbPrefix.'linkshell_character`)
                        UNION
                        SELECT `pvpteamid` AS `id`, `name`, \'pvpteam\' AS `type`, `crest` AS `icon` FROM `'.self::dbPrefix.'pvpteam` WHERE `deleted` IS NULL AND `pvpteamid` NOT IN (SELECT `pvpteamid` FROM `'.self::dbPrefix.'character` WHERE `pvpteamid` IS NOT NULL)
                        ORDER BY `name`;'
                    );
                }
                break;
            case 'other':
                #Communities
                if (!$nocache && !empty($json['other']['communities'])) {
                    $data['other']['communities'] = $json['other']['communities'];
                } else {
                    $data['other']['communities'] = $ArrayHelpers->splitByKey($dbCon->SelectAll('
                        SELECT `type`, IF(`has_community`=0, \'No community\', \'Community\') AS `value`, count(`has_community`) AS `count` FROM (
                            SELECT \'Free Company\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `'.self::dbPrefix.'freecompany` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `'.self::dbPrefix.'pvpteam` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `'.self::dbPrefix.'linkshell` WHERE `deleted` IS NULL
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
                }
                #Deleted entities statistics
                if (!$nocache && !empty($json['other']['entities'])) {
                    $data['other']['entities'] = $json['other']['entities'];
                } else {
                    $data['other']['entities'] = $dbCon->SelectAll('
                        SELECT CONCAT(IF(`deleted`=0, \'Active\', \'Deleted\'), \' \', `type`) AS `value`, count(`deleted`) AS `count` FROM (
                            SELECT \'Character\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `'.self::dbPrefix.'character`
                            UNION ALL
                            SELECT \'Free Company\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `'.self::dbPrefix.'freecompany`
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `'.self::dbPrefix.'pvpteam`
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `'.self::dbPrefix.'linkshell`
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `count` DESC
                    ');
                }
                if (!$nocache && !empty($json['pvpteam']['crests'])) {
                    $data['pvpteam']['crests'] = $json['pvpteam']['crests'];
                } else {
                    $data['pvpteam']['crests'] = $dbCon->countUnique(''.self::dbPrefix.'pvpteam', 'crest', '`'.self::dbPrefix.'pvpteam`.`deleted` IS NULL AND `'.self::dbPrefix.'pvpteam`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                }
                break;
        }
        unset($dbCon, $ArrayHelpers, $Lodestone);
        #Attempt to write to cache
        file_put_contents($cachePath, json_encode(array_merge($json, $data), JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
        return $data;
    }
}
