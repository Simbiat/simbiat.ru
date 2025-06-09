<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use JetBrains\PhpStorm\ExpectedValues;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Achievement;
use Simbiat\FFXIV\Character;
use Simbiat\FFXIV\CrossworldLinkshell;
use Simbiat\FFXIV\FreeCompany;
use Simbiat\FFXIV\Linkshell;
use Simbiat\FFXIV\Lodestone;
use Simbiat\FFXIV\PvPTeam;
use Simbiat\FFXIV\Statistics;
use Simbiat\Website\Caching;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\usercontrol\Email;

/**
 * Class handling regular tasks for FFXIV tracker
 */
class FFTracker
{
    /**
     * Update statistics
     * @return bool|string
     */
    public function UpdateStatistics(): bool|string
    {
        try {
            foreach (['raw', 'characters', 'groups', 'achievements', 'timelines', 'other', 'bugs'] as $type) {
                new Statistics()->update($type);
            }
            return true;
        } catch (\Throwable $e) {
            $error = $e->getMessage()."\r\n".$e->getTraceAsString();
            new Email(Config::ADMIN_MAIL)->send('[Alert]: Cron task failed', ['errors' => $error], 'Simbiat');
            return $error;
        }
    }
    
    /**
     * Update a FFXIV entity
     * @param string|int $id   Entity ID
     * @param string     $type Entity type
     *
     * @return bool|string
     */
    public function UpdateEntity(string|int $id, #[ExpectedValues(['character', 'freecompany', 'pvpteam', 'linkshell', 'crossworldlinkshell', 'crossworld_linkshell', 'achievement'])] string $type): bool|string
    {
        return match ($type) {
            'character' => new Character($id)->update(true),
            'freecompany' => new FreeCompany($id)->update(true),
            'pvpteam' => new PvPTeam($id)->update(true),
            'linkshell' => new Linkshell($id)->update(true),
            'crossworldlinkshell', 'crossworld_linkshell' => new CrossworldLinkshell($id)->update(true),
            'achievement' => new Achievement($id)->update(true),
            default => false,
        };
    }
    
    /**
     * Function to update old entities
     *
     * @param int $limit    How many entities to process
     * @param int $instance Instance number that called the function
     *
     * @return bool|string
     */
    public function UpdateOld(int $limit = 1, int $instance = 1): bool|string
    {
        #Sanitize entities number
        if ($limit < 1) {
            $limit = 1;
        }
        try {
            $entities = Query::query('
                    SELECT `type`, `id`, `priority`, `updated` FROM (
                        (SELECT \'character\' AS `type`, `ffxiv__character`.`character_id` AS `id`, `updated`, IF(`user_id` IS NOT NULL AND `deleted` IS NULL AND `hidden` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY), 1, 0) as `priority` FROM `ffxiv__character` LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `priority` DESC, `updated` LIMIT :maxLines OFFSET :offset)
                        UNION ALL
                        (SELECT \'freecompany\' AS `type`, `fc_id` AS `id`, `updated`, IF(`deleted` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) AND (SELECT `user_id` FROM `ffxiv__freecompany_character` LEFT JOIN `uc__user_to_ff_character` ON `ffxiv__freecompany_character`.`character_id`=`uc__user_to_ff_character`.`character_id` WHERE `ffxiv__freecompany_character`.`fc_id`=`ffxiv__freecompany`.`fc_id` AND `ffxiv__freecompany_character`.`current` = 1 AND `uc__user_to_ff_character`.`user_id` IS NOT NULL AND `deleted` IS NULL LIMIT 1) IS NOT NULL, 1, 0) as `priority` FROM `ffxiv__freecompany` ORDER BY `priority` DESC, `updated` LIMIT :maxLines OFFSET :offset)
                        UNION ALL
                        (SELECT \'pvpteam\' AS `type`, `pvp_id` AS `id`, `updated`, IF(`deleted` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) AND (SELECT `user_id` FROM `ffxiv__pvpteam_character` LEFT JOIN `uc__user_to_ff_character` ON `ffxiv__pvpteam_character`.`character_id`=`uc__user_to_ff_character`.`character_id` WHERE `ffxiv__pvpteam_character`.`pvp_id`=`ffxiv__pvpteam`.`pvp_id` AND `ffxiv__pvpteam_character`.`current` = 1 AND `uc__user_to_ff_character`.`user_id` IS NOT NULL AND `deleted` IS NULL LIMIT 1) IS NOT NULL, 1, 0) as `priority` FROM `ffxiv__pvpteam` ORDER BY `priority` DESC, `updated` LIMIT :maxLines OFFSET :offset)
                        UNION ALL
                        (SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `ls_id` AS `id`, `updated`, IF(`deleted` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) AND (SELECT `user_id` FROM `ffxiv__linkshell_character` LEFT JOIN `uc__user_to_ff_character` ON `ffxiv__linkshell_character`.`character_id`=`uc__user_to_ff_character`.`character_id` WHERE `ffxiv__linkshell_character`.`ls_id`=`ffxiv__linkshell`.`ls_id` AND `ffxiv__linkshell_character`.`current` = 1 AND `uc__user_to_ff_character`.`user_id` IS NOT NULL AND `deleted` IS NULL LIMIT 1) IS NOT NULL, 1, 0) as `priority` FROM `ffxiv__linkshell` ORDER BY `priority` DESC, `updated` LIMIT :maxLines OFFSET :offset)
                        UNION ALL
                        (SELECT \'achievement\' AS `type`, `achievement_id` AS `id`, `updated`, 0 AS `priority` FROM `ffxiv__achievement` as `ach_main` WHERE `achievement_id` = (SELECT `achievement_id` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character_achievement`.`character_id`=`ffxiv__character`.`character_id` WHERE `ffxiv__character_achievement`.`achievement_id` = `ach_main`.`achievement_id` AND `ffxiv__character`.`deleted` IS NULL AND `hidden` IS NULL LIMIT 1) ORDER BY `updated` LIMIT :maxLines OFFSET :offset)
                    ) `allEntities`
                    ORDER BY `priority` DESC, `updated` LIMIT :maxLines',
                [
                    ':maxLines' => [$limit, 'int'],
                    ':offset' => [($instance - 1) * $limit, 'int'],
                ], return: 'all'
            );
            foreach ($entities as $entity) {
                $extraForError = $entity['type'].' ID '.$entity['id'];
                $result = $this->UpdateEntity($entity['id'], $entity['type']);
                if (!\in_array($result, ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam', 'achievement', false, true], true)) {
                    #If we were throttled, means we already slept and can continue, instead of breaking the whole instance
                    if (preg_match('/Request throttled by Lodestone/', $result) === 1) {
                        continue;
                    }
                    return $result;
                }
                #Remove the cron task if it's present
                new TaskInstance('ffUpdateEntity', [(string)$entity['id'], $entity['type']])->delete();
            }
            return true;
        } catch (\Throwable $e) {
            Errors::error_log($e, $extraForError ?? '');
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }
    
    /**
     * Update the list of servers
     * @return bool|string
     */
    public function UpdateServers(): bool|string
    {
        try {
            $Lodestone = (new Lodestone());
            #Get server
            $worlds = $Lodestone->getWorldStatus()->getResult()['worlds'];
            #Prepare queries
            $queries = [];
            foreach ($worlds as $data_center => $servers) {
                foreach ($servers as $server => $status) {
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__server` (`server`, `data_center`) VALUES (:server, :data_center)',
                        [':server' => $server, ':data_center' => $data_center],
                    ];
                }
            }
            return Query::query($queries);
        } catch (\Throwable $e) {
            $error = $e->getMessage()."\r\n".$e->getTraceAsString();
            new Email(Config::ADMIN_MAIL)->send('[Alert]: Cron task failed', ['errors' => $error], 'Simbiat');
            return $error;
        }
    }
    
    /**
     * Register new characters (if found)
     * @return bool|string
     */
    public function registerNewCharacters(): bool|string
    {
        try {
            $cron = new TaskInstance();
            #Try to register new characters
            $maxId = Query::query('SELECT MAX(`character_id`) as `character_id` FROM `ffxiv__character`;', return: 'value');
            #We can't go higher than MySQL max unsigned integer. Unlikely we will ever get to it, but who knows?
            $newMaxId = min($maxId + 100, 4294967295);
            if ((int)$maxId < (int)$newMaxId) {
                for ($character = $maxId + 1; (int)$character <= (int)$newMaxId; $character++) {
                    $extraForError = 'character ID '.$character;
                    $cron->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$character, 'character'], 'message' => 'Updating character with ID '.$character])->add();
                }
            }
        } catch (\Throwable $exception) {
            Errors::error_log($exception, $extraForError ?? '');
            return $exception->getMessage()."\r\n".$exception->getTraceAsString();
        }
        return true;
    }
    
    /**
     * Register any new linkshells found
     * @return bool|string
     */
    public function registerNewLinkshells(): bool|string
    {
        try {
            $Lodestone = (new Lodestone());
            $cron = new TaskInstance();
            #Generate a list of worlds for linkshells
            $worlds = Query::query(
                'SELECT `server` AS `world`, \'linkshell\' AS `entity` FROM `ffxiv__server`
                            UNION ALL
                            SELECT UNIQUE(`data_center`) AS `world`, \'crossworldlinkshell\' AS `entity` FROM `ffxiv__server`;', return: 'all'
            );
            #Get cache
            $cachePath = Config::$statistics.'linkshellPages.json';
            $json = new Caching()->getArrayFromFile($cachePath);
            #Loop through the servers
            $pagesParsed = 0;
            foreach ($worlds as $world) {
                #Loop through order filter
                foreach (['1', '2', '3', '4'] as $order) {
                    #Loop through the number of member's filter
                    foreach ([10, 30, 50, 51] as $count) {
                        #Loop through pages
                        for ($page = 1; $page <= 20; $page++) {
                            if (!isset($json[$world['entity']][$world['world']][$order][$count][$page]) ||
                                #Count of 0 may mean that the last attempt failed (rate limit or maintenance)
                                $json[$world['entity']][$world['world']][$order][$count][$page]['count'] === 0 ||
                                #Cycle through everything every 5 days. At the time of writing, there should be less than 30000 pages, with 500 pages per hourly scan; the full cycle finishes in less than 3 days
                                time() - $json[$world['entity']][$world['world']][$order][$count][$page]['date'] > 432000
                            ) {
                                $pagesParsed++;
                                #Get linkshells
                                $Lodestone->searchLinkshell('', $world['world'], $count, $order, $page, $world['entity'] === 'crossworldlinkshell');
                                #Get data
                                $data = $Lodestone->getResult();
                                $pageTotal = (int)($data['linkshells']['pageTotal'] ?? 0);
                                if ($pageTotal === 0) {
                                    continue 2;
                                }
                                #Reset Lodestone
                                $Lodestone->resetResult();
                                if (!empty($data['linkshells'])) {
                                    #Clean data
                                    unset($data['linkshells']['pageCurrent'], $data['linkshells']['pageTotal'], $data['linkshells']['total']);
                                    #Get IDs
                                    $data = array_keys($data['linkshells']);
                                    #Iterrate through found items
                                    foreach ($data as $linkshell) {
                                        $extraForError = 'linkshell ID '.$linkshell;
                                        #Check if Linkshell exists in DB
                                        if (!Query::query('SELECT `ls_id` FROM `ffxiv__linkshell` WHERE `ls_id`=:id;', [':id' => [$linkshell, 'string']], return: 'check')) {
                                            $cron->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$linkshell, $world['entity']], 'message' => 'Updating '.$world['entity'].' with ID '.$linkshell])->add();
                                        }
                                    }
                                    #Attempt to update cache
                                    $json[$world['entity']][$world['world']][$order][$count][$page] = ['date' => time(), 'count' => \count($data)];
                                    file_put_contents($cachePath, json_encode($json, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
                                }
                                if ($pagesParsed === 500) {
                                    #Do not parse more than 200 pages at a time
                                    return true;
                                }
                                if ($page === $pageTotal) {
                                    continue 2;
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $exception) {
            Errors::error_log($exception, $extraForError ?? '');
            return $exception->getMessage()."\r\n".$exception->getTraceAsString();
        }
        return true;
    }
}