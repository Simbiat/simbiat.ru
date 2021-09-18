<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

class Search extends \Simbiat\Abstracts\Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = '';
    #Name of the table to search use
    protected string $table = '';
    #List of fields
    protected string $fields = '';
    #Optional WHERE clause
    protected string $where = '';
    #Condition for search
    protected string $whatToSearch = '';
    #Default order (for main page, for example)
    protected string $orderDefault = '';
    #Order for list pages
    protected string $orderList = '';

    /*
    private function fftracker(string $what = ''): array
    {
        $what = preg_replace('/(^[-+@<>()~*\'\s]*)|([-+@<>()~*\'\s]*$)/mi', '', $what);
        if ($what === '') {
            #Count entities
            $result['counts'] = $this->dbController->selectPair('
                        SELECT \'characters\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__character`
                        UNION ALL
                        SELECT \'companies\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__freecompany`
                        UNION ALL
                        SELECT \'linkshells\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__linkshell`
                        UNION ALL
                        SELECT \'pvpteams\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__pvpteam`
                        UNION ALL
                        SELECT \'achievements\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__achievement`
                        ');
            $result['entities'] = (new FFTracker)->GetRandomEntities(25);
        } else {
            #Prepare data for binding. Since we may be using data from user/URI we also try to sanitise it through rawurldecode
            $where_pdo = [':id'=>[(is_int($what) ? $what : strval($what)), (is_int($what) ? 'int' : 'string')], ':name'=>'*'.rawurldecode($what).'*', ':match'=>[$what, 'match']];
            #Count entities
            $result['counts'] = $this->dbController->selectPair('
                        SELECT \'characters\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__character` WHERE `characterid` = :id OR MATCH (`name`, `biography`) AGAINST (:match IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'companies\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id OR MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:match IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'linkshells\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `linkshellid` = :id OR MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'pvpteams\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id OR MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'achievements\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__achievement` WHERE `achievementid` = :name OR MATCH (`name`, `howto`) AGAINST (:match IN BOOLEAN MODE)
            ', $where_pdo);
            #If there are actual entities matching the criteria - show $maxlines amount of them
            if (array_sum($result['counts']) > 0) {
                #Need to use a secondary SELECT, because IN BOOLEAN MODE does not sort by default and we need `relevance` column for that, but we do not want to send to client
                $result['entities'] = $this->dbController->selectAll('
                        SELECT `id`, `type`, `name`, `icon` FROM (
                            SELECT `characterid` AS `id`, \'character\' as `type`, `name`, `avatar` AS `icon`, IF(`characterid` = :id, 99999, MATCH (`name`, `biography`) AGAINST (:match IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__character` WHERE `characterid` = :id OR MATCH (`name`, `biography`) AGAINST (:match IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `freecompanyid` AS `id`, \'freecompany\' as `type`, `name`, `crest` AS `icon`, IF(`freecompanyid` = :id, 99999, MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:match IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id OR MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:match IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `linkshellid` AS `id`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') as `type`, `name`, NULL AS `icon`, IF(`linkshellid` = :id, 99999, MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__linkshell` WHERE `linkshellid` = :id OR MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `pvpteamid` AS `id`, \'pvpteam\' as `type`, `name`, `crest` AS `icon`, IF(`pvpteamid` = :id, 99999, MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id OR MATCH (`name`) AGAINST (:match IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `achievementid` AS `id`, \'achievement\' as `type`, `name`, `icon`, IF(`achievementid` = :id, 99999, MATCH (`name`, `howto`) AGAINST (:match IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__achievement` WHERE `achievementid` = :id OR MATCH (`name`, `howto`) AGAINST (:match IN BOOLEAN MODE)
                            ORDER BY `relevance` DESC, `name` LIMIT 25
                        ) tempdata
                ', $where_pdo);
            }
        }
        return $result;
    }
    */
}
