<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Database\Controller;
use Simbiat\fftracker\Entities\Achievement;
use Simbiat\fftracker\Entities\Character;
use Simbiat\fftracker\Entities\CrossworldLinkshell;
use Simbiat\fftracker\Entities\FreeCompany;
use Simbiat\fftracker\Entities\Linkshell;
use Simbiat\fftracker\Entities\PvPTeam;
use Simbiat\Lodestone;

class Cron
{
    private const dbPrefix = 'ffxiv__';
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
            'character' => (new Character)->setId($id)->update(),
            'freecompany' => (new FreeCompany)->setId($id)->update(),
            'pvpteam' => (new PvPTeam)->setId($id)->update(),
            'linkshell' => (new Linkshell)->setId($id)->update(),
            'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($id)->update(),
            'achievement' => (new Achievement)->setId($id)->update(),
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
            $dbCon = (new Controller);
            $entities = $dbCon->selectAll('
                    SELECT `type`, `id` FROM (
                        SELECT * FROM (
                            SELECT \'character\' AS `type`, `characterid` AS `id`, `updated`, `deleted` FROM `'.self::dbPrefix.'character`
                            UNION ALL
                            SELECT \'freecompany\' AS `type`, `freecompanyid` AS `id`, `updated`, `deleted` FROM `'.self::dbPrefix.'freecompany`
                            UNION ALL
                            SELECT \'pvpteam\' AS `type`, `pvpteamid` AS `id`, `updated`, `deleted` FROM `'.self::dbPrefix.'pvpteam`
                            UNION ALL
                            SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `linkshellid` AS `id`, `updated`, `deleted` FROM `'.self::dbPrefix.'linkshell`
                            WHERE `deleted` IS NULL
                        ) `nonAch`
                        UNION ALL
                        SELECT \'achievement\' AS `type`, `'.self::dbPrefix.'achievement`.`achievementid` AS `id`, `updated`, NULL AS `deleted` FROM `'.self::dbPrefix.'achievement`
                    ) `allEntities`
                    ORDER BY `updated` LIMIT :maxLines',
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
            $dbController = (new Controller);
            #Get the freshest character ID
            $characterId = $dbController->selectValue('SELECT `characterid` FROM `' . self::dbPrefix . 'character` WHERE `deleted` IS NULL ORDER BY `updated` DESC LIMIT 1;');
            #Grab its data from Lodestone
            $character = (new Character)->setId($characterId)->getFromLodestone();
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
                if (!$dbController->checkColumn(self::dbPrefix.'character', $jobNoSpace)) {
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
            return $dbController->query('ALTER TABLE `'.self::dbPrefix.'character` '.implode(', ', $alter).';');
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
                        'INSERT IGNORE INTO `'.self::dbPrefix.'server` (`server`, `dataCenter`) VALUES (:server, :dataCenter)',
                        [':server' => $server, ':dataCenter' => $dataCenter],
                    ];
                }
            }
            return (new Controller)->query($queries);
        } catch (\Throwable $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Register new entities (if found)
    public function registerNew(): bool|string
    {
        $Lodestone = (new Lodestone);
        $dbCon = (new Controller);
        #Generate list of pages to parse (every hour 256 pages to scan, 2 seconds delay for each?)
        try {
            $worlds = $dbCon->selectAll('
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'linkshell\' AS `entity` FROM `'.self::dbPrefix.'server`
                    CROSS JOIN `'.self::dbPrefix.'orderby`
                    CROSS JOIN `'.self::dbPrefix.'count_filter`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `datacenter` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'crossworldlinkshell\' AS `entity` FROM `'.self::dbPrefix.'orderby`
                    CROSS JOIN `'.self::dbPrefix.'count_filter`
                    CROSS JOIN (
                        SELECT UNIQUE(`datacenter`) FROM `'.self::dbPrefix.'server`
                    ) `dataCenters`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, `value` AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'freecompany\' AS `entity` FROM `'.self::dbPrefix.'server`
                    CROSS JOIN `'.self::dbPrefix.'orderby`
                    CROSS JOIN `'.self::dbPrefix.'count_filter`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 3, 4)
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, 5 AS `order`, \'\' AS `count`, `page`, `gcId`, \'\' AS `clanid`, \'freecompany\' AS `entity` FROM `'.self::dbPrefix.'server`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 2 AS `page` UNION SELECT 3 AS `page` UNION SELECT 4 AS `page` UNION SELECT 5 AS `page` UNION SELECT 6 AS `page` UNION SELECT 7 AS `page` UNION SELECT 8 AS `page` UNION SELECT 9 AS `page` UNION SELECT 10 AS `page` UNION SELECT 11 AS `page` UNION SELECT 12 AS `page` UNION SELECT 13 AS `page` UNION SELECT 14 AS `page` UNION SELECT 15 AS `page` UNION SELECT 16 AS `page` UNION SELECT 17 AS `page` UNION SELECT 18 AS `page` UNION SELECT 19 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    CROSS JOIN `'.self::dbPrefix.'grandcompany`
                    WHERE `gcId` <> 0
                )
                UNION ALL
                (
                    SELECT `server` AS `world`, `orderID` AS `order`, \'\' AS `count`, `page`, `gcId`, `clanid`, \'character\' AS `entity` FROM `'.self::dbPrefix.'server`
                    CROSS JOIN `'.self::dbPrefix.'orderby`
                    CROSS JOIN `'.self::dbPrefix.'grandcompany`
                    CROSS JOIN `'.self::dbPrefix.'clan`
                    CROSS JOIN (
                        SELECT 1 AS `page` UNION SELECT 20 AS `page`
                    ) `pages`
                    WHERE `orderID` IN (1, 2, 5, 6)
                )
                UNION ALL
                (
                    SELECT `datacenter` AS `world`, `orderID` AS `order`, \'\' AS `count`, `page`, \'\' AS `gcId`, \'\' AS `clanid`, \'pvpteam\' AS `entity` FROM `'.self::dbPrefix.'orderby`
                    CROSS JOIN (
                        SELECT UNIQUE(`datacenter`) FROM `'.self::dbPrefix.'server`
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
            $worlds = $dbCon->selectUnique('SELECT `datacenter` FROM `' . self::dbPrefix . 'server`');
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
        (new \Simbiat\Tests)->testDump($data);
        return true;
    }
}
