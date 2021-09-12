<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Database\Controller;

class HomeSearch
{
    private ?Controller $dbController;

    public function __construct()
    {
        #Cache DB controller
        $this->dbController = (new Controller);
    }

    /**
     * @throws \Exception
     */
    public function search(string $type, string $what = ''): array
    {
        $results = [];
        if ($type === 'bictracker') {
            $results = $this->bicTracker($what);
        }
        if ($type === 'fftracker') {
            $results = $this->fftracker($what);
        }
        return $results;
    }


    /**
     * @throws \Exception
     */
    public function bicTracker(string $what = ''): array
    {
        return $this->dbController->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` WHERE `VKEY`=:name OR `BIC`=:name OR `OLD_NEWNUM`=:name OR `RegN`=:name OR MATCH (`NameP`, `Adr`) AGAINST (:match IN BOOLEAN MODE) ORDER BY `NameP`', [':name'=>$what, ':match'=>[$what, 'match']]);
    }

    /**
     * @throws \Exception
     */
    public function fftracker(string $what = ''): array
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
}
