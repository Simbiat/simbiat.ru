<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Database\Controller;

class HomeSearch
{
    private ?Controller $dbController;
    const bicPrefix = 'bic__';
    const ffPrefix = 'ff__';
    const maxEntities = 25;

    public function __construct()
    {
        #Cache DB controller
        $this->dbController = (new Controller);
    }

    /**
     * @throws \Exception
     */
    public function search(string $type = 'all', string $what = ''): array
    {
        $counts = [];
        $results = [];
        if ($type === 'bictracker') {
            $counts = array_merge($results, $this->bicTracker(true ,$what, 15));
            $results = array_merge($results, $this->bicTracker(false ,$what, 15));
        }
        if ($type === 'fftracker') {
            $counts = array_merge($results, $this->fftracker($what));
            $results = array_merge($results, $this->fftracker($what));
        }
        return ['counts'=>$counts, 'results'=>$results];
    }

#$statistics['bicchanges'] = $dbCon->selectAll('SELECT \'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut` FROM `bic__list` ORDER BY `Updated` DESC LIMIT '.$lastChanges);

    /**
     * @throws \Exception
     */
    public function bicTracker(bool $count = true, string $what = '', int $limit = 25): array
    {
        #Set binding
        if ($what !== '') {
            $binding = [':name' => $what, ':match' => [$what, 'match']];
            $condition = 'IF(`VKEY`=:name OR `BIC`=:name OR `OLD_NEWNUM`=:name OR `RegN`=:name, 99999, MATCH (`NameP`, `Adr`) AGAINST (:name IN BOOLEAN MODE))';
        }
        #Build query
        if ($count) {
            if ($what === '') {
                $result['openBics'] = $this->dbController->count('SELECT COUNT(*) FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NULL');
                $result['closeBics'] = $this->dbController->count('SELECT COUNT(*) FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NOT NULL');
            } else {
                $result['openBics'] = $this->dbController->count('SELECT COUNT(*) FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NULL AND '.$condition.' > 0', $binding);
                $result['closeBics'] = $this->dbController->count('SELECT COUNT(*) FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NOT NULL AND '.$condition.' > 0', $binding);
            }
        } else {
            $fields = '\'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`';
            if ($what === '') {
                $result['openBics'] = $this->dbController->selectAll('SELECT '.$fields.' FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NULL ORDER BY `Updated` DESC LIMIT '.$limit);
                $result['closeBics'] = $this->dbController->selectAll('SELECT '.$fields.' FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NOT NULL ORDER BY `Updated` DESC LIMIT '.$limit);
            } else {
                $result['openBics'] = $this->dbController->selectAll('SELECT '.$fields.', '.$condition.' as `relevance` FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NULL HAVING `relevance`>0 ORDER BY `relevance` DESC, `name` LIMIT '.$limit, $binding);
                $result['closeBics'] = $this->dbController->selectAll('SELECT '.$fields.', '.$condition.' as `relevance` FROM `' . self::bicPrefix . 'list` WHERE `DateOut` IS NOT NULL HAVING `relevance`>0 ORDER BY `relevance` DESC, `name` LIMIT '.$limit, $binding);
            }
        }
        return $result;
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
