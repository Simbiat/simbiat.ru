<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use JetBrains\PhpStorm\ExpectedValues;
use Simbiat\Cron\Agent;
use Simbiat\Cron\EventTypes;
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
use Simbiat\Website\Entities\Notifications\CronFailure;
use Simbiat\Website\Enums\SystemUsers;
use Simbiat\Website\Errors;

/**
 * Class handling regular tasks for FFXIV tracker
 */
class FFTracker
{
    /**
     * Update statistics
     * @return void
     */
    public function updateStatistics(): void
    {
        try {
            foreach (['raw', 'characters', 'groups', 'achievements', 'timelines', 'other', 'bugs'] as $type) {
                new Statistics()->update($type);
            }
        } catch (\Throwable $throwable) {
            $error = $throwable->getMessage()."\r\n".$throwable->getTraceAsString();
            new CronFailure()->save(SystemUsers::Owner->value, ['method' => __METHOD__, 'errors' => $error], true, false, Config::ADMIN_MAIL)->send();
        }
    }
    
    /**
     * Update a FFXIV entity
     * @param string|int $id   Entity ID
     * @param string     $type Entity type
     *
     * @return bool|string
     */
    public function updateEntity(string|int $id, #[ExpectedValues(['character', 'freecompany', 'pvpteam', 'linkshell', 'crossworldlinkshell', 'crossworld_linkshell', 'achievement'])] string $type): bool|string
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
     * @noinspection PhpUnused Used from Cron Agent
     */
    public function updateOld(int $limit = 1, int $instance = 1): bool|string
    {
        #Sanitize entities number
        if ($limit < 1) {
            $limit = 1;
        }
        try {
            $entities = Query::query('
                    SELECT `type`, `id`, `priority`, `updated` FROM (
                        (SELECT \'character\' AS `type`, `ffxiv__character`.`character_id` AS `id`, `updated`, IF(`deleted` IS NULL AND `hidden` IS NULL AND `updated`<=DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) AND EXISTS(SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`), 1, 0) as `priority` FROM `ffxiv__character` ORDER BY `priority` DESC, `updated` LIMIT :max_lines OFFSET :offset)
                        UNION ALL
                        (SELECT \'freecompany\' AS `type`, `fc_id` AS `id`, `updated`, 0 as `priority` FROM `ffxiv__freecompany` ORDER BY `priority` DESC, `updated` LIMIT :max_lines OFFSET :offset)
                        UNION ALL
                        (SELECT \'pvpteam\' AS `type`, `pvp_id` AS `id`, `updated`, 0 as `priority` FROM `ffxiv__pvpteam` ORDER BY `priority` DESC, `updated` LIMIT :max_lines OFFSET :offset)
                        UNION ALL
                        (SELECT IF(`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`, `ls_id` AS `id`, `updated`, 0 as `priority` FROM `ffxiv__linkshell` ORDER BY `priority` DESC, `updated` LIMIT :max_lines OFFSET :offset)
                        UNION ALL
                        (SELECT \'achievement\' AS `type`, `achievement_id` AS `id`, `updated`, 0 AS `priority` FROM `ffxiv__achievement` as `ach_main` WHERE `achievement_id` = (SELECT `achievement_id` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character_achievement`.`character_id`=`ffxiv__character`.`character_id` WHERE `ffxiv__character_achievement`.`achievement_id` = `ach_main`.`achievement_id` AND `ffxiv__character`.`deleted` IS NULL AND `hidden_achievements` IS NULL LIMIT 1) ORDER BY `updated` LIMIT :max_lines OFFSET :offset)
                    ) `all_entities`
                    ORDER BY `priority` DESC, `updated` LIMIT :max_lines',
                [
                    ':max_lines' => [$limit, 'int'],
                    ':offset' => [($instance - 1) * $limit, 'int'],
                ], return: 'all'
            );
            $cron_agent = new Agent();
            foreach ($entities as $entity) {
                $extra_for_error = $entity['type'].' ID '.$entity['id'];
                $cron_agent->log('Updating '.$extra_for_error.'...', EventTypes::CustomInformation);
                $result = $this->updateEntity($entity['id'], $entity['type']);
                if (!\in_array($result, ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam', 'achievement', false, true], true)) {
                    #If we were throttled, sleep an extra minute, and then do an early return to reduce throttling chance on other jobs. Do not treat this as failure, though
                    if (\preg_match('/Request throttled by Lodestone/', $result) === 1) {
                        $cron_agent->log('Throttled on '.$extra_for_error.'. Sleeping...', EventTypes::CustomNotice);
                        \sleep(60);
                        return true;
                    }
                    return $result;
                }
                #Remove the cron task if it's present
                new TaskInstance('ff_update_entity', [(string)$entity['id'], $entity['type']])->delete();
            }
            return true;
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable, $extra_for_error ?? '');
            return $throwable->getMessage()."\r\n".$throwable->getTraceAsString();
        }
    }
    
    /**
     * Update the list of servers
     * @return void
     */
    public function updateServers(): void
    {
        try {
            $lodestone = (new Lodestone());
            #Get server
            $worlds = $lodestone->getWorldStatus()->getResult()['worlds'];
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
            Query::query($queries);
        } catch (\Throwable $throwable) {
            $error = $throwable->getMessage()."\r\n".$throwable->getTraceAsString();
            new CronFailure()->save(SystemUsers::Owner->value, ['method' => __METHOD__, 'errors' => $error], true, false, Config::ADMIN_MAIL)->send();
        }
    }
    
    /**
     * Generate tasks to attempt to register new characters
     * @return void
     */
    public function registerNewCharacters(): void
    {
        try {
            $cron = new TaskInstance();
            #Try to register new characters
            $max_id = Query::query('SELECT MAX(`character_id`) as `character_id` FROM `ffxiv__character`;', return: 'value');
            #We can't go higher than MySQL max unsigned integer. Unlikely we will ever get to it, but who knows?
            $new_max_id = \min($max_id + 500, 4294967295);
            if ((int)$max_id < (int)$new_max_id) {
                for ($character = $max_id + 1; (int)$character <= (int)$new_max_id; $character++) {
                    $extra_for_error = 'character ID '.$character;
                    $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$character, 'character'], 'message' => 'Updating character with ID '.$character])->add();
                }
            }
        } catch (\Throwable $exception) {
            Errors::error_log($exception, $extra_for_error ?? '');
        }
    }
    
    /**
     * Register any new linkshells found
     * @return bool|string
     * @noinspection PhpUnused Used from Cron Agent
     */
    public function registerNewLinkshells(): bool|string
    {
        try {
            $lodestone = (new Lodestone());
            $cron = new TaskInstance();
            #Generate a list of worlds for linkshells
            $worlds = Query::query(
                'SELECT `server` AS `world`, \'linkshell\' AS `entity` FROM `ffxiv__server`
                            UNION ALL
                            SELECT UNIQUE(`data_center`) AS `world`, \'crossworldlinkshell\' AS `entity` FROM `ffxiv__server`;', return: 'all'
            );
            #Get cache
            $cache_path = Config::$statistics.'linkshellPages.json';
            $json = new Caching()->getArrayFromFile($cache_path);
            $cron_agent = new Agent();
            #Loop through the servers
            $pages_parsed = 0;
            foreach ($worlds as $world) {
                #Loop through order filter
                foreach (['1', '2', '3', '4'] as $order) {
                    #Loop through the number of member's filter
                    foreach ([10, 30, 50, 51] as $count) {
                        #Loop through pages
                        $cron_agent->log('Parsing '.$world['entity'].'s on '.$world['world'].' (pages for count '.$count.', order '.$order.')...', EventTypes::CustomInformation);
                        for ($page = 1; $page <= 20; $page++) {
                            if (!\array_key_exists($page, $json[$world['entity']][$world['world']][$order][$count]) ||
                                #Count of 0 may mean that the last attempt failed (rate limit or maintenance)
                                $json[$world['entity']][$world['world']][$order][$count][$page]['count'] === 0 ||
                                #Cycle through everything every 5 days. At the time of writing, there should be less than 30000 pages, with 500 pages per hourly scan; the full cycle finishes in less than 3 days
                                \time() - $json[$world['entity']][$world['world']][$order][$count][$page]['date'] > 432000
                            ) {
                                $pages_parsed++;
                                #Get linkshells
                                try {
                                    $lodestone->searchLinkshell('', $world['world'], $count, $order, $page, $world['entity'] === 'crossworldlinkshell');
                                } catch (\Throwable $exception) {
                                    #If we were throttled, sleep an extra minute, and then do an early return to reduce throttling chance on other jobs. Do not treat this as failure, though
                                    if (\preg_match('/Lodestone has throttled the request/ui', $exception->getMessage()) === 1) {
                                        \sleep(60);
                                        return true;
                                    }
                                }
                                #Get data
                                $data = $lodestone->getResult();
                                $page_total = (int)($data['linkshells']['page_total'] ?? 0);
                                if ($page_total === 0) {
                                    continue 2;
                                }
                                #Reset Lodestone
                                $lodestone->resetResult();
                                if (!empty($data['linkshells'])) {
                                    #Clean data
                                    unset($data['linkshells']['page_current'], $data['linkshells']['page_total'], $data['linkshells']['total']);
                                    #Get IDs
                                    $data = \array_keys($data['linkshells']);
                                    #Iterrate through found items
                                    foreach ($data as $linkshell) {
                                        $extra_for_error = 'linkshell ID '.$linkshell;
                                        #Check if Linkshell exists in DB
                                        if (!Query::query('SELECT `ls_id` FROM `ffxiv__linkshell` WHERE `ls_id`=:id;', [':id' => [$linkshell, 'string']], return: 'check')) {
                                            $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$linkshell, $world['entity']], 'message' => 'Updating '.$world['entity'].' with ID '.$linkshell])->add();
                                        }
                                    }
                                    #Attempt to update cache
                                    $json[$world['entity']][$world['world']][$order][$count][$page] = ['date' => \time(), 'count' => \count($data)];
                                    \file_put_contents($cache_path, \json_encode($json, \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_OBJECT_AS_ARRAY | \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION | \JSON_PRETTY_PRINT));
                                }
                                if ($pages_parsed === 500) {
                                    #Do not parse more than 500 pages at a time
                                    return true;
                                }
                                if ($page === $page_total) {
                                    continue 2;
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $exception) {
            Errors::error_log($exception, $extra_for_error ?? '');
            return $exception->getMessage()."\r\n".$exception->getTraceAsString();
        }
        return true;
    }
}