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

class Cron
{
    private const dbPrefix = 'ffxiv__';
    #Update statistics
    public function UpdateStatistics(): bool|string
    {
        try {
            foreach (['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other'] as $type) {
                (new Statistics)->get($type, '', true);
            }
            return true;
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
                        ) `nonach`
                        UNION ALL
                        SELECT \'achievement\' AS `type`, `'.self::dbPrefix.'achievement`.`achievementid` AS `id`, `updated`, NULL AS `deleted` FROM `'.self::dbPrefix.'achievement`
                    ) `allentities`
                    ORDER BY `updated` LIMIT :maxLines',
                [
                    ':maxLines'=>[$limit, 'int'],
                ]
            );
            foreach ($entities as $entity) {
                $result = match($entity['type']) {
                    'character' => (new Character)->setId($entity['id'])->update(),
                    'freecompany' => (new FreeCompany)->setId($entity['id'])->update(),
                    'pvpteam' => (new PvPTeam)->setId($entity['id'])->update(),
                    'linkshell' => (new Linkshell)->setId($entity['id'])->update(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($entity['id'])->update(),
                    'achievement' => (new Achievement)->setId($entity['id'])->update(),
                };
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
    /**
     * @throws \Exception
     */
    public function jobsUpdate(): bool|string
    {
        #Cache controller
        $dbController = (new Controller);
        #Get the freshest character ID
        $characterId = $dbController->selectValue('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `deleted` IS NULL ORDER BY `updated` DESC LIMIT 1;');
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
            if (!$dbController->checkColumn(''.self::dbPrefix.'character', $jobNoSpace)) {
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
    }
}
