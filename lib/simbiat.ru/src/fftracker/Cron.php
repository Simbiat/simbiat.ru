<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\fftracker\Entities\Achievement;
use Simbiat\fftracker\Entities\Character;
use Simbiat\fftracker\Entities\CrossworldLinkshell;
use Simbiat\fftracker\Entities\FreeCompany;
use Simbiat\fftracker\Entities\Linkshell;
use Simbiat\fftracker\Entities\PvPTeam;
use Simbiat\HomePage;
use Simbiat\Lodestone;

class Cron
{
    #Update statistics
    public function UpdateStatistics(): bool|string
    {
        try {
            foreach (['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'] as $type) {
                (new Statistics)->get($type, '', true);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    public function UpdateEntity(string $id, string $type): bool|string
    {
        return match ($type) {
            'character' => (new Character($id))->update(),
            'freecompany' => (new FreeCompany($id))->update(),
            'pvpteam' => (new PvPTeam($id))->update(),
            'linkshell' => (new Linkshell($id))->update(),
            'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell($id))->update(),
            'achievement' => (new Achievement($id))->update(),
            default => false,
        };
    }

    #Function to update old entities
    public function UpdateOld(int $limit = 1): bool|string
    {
        #Sanitize entities number
        if ($limit < 1) {
            $limit = 1;
        }
        try {
            $dbCon = HomePage::$dbController;
            $entities = $dbCon->selectAll('
                    SELECT `type`, `id`, IF(`userid` IS NOT NULL, IF(`updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY), 1, 0), 0) as `priority` FROM (
                        SELECT \'character\' AS `type`, `characterid` AS `id`, `updated`, `deleted`, `userid` FROM `ffxiv__character`
                        UNION ALL
                        SELECT \'freecompany\' AS `type`, `freecompanyid` AS `id`, `updated`, `deleted`, (SELECT `userid` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__character` ON `ffxiv__freecompany_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__freecompany`
                        UNION ALL
                        SELECT \'pvpteam\' AS `type`, `pvpteamid` AS `id`, `updated`, `deleted`, (SELECT `userid` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__character` ON `ffxiv__pvpteam_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__pvpteam`
                        UNION ALL
                        SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `linkshellid` AS `id`, `updated`, `deleted`, (SELECT `userid` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__character` ON `ffxiv__linkshell_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__linkshell`
                        UNION ALL
                        SELECT \'achievement\' AS `type`, `achievementid` AS `id`, `updated`, NULL AS `deleted`, NULL as `userid` FROM `ffxiv__achievement` WHERE `achievementid` IN (SELECT DISTINCT(`achievementid`) FROM `ffxiv__character_achievement`)
                    ) `allEntities` WHERE `deleted` IS NULL
                    ORDER BY `priority` DESC, `updated` LIMIT :maxLines',
                [
                    ':maxLines'=>[$limit, 'int'],
                ]
            );
            foreach ($entities as $entity) {
                $result = $this->UpdateEntity($entity['id'], $entity['type']);
                if (!in_array($result, ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam', 'achievement'])) {
                    return $result;
                }
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to add missing jobs to table
    public function UpdateJobs(): bool|string
    {
        try {
            #Cache controller
            $dbController = HomePage::$dbController;
            #Get the freshest character ID
            $characterId = $dbController->selectValue('SELECT `characterid` FROM `ffxiv__character` WHERE `deleted` IS NULL ORDER BY `updated` DESC LIMIT 1;');
            #Grab its data from Lodestone
            $character = (new Character(strval($characterId)))->getFromLodestone();
            if (empty($character['jobs'])) {
                return 'No jobs retrieved for character '.$characterId;
            }
            #Sort alphabetically by keys
            ksort($character['jobs'], SORT_NATURAL);
            #Prepare string for ALTER
            $alter = [];
            #Previous job in the list (for AFTER clause)
            $previous = '';
            foreach ($character['jobs'] as $job=>$details) {
                #Remove spaces from the job name
                $jobNoSpace = preg_replace('/\s*/', '', $job);
                #Check if job is present as respective column
                if (!$dbController->checkColumn('ffxiv__character', $jobNoSpace)) {
                    #Add respective column definition
                    $alter[] = 'ADD COLUMN `'.$jobNoSpace.'` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'Level of '.$job.' job\' AFTER `'.(empty($previous) ? 'pvp_matches' : $previous).'`';
                }
                #Update previous column name
                $previous = $jobNoSpace;
            }
            if (empty($alter)) {
                #Nothing to add
                return true;
            }
            #Generate and run the query
            return $dbController->query('ALTER TABLE `ffxiv__character` '.implode(', ', $alter).';');
        } catch (\Throwable $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Update list of servers
    public function UpdateServers(): bool|string
    {
        try {
            $Lodestone = (new Lodestone);
            #Get server
            $worlds = $Lodestone->getWorldStatus()->getResult()['worlds'];
            #Prepare queries
            $queries = [];
            foreach ($worlds as $dataCenter=>$servers) {
                foreach ($servers as $server=>$status) {
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__server` (`server`, `dataCenter`) VALUES (:server, :dataCenter)',
                        [':server' => $server, ':dataCenter' => $dataCenter],
                    ];
                }
            }
            return HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Register new entities (if found)
    public function registerNew(): bool|string
    {
        $Lodestone = (new Lodestone);
        $dbCon = HomePage::$dbController;
        #Generate list of pages to parse (every hour 256 pages to scan, 2 seconds delay for each?)
        try {
            $worlds = $dbCon->selectAll('
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'linkshell\' AS `entity` FROM `ffxiv__server`
                    CROSS JOIN `ffxiv__orderby`
                    CROSS JOIN `ffxiv__count_filter`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `datacenter` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'crossworldlinkshell\' AS `entity` FROM `ffxiv__orderby`
                    CROSS JOIN `ffxiv__count_filter`
                    CROSS JOIN (
                        SELECT UNIQUE(`datacenter`) FROM `ffxiv__server`
                    ) `dataCenters`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'freecompany\' AS `entity` FROM `ffxiv__server`
                    CROSS JOIN `ffxiv__orderby`
                    CROSS JOIN `ffxiv__count_filter`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, 5 AS `order`, \'\' AS `count`, `page`, `gcId`, \'\' AS `clanid`, \'freecompany\' AS `entity` FROM `ffxiv__server`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    CROSS JOIN `ffxiv__grandcompany`
                    WHERE `gcId` <> 0
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, \'\' AS `count`, `page`, `gcId`, `clanid`, \'character\' AS `entity` FROM `ffxiv__server`
                    CROSS JOIN `ffxiv__orderby`
                    CROSS JOIN `ffxiv__grandcompany`
                    CROSS JOIN `ffxiv__clan`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 5, 6)
                )
                UNION ALL
                (
                    SELECT `datacenter` AS `world`, `orderID` AS `order`, \'\' AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'pvpteam\' AS `entity` FROM `ffxiv__orderby`
                    CROSS JOIN (
                        SELECT UNIQUE(`datacenter`) FROM `ffxiv__server`
                    ) `dataCenters`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                ;
            ');
        } catch (\Throwable $e) {
            return 'Failed to generate pages list: '.$e->getMessage()."\r\n".$e->getTraceAsString();
        }
        exit;

        $request = 0;
        #Loop through the servers
        foreach ($worlds as $world) {
            #Loop through orders
            foreach (['1', '2', '3', '4'] as $order) {
                #Loop through counts
                foreach ([10, 30, 50, 51] as $count) {
                    #Get linkshells
                    $Lodestone->searchLinkshell('', $world, $count, $order);
                    $Lodestone->searchLinkshell('', $world, $count, $order, 20);
                    #Get free companies
                    #Loop through Grand Companies
                    foreach (['1', '2', '3'] as $gc) {
                        $Lodestone->searchFreeCompany('', $world, $count, gcId: $gc, order: $order);
                        $Lodestone->searchFreeCompany('', $world, $count, gcId: $gc, order: $order, page: 20);
                    }
                }
            }
            #Get the newest free companies
            #Loop through pages
            for ($page = 1; $page <= 20; $page++) {
                #Loop through Grand Companies
                foreach (['1', '2', '3'] as $gc) {
                    $Lodestone->searchFreeCompany('', $world, $count, gcId: $gc, order: '5', page: $page);
                }
            }
            #Get characters
            #Loop through orders
            foreach (['1', '2', '5', '6'] as $order) {
                #Loop through Grand Companies
                foreach (['1', '2', '3', '0'] as $gc) {
                    #Loop through clans
                    foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] as $clan) {
                        $Lodestone->searchCharacter('', $world, race_tribe: 'tribe_'.$clan, gcId: $gc, order: $order);
                        $Lodestone->searchCharacter('', $world, race_tribe: 'tribe_'.$clan, gcId: $gc, order: $order, page: 20);
                    }
                }
            }
        }
        #Get list of data centers
        try {
            $worlds = $dbCon->selectUnique('SELECT `datacenter` FROM `ffxiv__server`');
        } catch (\Throwable) {
            $worlds = [];
        }
        #Loop through the servers
        foreach ($worlds as $world) {
            #Loop through orders
            foreach (['1', '2', '3', '4'] as $order) {
                #Loop through counts
                foreach ([10, 30, 50, 51] as $count) {
                    #Get crossworld linkshells
                    $Lodestone->searchLinkshell('', $world['datacenter'], $count, $order, 1, true);
                    $Lodestone->searchLinkshell('', $world['datacenter'], $count, $order, 20, true);
                }
                #Get PvP Teams
                $Lodestone->searchPvPTeam('', $world['datacenter'], $order);
                $Lodestone->searchPvPTeam('', $world['datacenter'], $order, 20);
            }
        }
        $data = $Lodestone->getResult();
        \Simbiat\Tests\Tests::testDump($data);
        return true;
    }
}
