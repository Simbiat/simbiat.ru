<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Database\Controller;

class Search
{
    private ?Controller $dbController;
    #Items to display per page for lists
    const listItems = 100;
    #Settings for each type
    const forQueries = [
        'openBics' => [
            'table' => 'bic__list',
            'fields' => '\'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`',
            'where' => '`DateOut` IS NULL',
            'what' => 'IF(`VKEY`=:what OR `BIC`=:what OR `OLD_NEWNUM`=:what OR `RegN`=:what, 99999, MATCH (`NameP`, `Adr`) AGAINST (:match IN BOOLEAN MODE))',
            'order' => '`Updated` DESC',
            'orderList' => '`NameP`',
        ],
        'closedBics' => [
            'table' => 'bic__list',
            'fields' => '\'bic\' as `type`, `BIC` as `id`, `NameP` as `name`, `DateOut`',
            'where' => '`DateOut` IS NOT NULL',
            'what' => 'IF(`VKEY`=:what OR `BIC`=:what OR `OLD_NEWNUM`=:what OR `RegN`=:what, 99999, MATCH (`NameP`, `Adr`) AGAINST (:match IN BOOLEAN MODE))',
            'order' => '`Updated` DESC',
            'orderList' => '`NameP`',
        ],
    ];

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
            $counts['openBics'] = $this->count('openBics', $what);
            $counts['closedBics'] = $this->count('closedBics', $what);
            $results['openBics'] = $this->select('openBics', $what, 15);
            $results['closedBics'] = $this->select('closedBics', $what,15);
        }
        if ($type === 'fftracker') {
            #$counts = array_merge($results, $this->fftracker($what));
            #$results = array_merge($results, $this->fftracker($what));
        }
        return ['counts'=>$counts, 'results'=>$results];
    }

    #Function to generate list of entities or get a proper page number for redirect
    /**
     * @throws \Exception
     */
    public function listEntities(string $type, int $page = 1, string $what = ''): int|array
    {
        #Suggest redirect if page number is less than 1
        if ($page < 1) {
            return 1;
        }
        #Count entities first
        $count = $this->count($type, $what);
        #Count pages
        $pages = intval(ceil($count/self::listItems));
        #Suggest redirect if page is larger than the number of pages
        if ($page > $pages) {
            return $pages;
        }
        return ['count'=>$count, 'pages'=> $pages,'entities'=>$this->select($type, $what, self::listItems, self::listItems*($page-1), true)];
    }

    #Generalized function to count entities
    /**
     * @throws \Exception
     */
    private function count(string $type, string $what = ''): int
    {
        if (!isset(self::forQueries[$type])) {
            return 0;
        }
        if ($what !== '') {
            #Set binding
            $binding = [':what' => $what, ':match' => [$what, 'match']];
            return $this->dbController->count('SELECT COUNT(*) FROM `'.self::forQueries[$type]['table'].'` WHERE '.self::forQueries[$type]['where'].' AND '.self::forQueries[$type]['what'].' > 0', $binding);
        } else {
            return $this->dbController->count('SELECT COUNT(*) FROM `'.self::forQueries[$type]['table'].'` WHERE '.self::forQueries[$type]['where']);
        }
    }

    #Generalized function to select entities
    /**
     * @throws \Exception
     */
    private function select(string $type, string $what = '', int $limit = 100, int $offset = 0, bool $list = false): array
    {
        if (!isset(self::forQueries[$type])) {
            return [];
        }
        if ($what !== '') {
            #Set binding
            $binding = [':what' => $what, ':match' => [$what, 'match']];
            return $this->dbController->selectAll('SELECT '.self::forQueries[$type]['fields'].', '.self::forQueries[$type]['what'].' as `relevance` FROM `'.self::forQueries[$type]['table'].'` WHERE '.self::forQueries[$type]['where'].' HAVING `relevance`>0 ORDER BY `relevance` DESC, `name` LIMIT '.$limit.' OFFSET '.$offset, $binding);
        } else {
            return $this->dbController->selectAll('SELECT '.self::forQueries[$type]['fields'].' FROM `'.self::forQueries[$type]['table'].'` WHERE '.self::forQueries[$type]['where'].' ORDER BY '.($list ? self::forQueries[$type]['orderList'] : self::forQueries[$type]['order']).' LIMIT '.$limit.' OFFSET '.$offset);
        }
    }

    /**
     * @throws \Exception
     */
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
}
