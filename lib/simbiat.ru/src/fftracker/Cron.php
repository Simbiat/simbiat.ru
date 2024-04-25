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
            foreach (['raw', 'characters', 'groups', 'achievements', 'timelines', 'other', 'bugs'] as $type) {
                (new Statistics)->update($type);
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
                    SELECT `type`, `id`, IF(`userid` IS NOT NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY), 1, IF(`type`=\'character\' AND `clanid` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 WEEK), 2, 0)) as `priority` FROM (
                        SELECT \'character\' AS `type`, `characterid` AS `id`, `updated`, `clanid`, `userid` FROM `ffxiv__character`
                        UNION ALL
                        SELECT \'freecompany\' AS `type`, `freecompanyid` AS `id`, `updated`, null as `clanid`, (SELECT `userid` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__character` ON `ffxiv__freecompany_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__freecompany_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__freecompany`
                        UNION ALL
                        SELECT \'pvpteam\' AS `type`, `pvpteamid` AS `id`, `updated`,  null as `clanid`, (SELECT `userid` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__character` ON `ffxiv__pvpteam_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__pvpteam_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__pvpteam`
                        UNION ALL
                        SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `linkshellid` AS `id`, `updated`, null as `clanid`, (SELECT `userid` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__character` ON `ffxiv__linkshell_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` AND `ffxiv__character`.`userid` IS NOT NULL) as `userid` FROM `ffxiv__linkshell`
                        UNION ALL
                        SELECT \'achievement\' AS `type`, `achievementid` AS `id`, `updated`, null as `clanid`, NULL as `userid` FROM `ffxiv__achievement` WHERE `achievementid` IN (SELECT DISTINCT(`achievementid`) FROM `ffxiv__character_achievement`)
                    ) `allEntities`
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
            $character = (new Character((string)$characterId))->getFromLodestone();
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
        $cron = (new \Simbiat\Cron);
        $dbCon = HomePage::$dbController;
        #Try to register new characters
        $maxId = $dbCon->selectValue('SELECT MAX(`characterid`) FROM `ffxiv__character`;');
        #We can't go higher than MySQL max unsigned integer. Unlikely we will ever get to it, but who knows?
        $newMaxId = min($maxId + 100, 4294967295);
        if ($maxId < $newMaxId) {
            for ($character = $maxId + 1; $character <= $newMaxId; $character++) {
                try {
                    $cron->add('ffUpdateEntity', [$character, 'character'], message: 'Updating character with ID ' . $character);
                } catch (\Throwable) {
                    #Do nothing, not critical
                }
            }
        }
        return true;
        #Below generates need to scan a total of 29440 pages (and will probably grow)
        #Need to figure out a way to process this in a nice way
        #Generate list of worlds for linkshells
        $worlds = $dbCon->selectAll(
                        'SELECT `server` AS `world`, \'linkshell\' AS `entity` FROM `ffxiv__server`
                        UNION ALL
                        SELECT UNIQUE(`datacenter`) AS `world`, \'crossworldlinkshell\' AS `entity` FROM `ffxiv__server`;'
        );
        #Loop through the servers
        foreach ($worlds as $world) {
            #Loop through order filter
            foreach (['1', '2', '3', '4'] as $order) {
                #Loop through number of members filter
                foreach ([10, 30, 50, 51] as $count) {
                    #Loop through pages
                    for ($page = 1; $page <= 20; $page++) {
                        #Get linkshells
                        $Lodestone->searchLinkshell('', $world['world'], $count, $order, $page, $world['entity'] === 'crossworldlinkshell');
                        #Get data
                        $data = $Lodestone->getResult();
                        #Reset Lodestone
                        $Lodestone->setResult([]);
                        if (!empty($data['linkshells'])) {
                            #Clean data
                            unset($data['linkshells']['pageCurrent'], $data['linkshells']['pageTotal'], $data['linkshells']['total']);
                            #Get IDs
                            $data = array_keys($data['linkshells']);
                            #Iterrate through found items
                            foreach ($data as $linkshell) {
                                #Check if Linkshell exists in DB
                                if (!$dbCon->check('SELECT `linkshellid` FROM `ffxiv__linkshell` WHERE `linkshellid`=:id;', [':id' => [$linkshell, 'string']])) {
                                    try {
                                        $cron->add('ffUpdateEntity', [$linkshell, $world['entity']], message: 'Updating '.$world['entity'].' with ID '.$linkshell);
                                    } catch (\Throwable) {
                                        #Do nothing, not critical
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}
