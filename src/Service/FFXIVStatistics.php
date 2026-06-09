<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\FFXIV\AbstractEntity;
use JetBrains\PhpStorm\ExpectedValues;
use Simbiat\ArrayHelpers\Converters;
use Simbiat\ArrayHelpers\Editors;
use Simbiat\ArrayHelpers\Splitters;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Query;
use function in_array;

/**
 * Class responsible for generating FFXIV statistics
 */
class FFXIVStatistics
{
    /**
     * List of supported statistics types
     */
    private const array STATISTICS_TYPE = ['raw', 'characters', 'groups', 'achievements', 'timelines', 'bugs', 'other'];
    
    /**
     * @param string $type
     * @internal
     * @throws \JsonException
     * @throws \Exception
     */
    public function update(#[ExpectedValues(self::STATISTICS_TYPE)] string $type = 'other'): void
    {
        $data = [];
        #Sanitize type
        if (!in_array($type, self::STATISTICS_TYPE, true)) {
            $type = 'other';
        }
        #Create a path if missing
        if (!\is_dir(Config::$statistics) && !\mkdir(Config::$statistics) && !\is_dir(Config::$statistics)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', Config::$statistics));
        }
        $cache_path = Config::$statistics.$type.'.json';
        $data['time'] = \time();
        switch ($type) {
            case 'raw':
                $this->getRaw($data);
                break;
            case 'characters':
                $this->getCharacters($data);
                break;
            case 'groups':
                $this->getGroups($data);
                break;
            case 'achievements':
                $this->getAchievements($data);
                break;
            case 'timelines':
                $this->getTimelines($data);
                break;
            case 'bugs':
                $this->getBugs($data);
                break;
            case 'other':
                $this->getOthers($data);
                break;
        }
        #Attempt to write to cache
        \file_put_contents($cache_path, \json_encode($data, \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_OBJECT_AS_ARRAY | \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION | \JSON_PRETTY_PRINT));
        if ($type === 'bugs') {
            $this->scheduleBugs($data);
        }
    }
    
    /**
     * Get data raw data for character statistics based on counts
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getRaw(array &$data): void
    {
        $data['raw'] = Query::query(
            'SELECT COUNT(*) as `count`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`gender`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gc_name` FROM `ffxiv__character`
                                LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clan_id`=`ffxiv__clan`.`clan_id`
                                LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`city_id`=`ffxiv__city`.`city_id`
                                LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardian_id`=`ffxiv__guardian`.`guardian_id`
                                LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gc_rank_id`=`ffxiv__grandcompany_rank`.`gc_rank_id`
                                LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gc_id`=`ffxiv__grandcompany`.`gc_id`
                                WHERE `ffxiv__character`.`clan_id` IS NOT NULL GROUP BY `ffxiv__clan`.`race`, `ffxiv__clan`.`clan`, `ffxiv__character`.`gender`, `ffxiv__guardian`.`guardian`, `ffxiv__city`.`city_id`, `ffxiv__grandcompany_rank`.`gc_id` ORDER BY `count` DESC;
                    ', return: 'all');
    }
    
    /**
     * Get data for character-based statistics
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getCharacters(array &$data): void
    {
        #Jobs popularity
        $data['characters']['jobs'] = Query::query(
            'SELECT `name`, SUM(`level`) as `sum` FROM `ffxiv__character_jobs` LEFT JOIN `ffxiv__jobs` ON `ffxiv__jobs`.`job_id`=`ffxiv__character_jobs`.`job_id` GROUP BY `ffxiv__character_jobs`.`job_id` ORDER BY `sum` DESC;', return: 'pair'
        );
        #Most name changes
        $data['characters']['changes']['name'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__character_names`.`character_id`, count(`ffxiv__character_names`.`character_id`) AS `count` FROM `ffxiv__character_names` GROUP BY `ffxiv__character_names`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['changes']['name'], 'value', 'name');
        #Most reincarnation
        $data['characters']['changes']['clan'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__character_clans`.`character_id`, count(`ffxiv__character_clans`.`character_id`) AS `count` FROM `ffxiv__character_clans` GROUP BY `ffxiv__character_clans`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['changes']['clan'], 'value', 'name');
        #Most servers
        $data['characters']['changes']['server'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__character_servers`.`character_id`, count(`ffxiv__character_servers`.`character_id`) AS `count` FROM `ffxiv__character_servers` GROUP BY `ffxiv__character_servers`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['changes']['server'], 'value', 'name');
        #Most companies
        $data['characters']['groups']['Free Companies'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__freecompany_character`.`character_id`, count(`ffxiv__freecompany_character`.`character_id`) AS `count` FROM `ffxiv__freecompany_character` GROUP BY `ffxiv__freecompany_character`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['groups']['Free Companies'], 'value', 'name');
        #Most PvP teams
        $data['characters']['groups']['PvP Teams'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__pvpteam_character`.`character_id`, count(`ffxiv__pvpteam_character`.`character_id`) AS `count` FROM `ffxiv__pvpteam_character` GROUP BY `ffxiv__pvpteam_character`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['groups']['PvP Teams'], 'value', 'name');
        #Most x-linkshells
        $data['characters']['groups']['Linkshells'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__linkshell_character`.`character_id`, count(`ffxiv__linkshell_character`.`character_id`) AS `count` FROM `ffxiv__linkshell_character` GROUP BY `ffxiv__linkshell_character`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['groups']['Linkshells'], 'value', 'name');
        #Most linkshells
        $data['characters']['groups']['simultaneous_linkshells'] = Query::query('SELECT `tempresult`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name` AS `value`, `count` FROM (SELECT `ffxiv__linkshell_character`.`character_id`, count(`ffxiv__linkshell_character`.`character_id`) AS `count` FROM `ffxiv__linkshell_character` WHERE `ffxiv__linkshell_character`.`current`=1 GROUP BY `ffxiv__linkshell_character`.`character_id` ORDER BY `count` DESC LIMIT 20) `tempresult` INNER JOIN `ffxiv__character` ON `tempresult`.`character_id`=`ffxiv__character`.`character_id` ORDER BY `count` DESC', return: 'all');
        Editors::renameColumn($data['characters']['groups']['simultaneous_linkshells'], 'value', 'name');
        #Groups affiliation
        $data['characters']['groups']['participation'] = Query::query('
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
                            SELECT `character_id`,
                                EXISTS(SELECT `character_id` FROM `ffxiv__freecompany_character` WHERE `character_id`=`main`.`character_id` AND `current`=1) as `fc`,
                                EXISTS(SELECT `character_id` FROM `ffxiv__pvpteam_character` WHERE `character_id`=`main`.`character_id` AND `current`=1) as `pvp`,
                                EXISTS(SELECT `character_id` FROM `ffxiv__linkshell_character` WHERE `character_id`=`main`.`character_id` AND `current`=1) as `ls`
                            FROM `ffxiv__character` AS `main` WHERE `deleted` IS NULL
                        ) as `temp`
                        GROUP BY `affiliation`;
                    ', return: 'all');
        #Get characters with most PvP matches. Using regular SQL since we do not count unique values, but rather use the regular column values
        $data['characters']['most_pvp'] = Query::query('SELECT `ffxiv__character`.`character_id` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`, `pvp_matches` AS `count` FROM `ffxiv__character` ORDER BY `ffxiv__character`.`pvp_matches` DESC LIMIT 20', return: 'all');
        #Characters
        $data['servers']['characters'] = Query::query('SELECT `ffxiv__character`.`gender`, `ffxiv__server`.`server` AS `value`, count(`ffxiv__character`.`server_id`) AS `count` FROM `ffxiv__character` INNER JOIN `ffxiv__server` ON `ffxiv__character`.`server_id`=`ffxiv__server`.`server_id` WHERE `ffxiv__character`.`deleted` IS NULL GROUP BY `ffxiv__character`.`gender`, `value` ORDER BY `count` DESC', return: 'all');
    }
    
    /**
     * Get data for statistics related to different groups
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getGroups(array &$data): void
    {
        #Get most popular estate locations
        $data['freecompany']['estate'] = Splitters::topAndBottom(Query::query('SELECT `ffxiv__estate`.`area`, `ffxiv__estate`.`plot`, CONCAT(`ffxiv__estate`.`area`, \', plot \', `ffxiv__estate`.`plot`) AS `value`, count(`ffxiv__freecompany`.`estate_id`) AS `count` FROM `ffxiv__freecompany` INNER JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estate_id`=`ffxiv__estate`.`estate_id` WHERE `ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estate_id` IS NOT NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all'), 20);
        #Get statistics by activity time
        $data['freecompany']['active'] = Query::query('SELECT IF(`ffxiv__freecompany`.`recruitment`=1, \'Recruiting\', \'Not recruiting\') AS `recruiting`, SUM(IF(`ffxiv__freecompany`.`active_id` = 1, 1, 0)) AS `Always`, SUM(IF(`ffxiv__freecompany`.`active_id` = 2, 1, 0)) AS `Weekdays`, SUM(IF(`ffxiv__freecompany`.`active_id` = 3, 1, 0)) AS `Weekends` FROM `ffxiv__freecompany` INNER JOIN `ffxiv__timeactive` ON `ffxiv__freecompany`.`active_id`=`ffxiv__timeactive`.`active_id` WHERE `ffxiv__freecompany`.`deleted` IS NULL GROUP BY 1 ORDER BY 1 DESC', return: 'all');
        #Get statistics by activities
        $data['freecompany']['activities'] = Query::query('SELECT  SUM(`role_playing`)/COUNT(`fc_id`)*100 AS `Role-playing`, SUM(`leveling`)/COUNT(`fc_id`)*100 AS `Leveling`, SUM(`casual`)/COUNT(`fc_id`)*100 AS `Casual`, SUM(`hardcore`)/COUNT(`fc_id`)*100 AS `Hardcore`, SUM(`dungeons`)/COUNT(`fc_id`)*100 AS `Dungeons`, SUM(`guildhests`)/COUNT(`fc_id`)*100 AS `Guildhests`, SUM(`trials`)/COUNT(`fc_id`)*100 AS `Trials`, SUM(`raids`)/COUNT(`fc_id`)*100 AS `Raids`, SUM(`pvp`)/COUNT(`fc_id`)*100 AS `PvP` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL', return: 'row');
        \arsort($data['freecompany']['activities']);
        #Get statistics by job search
        $data['freecompany']['job_demand'] = Query::query('SELECT SUM(`tank`)/COUNT(`fc_id`)*100 AS `Tank`, SUM(`healer`)/COUNT(`fc_id`)*100 AS `Healer`, SUM(`dps`)/COUNT(`fc_id`)*100 AS `DPS`, SUM(`crafter`)/COUNT(`fc_id`)*100 AS `Crafter`, SUM(`gatherer`)/COUNT(`fc_id`)*100 AS `Gatherer` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL', return: 'row');
        \arsort($data['freecompany']['job_demand']);
        #Get statistics for grand companies for characters
        $data['gc_characters'] = Query::query(
            'SELECT COUNT(*) as `count`, `ffxiv__character`.`gender`, `ffxiv__grandcompany`.`gc_name`, `ffxiv__grandcompany_rank`.`gc_rank` FROM `ffxiv__character`
                                LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gc_rank_id`=`ffxiv__grandcompany_rank`.`gc_rank_id`
                                LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gc_id`=`ffxiv__grandcompany`.`gc_id`
                                WHERE `ffxiv__character`.`gc_rank_id` IS NOT NULL GROUP BY `ffxiv__character`.`gender`, `ffxiv__grandcompany`.`gc_name`, `ffxiv__grandcompany_rank`.`gc_rank` ORDER BY `count` DESC;
                    ', return: 'all');
        #Get statistics for grand companies for free companies
        $data['gc_companies'] = Query::query('SELECT `ffxiv__grandcompany`.`gc_name` AS `value`, count(`ffxiv__freecompany`.`gc_id`) AS `count` FROM `ffxiv__freecompany` INNER JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`gc_id`=`ffxiv__grandcompany`.`gc_id` GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #City by free company
        $data['cities']['free_company'] = Query::query('SELECT `ffxiv__estate`.`area` AS `value`, count(`ffxiv__freecompany`.`estate_id`) AS `count` FROM `ffxiv__freecompany` INNER JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estate_id`=`ffxiv__estate`.`estate_id` WHERE `ffxiv__freecompany`.`estate_id` IS NOT NULL AND `ffxiv__freecompany`.`deleted` IS NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #Grand companies distribution (free companies)
        $data['cities']['gc_fc'] = Query::query('SELECT `ffxiv__city`.`city`, `ffxiv__grandcompany`.`gc_name` AS `value`, COUNT(`ffxiv__freecompany`.`fc_id`) AS `count` FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estate_id`=`ffxiv__estate`.`estate_id` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`city_id`=`ffxiv__city`.`city_id` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__freecompany`.`gc_id`=`ffxiv__grandcompany_rank`.`gc_rank_id` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`gc_id`=`ffxiv__grandcompany`.`gc_id` WHERE `ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estate_id` IS NOT NULL AND `ffxiv__grandcompany`.`gc_name` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC', return: 'all');
        #Free companies
        $data['servers']['Free Companies'] = Query::query('SELECT `ffxiv__server`.`server` AS `value`, count(`ffxiv__freecompany`.`server_id`) AS `count` FROM `ffxiv__freecompany` INNER JOIN `ffxiv__server` ON `ffxiv__freecompany`.`server_id`=`ffxiv__server`.`server_id` WHERE `ffxiv__freecompany`.`deleted` IS NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #Linkshells
        $data['servers']['Linkshells'] = Query::query('SELECT `ffxiv__server`.`server` AS `value`, count(`ffxiv__linkshell`.`server_id`) AS `count` FROM `ffxiv__linkshell` INNER JOIN `ffxiv__server` ON `ffxiv__linkshell`.`server_id`=`ffxiv__server`.`server_id` WHERE `ffxiv__linkshell`.`crossworld` = 0 AND `ffxiv__linkshell`.`deleted` IS NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #Crossworld linkshells
        $data['servers']['crossworldlinkshell'] = Query::query('SELECT `ffxiv__server`.`data_center` AS `value`, count(`ffxiv__linkshell`.`server_id`) AS `count` FROM `ffxiv__linkshell` INNER JOIN `ffxiv__server` ON `ffxiv__linkshell`.`server_id`=`ffxiv__server`.`server_id` WHERE `ffxiv__linkshell`.`crossworld` = 1 AND `ffxiv__linkshell`.`deleted` IS NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #PvP teams
        $data['servers']['pvpteam'] = Query::query('SELECT `ffxiv__server`.`data_center` AS `value`, count(`ffxiv__pvpteam`.`data_center_id`) AS `count` FROM `ffxiv__pvpteam` INNER JOIN `ffxiv__server` ON `ffxiv__pvpteam`.`data_center_id`=`ffxiv__server`.`server_id` WHERE `ffxiv__pvpteam`.`deleted` IS NULL GROUP BY `value` ORDER BY `count` DESC', return: 'all');
        #Get the most popular crests for companies
        $data['freecompany']['crests'] = AbstractEntity::cleanCrestResults(Query::query('SELECT COUNT(*) AS `count`, `crest_part_1`, `crest_part_2`, `crest_part_3` FROM `ffxiv__freecompany` GROUP BY `crest_part_1`, `crest_part_2`, `crest_part_3` ORDER BY `count` DESC LIMIT 20;', return: 'all'));
        #Get the most popular crests for PvP Teams
        $data['pvpteam']['crests'] = AbstractEntity::cleanCrestResults(Query::query('SELECT COUNT(*) AS `count`, `crest_part_1`, `crest_part_2`, `crest_part_3` FROM `ffxiv__pvpteam` GROUP BY `crest_part_1`, `crest_part_2`, `crest_part_3` ORDER BY `count` DESC LIMIT 20;', return: 'all'));
    }
    
    /**
     * Get data for achievements' statistics
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getAchievements(array &$data): void
    {
        #Get achievements statistics
        $data['achievements'] = Query::query(
            'SELECT \'achievement\' as `type`, `category`, `achievement_id` AS `id`, `icon`, `name`, `earned_by` AS `count`
                    FROM `ffxiv__achievement`
                    WHERE `ffxiv__achievement`.`category` IS NOT NULL AND `earned_by`>0 ORDER BY `count`;', return: 'all'
        );
        #Split achievements by categories
        $data['achievements'] = Splitters::splitByKey($data['achievements'], 'category');
        #Get only the top 20 for each category
        foreach ($data['achievements'] as $key => $category) {
            $data['achievements'][$key] = \array_slice($category, 0, 20);
        }
        #Get the most and least popular titles
        $data['titles'] = Splitters::topAndBottom(Query::query('SELECT COUNT(*) as `count`, `ffxiv__achievement`.`title`, `ffxiv__achievement`.`achievement_id` FROM `ffxiv__character` LEFT JOIN `ffxiv__achievement` ON `ffxiv__achievement`.`achievement_id`=`ffxiv__character`.`title_id` WHERE `ffxiv__character`.`title_id` IS NOT NULL GROUP BY `title_id` ORDER BY `count` DESC;', return: 'all'), 20);
        
    }
    
    /**
     * Get data statistics linked to dates
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getTimelines(array &$data): void
    {
        #Get namedays timeline. Using custom SQL, since need special order by `nameday_id`, instead of by `count`
        $data['namedays'] = Query::query('SELECT `ffxiv__nameday`.`nameday` AS `value`, COUNT(`ffxiv__character`.`nameday_id`) AS `count` FROM `ffxiv__character` INNER JOIN `ffxiv__nameday` ON `ffxiv__character`.`nameday_id`=`ffxiv__nameday`.`nameday_id` GROUP BY `ffxiv__nameday`.`nameday_id` ORDER BY `count` DESC;', return: 'all');
        #Timeline of entities formation, updates, etc.
        $data['timelines'] = Query::query(
            'SELECT DATE(`registered`) AS `date`, COUNT(*) AS `count`, \'characters_registered\' as `type` FROM `ffxiv__character` GROUP BY `date`
                            UNION
                            SELECT DATE(`deleted`) AS `date`, COUNT(*) AS `count`, \'characters_deleted\' as `type` FROM `ffxiv__character` WHERE `deleted` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`hidden`) AS `date`, COUNT(*) AS `count`, \'characters_hidden\' as `type` FROM `ffxiv__character` WHERE `hidden` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`formed`) AS `date`, COUNT(*) AS `count`, \'free_companies_formed\' as `type` FROM `ffxiv__freecompany` GROUP BY `date`
                            UNION
                            SELECT DATE(`registered`) AS `date`, COUNT(*) AS `count`, \'free_companies_registered\' as `type` FROM `ffxiv__freecompany` GROUP BY `date`
                            UNION
                            SELECT DATE(`deleted`) AS `date`, COUNT(*) AS `count`, \'free_companies_deleted\' as `type` FROM `ffxiv__freecompany` WHERE `deleted` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`formed`) AS `date`, COUNT(*) AS `count`, \'pvp_teams_formed\' as `type` FROM `ffxiv__pvpteam` WHERE `formed` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`registered`) AS `date`, COUNT(*) AS `count`, \'pvp_teams_registered\' as `type` FROM `ffxiv__pvpteam` GROUP BY `date`
                            UNION
                            SELECT DATE(`deleted`) AS `date`, COUNT(*) AS `count`, \'pvp_teams_deleted\' as `type` FROM `ffxiv__pvpteam` WHERE `deleted` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`formed`) AS `date`, COUNT(*) AS `count`, \'linkshells_formed\' as `type` FROM `ffxiv__linkshell` WHERE `formed` IS NOT NULL GROUP BY `date`
                            UNION
                            SELECT DATE(`registered`) AS `date`, COUNT(*) AS `count`, \'linkshells_registered\' as `type` FROM `ffxiv__linkshell` GROUP BY `date`
                            UNION
                            SELECT DATE(`deleted`) AS `date`, COUNT(*) AS `count`, \'linkshells_deleted\' as `type` FROM `ffxiv__linkshell` WHERE `deleted` IS NOT NULL GROUP BY `date`;', return: 'all'
        );
        $data['timelines'] = Splitters::splitByKey($data['timelines'], 'date');
        foreach ($data['timelines'] as $date => $datapoint) {
            $data['timelines'][$date] = Converters::multiToSingle(Editors::digitToKey($datapoint, 'type', true), 'count');
        }
        \krsort($data['timelines']);
    }
    
    /**
     * Get data for potential data bugs
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getBugs(array &$data): void
    {
        #Characters with no clan/race
        $data['bugs']['no_clan'] = Query::query('SELECT `character_id` AS `id`, `name`, `avatar` AS `icon`, \'character\' AS `type` FROM `ffxiv__character` WHERE `clan_id` IS NULL AND `deleted` IS NULL AND `hidden` IS NULL ORDER BY `updated`, `name`;', return: 'all');
        #Characters with no avatar
        $data['bugs']['no_avatar'] = Query::query('SELECT `character_id` AS `id`, `name`, `avatar` AS `icon`, \'character\' AS `type` FROM `ffxiv__character` WHERE `avatar` LIKE \'defaultf%\' AND `deleted` IS NULL AND `hidden` IS NULL ORDER BY `updated`, `name`;', return: 'all');
        #Groups with no members
        $data['bugs']['no_members'] = AbstractEntity::cleanCrestResults(Query::query(
            'SELECT `fc_id` AS `id`, `name`, \'freecompany\' AS `type`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `gc_id` FROM `ffxiv__freecompany` as `fc` WHERE `deleted` IS NULL AND `fc_id` NOT IN (SELECT `fc_id` FROM `ffxiv__freecompany_character` WHERE `fc_id`=`fc`.`fc_id` AND `current`=1)
                        UNION
                        SELECT `ls_id` AS `id`, `name`, IF(`crossworld`=1, \'crossworldlinkshell\', \'linkshell\') AS `type`, null as `crest_part_1`, null as `crest_part_2`, null as `crest_part_3`, null as `gc_id` FROM `ffxiv__linkshell` as `ls` WHERE `deleted` IS NULL AND `ls_id` NOT IN (SELECT `ls_id` FROM `ffxiv__linkshell_character` WHERE `ls_id`=`ls`.`ls_id` AND `current`=1)
                        UNION
                        SELECT `pvp_id` AS `id`, `name`, \'pvpteam\' AS `type`, `crest_part_1`, `crest_part_2`, `crest_part_3`, null as `gc_id` FROM `ffxiv__pvpteam` as `pvp` WHERE `deleted` IS NULL AND `pvp_id` NOT IN (SELECT `pvp_id` FROM `ffxiv__pvpteam_character` WHERE `pvp_id`=`pvp`.`pvp_id` AND `current`=1)
                        ORDER BY `name`;', return: 'all'
        ));
        #Get entities with duplicate names
        $duplicate_names = Query::query(
        /** @lang MariaDB */ '(
                        SELECT
                            \'character\' AS `type`,
                            `f`.`character_id` AS `id`,
                            `f`.`name`,
                            `f`.`avatar` AS `icon`,
                            `u`.`user_id`,
                            NULL AS `crest_part_1`,
                            NULL AS `crest_part_2`,
                            NULL AS `crest_part_3`,
                            `s`.`server`,
                            `s`.`data_center`
                        FROM `ffxiv__character` `f`
                        JOIN (
                            SELECT `name`, `server_id`
                            FROM `ffxiv__character`
                            WHERE `deleted` IS NULL AND `hidden` IS NULL
                            GROUP BY `name`, `server_id`
                            HAVING COUNT(*) > 1
                        ) `dups`
                            ON `dups`.`name` = `f`.`name`
                           AND `dups`.`server_id` = `f`.`server_id`
                        LEFT JOIN `uc__user_to_ff_character` `u`
                            ON `u`.`character_id` = `f`.`character_id`
                        LEFT JOIN `ffxiv__server` `s`
                            ON `s`.`server_id` = `f`.`server_id`
                        WHERE `f`.`deleted` IS NULL
                          AND `f`.`hidden` IS NULL
                    )
                    UNION ALL
                    (
                        SELECT
                            \'freecompany\' AS `type`,
                            `f`.`fc_id` AS `id`,
                            `f`.`name`,
                            NULL AS `icon`,
                            NULL AS `user_id`,
                            `f`.`crest_part_1`,
                            `f`.`crest_part_2`,
                            `f`.`crest_part_3`,
                            `s`.`server`,
                            `s`.`data_center`
                        FROM `ffxiv__freecompany` `f`
                        JOIN (
                            SELECT `name`, `server_id`
                            FROM `ffxiv__freecompany`
                            WHERE `deleted` IS NULL
                            GROUP BY `name`, `server_id`
                            HAVING COUNT(*) > 1
                        ) `d`
                          ON `d`.`name` = `f`.`name`
                         AND `d`.`server_id` = `f`.`server_id`
                        LEFT JOIN `ffxiv__server` `s`
                          ON `s`.`server_id` = `f`.`server_id`
                        WHERE `f`.`deleted` IS NULL
                    )
                    UNION ALL
                    (
                        SELECT
                            \'pvpteam\' AS `type`,
                            `f`.`pvp_id` AS `id`,
                            `f`.`name`,
                            NULL AS `icon`,
                            NULL AS `user_id`,
                            `f`.`crest_part_1`,
                            `f`.`crest_part_2`,
                            `f`.`crest_part_3`,
                            `s`.`server`,
                            `s`.`data_center`
                        FROM `ffxiv__pvpteam` `f`
                        JOIN (
                            SELECT `name`, `data_center_id`
                            FROM `ffxiv__pvpteam`
                            WHERE `deleted` IS NULL
                            GROUP BY `name`, `data_center_id`
                            HAVING COUNT(*) > 1
                        ) `d`
                          ON `d`.`name` = `f`.`name`
                         AND `d`.`data_center_id` = `f`.`data_center_id`
                        LEFT JOIN `ffxiv__server` `s`
                          ON `s`.`server_id` = `f`.`data_center_id`
                        WHERE `f`.`deleted` IS NULL
                    )
                    UNION ALL
                    (
                        SELECT
                            IF(`f`.`crossworld` = 0, \'linkshell\', \'crossworldlinkshell\') AS `type`,
                            `f`.`ls_id` AS `id`,
                            `f`.`name`,
                            NULL AS `icon`,
                            NULL AS `user_id`,
                            NULL AS `crest_part_1`,
                            NULL AS `crest_part_2`,
                            NULL AS `crest_part_3`,
                            `s`.`server`,
                            `s`.`data_center`
                        FROM `ffxiv__linkshell` `f`
                        JOIN (
                            SELECT `name`, `server_id`, `crossworld`
                            FROM `ffxiv__linkshell`
                            WHERE `deleted` IS NULL
                            GROUP BY `name`, `server_id`, `crossworld`
                            HAVING COUNT(*) > 1
                        ) d
                          ON `d`.`name` = `f`.`name`
                         AND `d`.`server_id` = `f`.`server_id`
                         AND `d`.`crossworld` = `f`.`crossworld`
                        LEFT JOIN `ffxiv__server` `s`
                          ON `s`.`server_id` = `f`.`server_id`
                        WHERE `f`.`deleted` IS NULL
                    );', return: 'all'
        );
        #Split by the entity type
        $data['bugs']['duplicate_names'] = Splitters::splitByKey($duplicate_names, 'type', keep_key: true);
        foreach ($data['bugs']['duplicate_names'] as $entity_type => $names_data) {
            #Split by server/data center
            $data['bugs']['duplicate_names'][$entity_type] = Splitters::splitByKey($names_data, (in_array($entity_type, ['pvpteam', 'crosswordlinkshell']) ? 'data_center' : 'server'));
            foreach ($data['bugs']['duplicate_names'][$entity_type] as $server => $server_data) {
                #Split by name
                $data['bugs']['duplicate_names'][$entity_type][$server] = Splitters::splitByKey($server_data, 'name', keep_key: true, case_insensitive: true);
                foreach ($data['bugs']['duplicate_names'][$entity_type][$server] as $name => $name_data) {
                    if (in_array($entity_type, ['freecompany', 'pvpteam'])) {
                        $name_data = AbstractEntity::cleanCrestResults($name_data);
                    }
                    foreach ($name_data as $key => $duplicates) {
                        #Clean up
                        unset($duplicates['crest_part_1'], $duplicates['crest_part_2'], $duplicates['crest_part_3']);
                        if (in_array($entity_type, ['crossworldlinkshell', 'pvpteam'])) {
                            unset($duplicates['server']);
                        } else {
                            unset($duplicates['data_center']);
                        }
                        #Update array
                        $data['bugs']['duplicate_names'][$entity_type][$server][$name][$key] = $duplicates;
                    }
                }
            }
        }
    }
    
    /**
     * Get data for uncategorized statistics
     *
     * @param array $data Array of gathered data
     *
     * @return void
     */
    private function getOthers(array &$data): void
    {
        #Communities
        $data['other']['communities'] = Query::query(/** @lang MySQL */ '
                            (SELECT \'Free Company\' AS `type`, \'No community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL AND `community_id` IS NULL)
                            UNION ALL
                            (SELECT \'Free Company\' AS `type`, \'Community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL AND `community_id` IS NOT NULL)
                            UNION ALL
                            (SELECT \'PvP Team\' AS `type`, \'No community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL AND `community_id` IS NULL)
                            UNION ALL
                            (SELECT \'PvP Team\' AS `type`, \'Community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL AND `community_id` IS NOT NULL)
                            UNION ALL
                            (SELECT \'Linkshell\' AS `type`, \'No community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `community_id` IS NULL AND `crossworld`=0)
                            UNION ALL
                            (SELECT \'Linkshell\' AS `type`, \'Community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `community_id` IS NOT NULL AND `crossworld`=0)
                            UNION ALL
                            (SELECT \'Crossworld Linkshell\' AS `type`, \'No community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `community_id` IS NULL AND `crossworld`=1)
                            UNION ALL
                            (SELECT \'Crossworld Linkshell\' AS `type`, \'Community\' AS `value`, COUNT(*) as `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `community_id` IS NOT NULL AND `crossworld`=1)
                            ORDER BY `type`, `value`
                    ', return: 'all');
        #Deleted entities statistics
        $data['other']['entities'] = Query::query(/** @lang MySQL */ '
                            (SELECT \'Active Character\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__character` WHERE `deleted` IS NULL)
                            UNION ALL
                            (SELECT \'Deleted Character\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__character` WHERE `deleted` IS NOT NULL)
                            UNION ALL
                            (SELECT \'Active Free Company\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL)
                            UNION ALL
                            (SELECT \'Deleted Free Company\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__freecompany` WHERE `deleted` IS NOT NULL)
                            UNION ALL
                            (SELECT \'Active PvP Team\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL)
                            UNION ALL
                            (SELECT \'Deleted PvP Team\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__pvpteam` WHERE `deleted` IS NOT NULL)
                            UNION ALL
                            (SELECT \'Active Linkshell\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `crossworld`=0)
                            UNION ALL
                            (SELECT \'Deleted Linkshell\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NOT NULL AND `crossworld`=0)
                            UNION ALL
                            (SELECT \'Active Crossworld Linkshell\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `crossworld`=1)
                            UNION ALL
                            (SELECT \'Deleted Crossworld Linkshell\' AS `value`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `deleted` IS NOT NULL AND `crossworld`=1)
                            ORDER BY `count` DESC;
                    ', return: 'all');
        #Number of updated entities in the last 30 days
        $data['updates_stats'] = Query::query(
        /** @lang MariaDB */ '(SELECT DATE(`updated`) AS `date`, COUNT(*) AS `count`, \'characters\' as `type` FROM `ffxiv__character` GROUP BY `date` ORDER BY `date` DESC LIMIT 30)
                            UNION
                            (SELECT DATE(`updated`) AS `date`, COUNT(*) AS `count`, \'free_companies\' as `type` FROM `ffxiv__freecompany` GROUP BY `date` ORDER BY `date` DESC LIMIT 30)
                            UNION
                            (SELECT DATE(`updated`) AS `date`, COUNT(*) AS `count`, \'pvp_teams\' as `type` FROM `ffxiv__pvpteam` GROUP BY `date` ORDER BY `date` DESC LIMIT 30)
                            UNION
                            (SELECT DATE(`updated`) AS `date`, COUNT(*) AS `count`, \'linkshells\' as `type` FROM `ffxiv__linkshell` GROUP BY `date` ORDER BY `date` DESC LIMIT 30);', return: 'all'
        );
        $data['updates_stats'] = Splitters::splitByKey($data['updates_stats'], 'date');
        foreach ($data['updates_stats'] as $date => $datapoint) {
            $data['updates_stats'][$date] = Converters::multiToSingle(Editors::digitToKey($datapoint, 'type', true), 'count');
        }
        \krsort($data['updates_stats']);
        $data['updates_stats'] = \array_slice($data['updates_stats'], 0, 30);
    }
    
    /**
     * Adds jobs for all entities considered as potential bugs
     *
     * @param array $data Array of gathered data
     *
     * @return void
     * @throws \Exception
     */
    private function scheduleBugs(array $data): void
    {
        #These may be because of temporary issues on the parser or Lodestone side, so schedule them for update
        $cron = new TaskInstance();
        foreach ($data['bugs']['no_clan'] as $character) {
            $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$character['id'], 'character'], 'message' => 'Updating character with ID '.$character['id']])->add();
        }
        foreach ($data['bugs']['no_avatar'] as $character) {
            $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$character['id'], 'character'], 'message' => 'Updating character with ID '.$character['id']])->add();
        }
        foreach ($data['bugs']['no_members'] as $group) {
            $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$group['id'], $group['type']], 'message' => 'Updating group with ID '.$group['id']])->add();
        }
        foreach ($data['bugs']['duplicate_names'] as $servers) {
            foreach ($servers as $server) {
                foreach ($server as $names) {
                    foreach ($names as $duplicate) {
                        $cron->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$duplicate['id'], $duplicate['type']], 'message' => 'Updating entity with ID '.$duplicate['id']])->add();
                    }
                }
            }
        }
    }
}