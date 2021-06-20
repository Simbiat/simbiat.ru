<?php
declare(strict_types=1);
namespace Simbiat\FFTModules;

use Simbiat\ArrayHelpers;
use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\LodestoneModules\Converters;

trait Output
{
    #Generalized function to get entity data
    /**
     * @throws \Exception
     */
    public function TrackerGrab(string $type, string $id): array
    {
        return match($type) {
            'character' => $this->GetCharacter($id),
            'achievement' => $this->GetAchievement($id),
            'freecompany' => $this->GetCompany($id),
            'pvpteam' => $this->GetPVP($id),
            'linkshell', 'crossworld_linkshell', 'crossworldlinkshell' => $this->GetLinkshell($id),
            default => [],
        };
    }

    /**
     * @throws \Exception
     */
    private function GetCharacter(string $id): array
    {
        $dbController = (new Controller);
        #Get general information. Using *, but add name, because otherwise Achievement name overrides Character name and we do not want that
        $data = $dbController->selectRow('SELECT *, `ffxiv__character`.`name`, `ffxiv__character`.`updated` FROM `ffxiv__character` LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid` = `ffxiv__clan`.`clanid` LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid` = `ffxiv__guardian`.`guardianid` LEFT JOIN `ffxiv__nameday` ON `ffxiv__character`.`namedayid` = `ffxiv__nameday`.`namedayid` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid` = `ffxiv__city`.`cityid` LEFT JOIN `ffxiv__server` ON `ffxiv__character`.`serverid` = `ffxiv__server`.`serverid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid` = `ffxiv__grandcompany_rank`.`gcrankid` LEFT JOIN `ffxiv__achievement` ON `ffxiv__character`.`titleid` = `ffxiv__achievement`.`achievementid` WHERE `ffxiv__character`.`characterid` = :id;', [':id'=>$id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names. For now this is commented out due to cases of bullying, when the old names are learnt. They are still being collected, though for statistical purposes.
        #$data['oldnames'] = $dbController->selectColumn('SELECT `name` FROM `ffxiv__character_names` WHERE `characterid`=:id AND `name`!=:name', [':id'=>$id, ':name'=>$data['name']]);
        #Get levels
        $data['jobs'] = $dbController->selectPair('SELECT `ffxiv__job`.`name` AS `job`, `level` FROM `ffxiv__character_jobs` INNER JOIN `ffxiv__job` ON `ffxiv__job`.`jobid`=`ffxiv__character_jobs`.`jobid` WHERE `characterid`=:id;', [':id'=>$id]);
        #Get previous known incarnations (combination of gender and race/clan)
        $data['incarnations'] = $dbController->selectAll('SELECT `genderid`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan` FROM `ffxiv__character_clans` LEFT JOIN `ffxiv__clan` ON `ffxiv__character_clans`.`clanid` = `ffxiv__clan`.`clanid` WHERE `ffxiv__character_clans`.`characterid`=:id AND (`ffxiv__character_clans`.`clanid`!=:clanid AND `ffxiv__character_clans`.`genderid`!=:genderid) ORDER BY `genderid` , `race` , `clan` ', [':id'=>$id, ':clanid'=>$data['clanid'], ':genderid'=>$data['genderid']]);
        #Get old servers
        $data['servers'] = $dbController->selectAll('SELECT `ffxiv__server`.`datacenter`, `ffxiv__server`.`server` FROM `ffxiv__character_servers` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`serverid`=`ffxiv__character_servers`.`serverid` WHERE `ffxiv__character_servers`.`characterid`=:id AND `ffxiv__character_servers`.`serverid` != :serverid ORDER BY `datacenter` , `server` ', [':id'=>$id, ':serverid'=>$data['serverid']]);
        #Get achievements
        $data['achievements'] = $dbController->selectAll('SELECT `ffxiv__achievement`.`achievementid`, `ffxiv__achievement`.`category`, `ffxiv__achievement`.`subcategory`, `ffxiv__achievement`.`name`, `time`, `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__achievement` ON `ffxiv__character_achievement`.`achievementid`=`ffxiv__achievement`.`achievementid` WHERE `ffxiv__character_achievement`.`characterid` = :id AND `ffxiv__achievement`.`category` IS NOT NULL AND `ffxiv__achievement`.`achievementid` IS NOT NULL ORDER BY `time` DESC, `name` ', [':id'=>$id]);
        #Get affiliated groups' details
        $data['groups'] = $dbController->selectAll(
            '(SELECT \'freecompany\' AS `type`, `ffxiv__character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, 1 AS `current`, `ffxiv__character`.`company_joined` AS `join`, `ffxiv__character`.`company_rank` AS `rankid`, `ffxiv__freecompany_rank`.`rankname`, `ffxiv__freecompany`.`crest` AS `icon` FROM `ffxiv__character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__character`.`freecompanyid`=`ffxiv__freecompany_rank`.`freecompanyid` AND `ffxiv__character`.`company_rank`=`ffxiv__freecompany_rank`.`rankid` WHERE `characterid`=:id AND `ffxiv__character`.`freecompanyid` IS NOT NULL)
            UNION ALL
            (SELECT \'freecompany\' AS `type`, `ffxiv__freecompany_x_character`.`freecompanyid` AS `id`, `ffxiv__freecompany`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `ffxiv__freecompany`.`crest` AS `icon` FROM `ffxiv__freecompany_x_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_x_character`.`freecompanyid`=`ffxiv__freecompany`.`freecompanyid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `ffxiv__linkshell_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, 1 AS `current`, NULL AS `join`, `ffxiv__linkshell_character`.`rankid`, `ffxiv__linkshell_rank`.`rank` AS `rankname`, NULL AS `icon` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rankid`=`ffxiv__linkshell_rank`.`lsrankid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `ffxiv__linkshell_x_character`.`linkshellid` AS `id`, `ffxiv__linkshell`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, NULL AS `icon` FROM `ffxiv__linkshell_x_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_x_character`.`linkshellid`=`ffxiv__linkshell`.`linkshellid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `ffxiv__character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, 1 AS `current`, NULL AS `join`, `ffxiv__character`.`pvp_rank` AS `rankid`, `ffxiv__pvpteam_rank`.`rank` AS `rankname`, `ffxiv__pvpteam`.`crest` AS `icon` FROM `ffxiv__character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__character`.`pvp_rank`=`ffxiv__pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `ffxiv__character`.`pvpteamid` IS NOT NULL)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `ffxiv__pvpteam_x_character`.`pvpteamid` AS `id`, `ffxiv__pvpteam`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `ffxiv__pvpteam`.`crest` AS `icon` FROM `ffxiv__pvpteam_x_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_x_character`.`pvpteamid`=`ffxiv__pvpteam`.`pvpteamid` WHERE `characterid`=:id)
            ORDER BY `current` DESC, `name` ASC;',
            [':id'=>$id]
        );
        #Clean up the data from unnecessary (technical) clutter
        unset($data['clanid'], $data['namedayid'], $data['achievementid'], $data['category'], $data['subcategory'], $data['howto'], $data['points'], $data['icon'], $data['item'], $data['itemicon'], $data['itemid'], $data['serverid']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$id, 'character'], priority: 1, message: 'Updating character with ID '.$id);
        }
        unset($dbController);
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function GetCompany(string $id): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__server` ON `ffxiv__freecompany`.`serverid`=`ffxiv__server`.`serverid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany_rank`.`gcrankid` LEFT JOIN `ffxiv__timeactive` ON `ffxiv__freecompany`.`activeid`=`ffxiv__timeactive`.`activeid` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estateid`=`ffxiv__estate`.`estateid` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`cityid`=`ffxiv__city`.`cityid` WHERE `freecompanyid`=:id', [':id'=>$id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }

        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `ffxiv__freecompany_names` WHERE `freecompanyid`=:id AND `name`!=:name', [':id'=>$id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `ffxiv__character`.`characterid`, `company_joined` AS `join`, `ffxiv__freecompany_rank`.`rankid`, `rankname` AS `rank`, `name`, `avatar` FROM `ffxiv__character` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`rankid`=`ffxiv__character`.`company_rank` AND `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__character`.`freecompanyid` JOIN (SELECT `company_rank`, COUNT(*) AS `total` FROM `ffxiv__character` WHERE `ffxiv__character`.`freecompanyid`=:id GROUP BY `company_rank`) `ranklist` ON `ranklist`.`company_rank` = `ffxiv__character`.`company_rank` WHERE `ffxiv__character`.`freecompanyid`=:id ORDER BY `ranklist`.`total` , `ranklist`.`company_rank` , `ffxiv__character`.`name` ', [':id'=>$id]);
        #History of ranks. Ensuring that we get only the freshest 100 entries sorted from latest to newest
        $data['ranks_history'] = $dbcon->selectAll('SELECT * FROM (SELECT `date`, `weekly`, `monthly`, `members` FROM `ffxiv__freecompany_ranking` WHERE `freecompanyid`=:id ORDER BY `date` DESC LIMIT 100) `lastranks` ORDER BY `date` ', [':id'=>$id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['grandcompanyid'], $data['estateid'], $data['gcrankid'], $data['gc_rank'], $data['gc_icon'], $data['activeid'], $data['cityid'], $data['left'], $data['top'], $data['cityicon']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$id, 'freecompany'], priority: 1, message: 'Updating free company with ID '.$id);
        }
        unset($dbcon);
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function GetLinkshell(string $id): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `ffxiv__linkshell` LEFT JOIN `ffxiv__server` ON `ffxiv__linkshell`.`serverid`=`ffxiv__server`.`serverid` WHERE `linkshellid`=:id', [':id'=>$id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `ffxiv__linkshell_names` WHERE `linkshellid`=:id AND `name`<>:name', [':id'=>$id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `ffxiv__linkshell_character`.`characterid`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar`, `ffxiv__linkshell_rank`.`rank`, `ffxiv__linkshell_rank`.`lsrankid` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_rank`.`lsrankid`=`ffxiv__linkshell_character`.`rankid` LEFT JOIN `ffxiv__character` ON `ffxiv__linkshell_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__linkshell_character`.`linkshellid`=:id ORDER BY `ffxiv__linkshell_character`.`rankid` , `ffxiv__character`.`name` ', [':id'=>$id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['serverid']);
        if ($data['crossworld']) {
            unset($data['server']);
        }
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            if ($data['crossworld'] == '0') {
                (new Cron)->add('ffentityupdate', [$id, 'linkshell'], priority: 1, message: 'Updating linkshell with ID '.$id);
            } else {
                (new Cron)->add('ffentityupdate', [$id, 'crossworldlinkshell'], priority: 1, message: 'Updating crossworldlinkshell with ID '.$id);
            }
        }
        unset($dbcon);
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function GetPVP(string $id): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `ffxiv__pvpteam` LEFT JOIN `ffxiv__server` ON `ffxiv__pvpteam`.`datacenterid`=`ffxiv__server`.`serverid` WHERE `pvpteamid`=:id', [':id'=>$id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `ffxiv__pvpteam_names` WHERE `pvpteamid`=:id AND `name`<>:name', [':id'=>$id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `ffxiv__character`.`characterid`, `ffxiv__character`.`pvp_matches` AS `matches`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar`, `ffxiv__pvpteam_rank`.`rank`, `ffxiv__pvpteam_rank`.`pvprankid` FROM `ffxiv__character` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_rank`.`pvprankid`=`ffxiv__character`.`pvp_rank` WHERE `ffxiv__character`.`pvpteamid`=:id ORDER BY `ffxiv__character`.`pvp_rank` , `ffxiv__character`.`name` ', [':id'=>$id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['datacenterid'], $data['serverid'], $data['server']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$id, 'pvpteam'], priority: 1, message: 'Updating PvP team with ID '.$id);
        }
        unset($dbcon);
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function GetAchievement(string $id): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT *, (SELECT COUNT(*) FROM `ffxiv__character_achievement` WHERE `ffxiv__character_achievement`.`achievementid` = `ffxiv__achievement`.`achievementid`) as `count` FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`achievementid` = :id', [':id'=>$id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = $dbcon->selectAll('SELECT * FROM (SELECT \'character\' AS `type`, `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar` AS `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`characterid` = `ffxiv__character_achievement`.`characterid` WHERE `ffxiv__character_achievement`.`achievementid` = :id ORDER BY `ffxiv__character_achievement`.`time` DESC LIMIT '.$this->maxlines.') t ORDER BY `name`', [':id'=>$id]);
        #Register for an update if old enough or category or howto or dbid are empty. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if ((empty($data['category']) || empty($data['subcategory']) || empty($data['howto']) || empty($data['dbid']) || (time() - strtotime($data['updated'])) >= 31536000) && !empty($data['characters']) && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$id, 'achievement', array_column($data['characters'], 'id')[0]], priority: 2, message: 'Updating achievement with ID '.$id);
        }
        unset($dbcon);
        return $data;
    }

    #Function to search for entities

    /**
     * @throws \Exception
     */
    public function Search(string $what = ''): array
    {
        $dbcon = (new Controller);
        $what = preg_replace('/(^[-+@<>()~*\'\s]*)|([-+@<>()~*\'\s]*$)/mi', '', $what);
        if ($what === '') {
            #Count entities
            $result['counts'] = $dbcon->selectPair('
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
            $result['entities'] = $this->GetRandomEntities($this->maxlines);
        } else {
            #Prepare data for binding. Since we may be using data from user/URI we also try to sanitise it through rawurldecode
            $where_pdo = array(':id'=>[(is_int($what) ? $what : strval($what)), (is_int($what) ? 'int' : 'string')], ':name'=>'*'.rawurldecode($what).'*');
            #Count entities
            $result['counts'] = $dbcon->selectPair('
                        SELECT \'characters\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__character` WHERE `characterid` = :id OR MATCH (`name`, `biography`) AGAINST (:name IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'companies\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id OR MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:name IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'linkshells\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__linkshell` WHERE `linkshellid` = :id OR MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'pvpteams\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id OR MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)
                        UNION ALL
                        SELECT \'achievements\' AS `type`, COUNT(*) AS `count` FROM `ffxiv__achievement` WHERE `achievementid` = :name OR MATCH (`name`, `howto`) AGAINST (:name IN BOOLEAN MODE)
            ', $where_pdo);
            #If there are actual entities matching the criteria - show $maxlines amount of them
            if (array_sum($result['counts']) > 0) {
                #Need to use a secondary SELECT, because IN BOOLEAN MODE does not sort by default and we need `relevance` column for that, but we do not want to send to client
                $result['entities'] = $dbcon->selectAll('
                        SELECT `id`, `type`, `name`, `icon` FROM (
                            SELECT `characterid` AS `id`, \'character\' as `type`, `name`, `avatar` AS `icon`, IF(`characterid` = :id, 99999, MATCH (`name`, `biography`) AGAINST (:name IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__character` WHERE `characterid` = :id OR MATCH (`name`, `biography`) AGAINST (:name IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `freecompanyid` AS `id`, \'freecompany\' as `type`, `name`, `crest` AS `icon`, IF(`freecompanyid` = :id, 99999, MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:name IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__freecompany` WHERE `freecompanyid` = :id OR MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:name IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `linkshellid` AS `id`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') as `type`, `name`, NULL AS `icon`, IF(`linkshellid` = :id, 99999, MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__linkshell` WHERE `linkshellid` = :id OR MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `pvpteamid` AS `id`, \'pvpteam\' as `type`, `name`, `crest` AS `icon`, IF(`pvpteamid` = :id, 99999, MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__pvpteam` WHERE `pvpteamid` = :id OR MATCH (`name`) AGAINST (:name IN BOOLEAN MODE)
                            UNION ALL
                            SELECT `achievementid` AS `id`, \'achievement\' as `type`, `name`, `icon`, IF(`achievementid` = :id, 99999, MATCH (`name`, `howto`) AGAINST (:name IN BOOLEAN MODE)) AS `relevance` FROM `ffxiv__achievement` WHERE `achievementid` = :id OR MATCH (`name`, `howto`) AGAINST (:name IN BOOLEAN MODE)
                            ORDER BY `relevance` DESC, `name` LIMIT ' .$this->maxlines.'
                        ) tempdata
                ', $where_pdo);
            }
        }
        unset($dbcon);
        return $result;
    }

    #Function to get a list of entities

    /**
     * @throws \Exception
     */
    public function listEntities(string $type, int $offset = 0, int $limit = 100): array
    {
        #Sanitize type
        if (!in_array($type, ['freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'characters', 'achievements', 'pvpteams'])) {
            return [];
        } else {
            #Update type
            $type = match($type) {
                'freecompanies' => 'freecompany',
                'linkshells', 'crossworldlinkshells', 'crossworld_linkshells' => 'linkshell',
                'characters' => 'character',
                'achievements' => 'achievement',
                'pvpteams' => 'pvpteam',
            };
        }
        #Set avatar value
        $avatar = match($type) {
            'character' => '`avatar`',
            'achievement' => '`icon`',
            'freecompany', 'pvpteam' => '`crest`',
            default => 'NULL',
        };
        #Sanitize numbers
        if ($offset < 0) {
            $offset = 0;
        }
        if ($limit < 1) {
            $limit = 1;
        }
        $dbcon = (new Controller);
        #Forcing index, because for some reason MySQL is using filesort for this query
        $result['entities'] = $dbcon->selectAll('SELECT `'.$type.'id` AS `id`, '.($type === 'linkshell' ? 'IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\')' : '\''.$type.'\'').' as `type`, `name`, '.$avatar.' AS `icon`, `updated` FROM `ffxiv__'.$type.'` FORCE INDEX(`name_order`) ORDER BY `name` ASC LIMIT '.$offset.', '.$limit);
        $result['statistics'] = $dbcon->selectRow('SELECT COUNT(`'.$type.'id`) AS `count`, MAX(`updated`) AS `updated` FROM `ffxiv__'.$type.'`');
        return $result;
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function Statistics(string $type = 'genetics', string $cachepath = '', bool $nocache = false): array
    {
        $data = [];
        #Sanitize type
        $type = strtolower($type);
        if (!in_array($type, ['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'])) {
            $type = 'genetics';
        }
        #Sanitize cachepath
        if (empty($cachepath)) {
            #Create path if missing
            if (!is_dir(dirname(__DIR__).'/statistics/')) {
                mkdir(dirname(__DIR__).'/statistics/');
            }
            $cachepath = dirname(__DIR__).'/statistics/'.$type.'.json';
        }
        #Check if cache file exists
        if (is_file($cachepath)) {
            #Read the cache
            $json = file_get_contents($cachepath);
            if ($json !== false && $json !== '') {
                $json = json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
                if ($json !== NULL) {
                    if (!is_array($json)) {
                        $json = [];
                    }
                } else {
                    $json = [];
                }
            } else {
                $json = [];
            }
        } else {
            $json = [];
        }
        #Get Lodestone object for optimization
        $Lodestone = (new Converters);
        #Get ArrayHelpers object for optimization
        $ArrayHelpers = (new ArrayHelpers);
        #Get connection object for slight optimization
        $dbcon = (new Controller);
        switch ($type) {
            case 'genetics':
                #Get statistics by clan
                if (!$nocache && !empty($json['characters']['clans'])) {
                    $data['characters']['clans'] = $json['characters']['clans'];
                } else {
                    $data['characters']['clans'] = $ArrayHelpers->splitByKey($dbcon->countUnique('ffxiv__character', 'clanid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__clan', 'INNER', 'clanid', '`ffxiv__character`.`genderid`, CONCAT(`ffxiv__clan`.`race`, \' of \', `ffxiv__clan`.`clan`, \' clan\')', 'DESC', 0, ['`ffxiv__character`.`genderid`']), 'genderid', ['female', 'male'], [0, 1]);
                }
                #Clan distribution by city
                if (!$nocache && !empty($json['cities']['clans'])) {
                    $data['cities']['clans'] = $json['cities']['clans'];
                } else {
                    $data['cities']['clans'] = $ArrayHelpers->splitByKey($dbcon->SelectAll('SELECT `ffxiv__city`.`city`, CONCAT(`ffxiv__clan`.`race`, \' of \', `ffxiv__clan`.`clan`, \' clan\') AS `value`, COUNT(`ffxiv__character`.`characterid`) AS `count` FROM `ffxiv__character` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid`=`ffxiv__clan`.`clanid` GROUP BY `city`, `value` ORDER BY `count` DESC'), 'city', [$Lodestone->getCityName(2, $this->language), $Lodestone->getCityName(4, $this->language), $Lodestone->getCityName(5, $this->language)], []);
                }
                #Clan distribution by grand company
                if (!$nocache && !empty($json['grand_companies']['clans'])) {
                    $data['grand_companies']['clans'] = $json['grand_companies']['clans'];
                } else {
                    $data['grand_companies']['clans'] = $ArrayHelpers->splitByKey($dbcon->SelectAll('SELECT `ffxiv__grandcompany_rank`.`gc_name`, CONCAT(`ffxiv__clan`.`race`, \' of \', `ffxiv__clan`.`clan`, \' clan\') AS `value`, COUNT(`ffxiv__character`.`characterid`) AS `count` FROM `ffxiv__character` LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clanid`=`ffxiv__clan`.`clanid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid` WHERE `ffxiv__character`.`deleted` IS NULL AND `ffxiv__grandcompany_rank`.`gc_name` IS NOT NULL GROUP BY `gc_name`, `value` ORDER BY `count` DESC'), 'gc_name', [], []);
                }
                break;
            case 'astrology':
                #Get statitics by guardian
                if (!$nocache && !empty($json['characters']['guardians'])) {
                    $data['characters']['guardians'] = $json['characters']['guardians'];
                } else {
                    $data['characters']['guardians'] = $dbcon->countUnique('ffxiv__character', 'guardianid', '`ffxiv__character`.`deleted` IS NULL','ffxiv__guardian', 'INNER', 'guardianid', '`ffxiv__character`.`genderid`, `ffxiv__guardian`.`guardian`', 'DESC', 0, ['`ffxiv__character`.`genderid`']);
                    #Add colors to guardians
                    foreach ($data['characters']['guardians'] as $key=>$guardian) {
                        $data['characters']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    #Split guardians by gender
                    $data['characters']['guardians'] = $ArrayHelpers->splitByKey($data['characters']['guardians'], 'genderid', ['female', 'male'], [0, 1]);
                }
                #Guardian distribution by city
                if (!$nocache && !empty($json['cities']['guardians'])) {
                    $data['cities']['guardians'] = $json['cities']['guardians'];
                } else {
                    $data['cities']['guardians'] = $dbcon->SelectAll('SELECT `ffxiv__city`.`city`, `ffxiv__guardian`.`guardian` AS `value`, COUNT(`ffxiv__character`.`characterid`) AS `count` FROM `ffxiv__character` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid`=`ffxiv__guardian`.`guardianid` GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to guardians
                    foreach ($data['cities']['guardians'] as $key=>$guardian) {
                        $data['cities']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    $data['cities']['guardians'] = $ArrayHelpers->splitByKey($data['cities']['guardians'], 'city', [], []);
                }
                #Guardians distribution by grand company
                if (!$nocache && !empty($json['grand_companies']['guardians'])) {
                    $data['grand_companies']['guardians'] = $json['grand_companies']['guardians'];
                } else {
                    $data['grand_companies']['guardians'] = $dbcon->SelectAll('SELECT `ffxiv__grandcompany_rank`.`gc_name`, `ffxiv__guardian`.`guardian` AS `value`, COUNT(`ffxiv__character`.`characterid`) AS `count` FROM `ffxiv__character` LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardianid`=`ffxiv__guardian`.`guardianid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid` WHERE `ffxiv__character`.`deleted` IS NULL AND `ffxiv__grandcompany_rank`.`gc_name` IS NOT NULL GROUP BY `gc_name`, `value` ORDER BY `count` DESC');
                    #Add colors to guardians
                    foreach ($data['grand_companies']['guardians'] as $key=>$guardian) {
                        $data['grand_companies']['guardians'][$key]['color'] = $Lodestone->colorGuardians($guardian['value']);
                    }
                    $data['grand_companies']['guardians'] = $ArrayHelpers->splitByKey($data['grand_companies']['guardians'], 'gc_name', [], []);
                }
                break;
            case 'characters':
                #Jobs popularity
                if (!$nocache && !empty($json['characters']['jobs'])) {
                    $data['characters']['jobs'] = $json['characters']['jobs'];
                } else {
                    $data['characters']['jobs'] = $dbcon->selectPair('SELECT `ffxiv__job`.`name` AS `job`, `sum`.`level` FROM (SELECT `jobid`, SUM(`level`) AS `level` FROM `ffxiv__character_jobs` GROUP BY `jobid`) AS `sum` INNER JOIN `ffxiv__job` ON `sum`.`jobid`=`ffxiv__job`.`jobid` ORDER BY `sum`.`level` DESC;');
                }
                #Most name changes
                if (!$nocache && !empty($json['characters']['changes']['name'])) {
                    $data['characters']['changes']['name'] = $json['characters']['changes']['name'];
                } else {
                    $data['characters']['changes']['name'] = $this->valueToName($dbcon->countUnique('ffxiv__character_names', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most reincarnation
                if (!$nocache && !empty($json['characters']['changes']['clan'])) {
                    $data['characters']['changes']['clan'] = $json['characters']['changes']['clan'];
                } else {
                    $data['characters']['changes']['clan'] = $this->valueToName($dbcon->countUnique('ffxiv__character_clans', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most servers
                if (!$nocache && !empty($json['characters']['changes']['server'])) {
                    $data['characters']['changes']['server'] = $json['characters']['changes']['server'];
                } else {
                    $data['characters']['changes']['server'] = $this->valueToName($dbcon->countUnique('ffxiv__character_servers', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most companies
                if (!$nocache && !empty($json['characters']['xgroups']['Free Companies'])) {
                    $data['characters']['xgroups']['Free Companies'] = $json['characters']['xgroups']['Free Companies'];
                } else {
                    $data['characters']['xgroups']['Free Companies'] = $this->valueToName($dbcon->countUnique('ffxiv__freecompany_x_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most PvP teams
                if (!$nocache && !empty($json['characters']['xgroups']['PvP Teams'])) {
                    $data['characters']['xgroups']['PvP Teams'] = $json['characters']['xgroups']['PvP Teams'];
                } else {
                    $data['characters']['xgroups']['PvP Teams'] = $this->valueToName($dbcon->countUnique('ffxiv__pvpteam_x_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most x-linkshells
                if (!$nocache && !empty($json['characters']['xgroups']['Linkshells'])) {
                    $data['characters']['xgroups']['Linkshells'] = $json['characters']['xgroups']['Linkshells'];
                } else {
                    $data['characters']['xgroups']['Linkshells'] = $this->valueToName($dbcon->countUnique('ffxiv__linkshell_x_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Most linkshells
                if (!$nocache && !empty($json['characters']['groups']['linkshell'])) {
                    $data['characters']['groups']['linkshell'] = $json['characters']['groups']['linkshell'];
                } else {
                    $data['characters']['groups']['linkshell'] = $this->valueToName($dbcon->countUnique('ffxiv__linkshell_character', 'characterid', '', 'ffxiv__character', 'INNER', 'characterid', '`tempresult`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`', 'DESC', 20, [], true));
                }
                #Groups affiliation
                if (!$nocache && !empty($json['characters']['groups']['participation'])) {
                    $data['characters']['groups']['participation'] = $json['characters']['groups']['participation'];
                } else {
                    $data['characters']['groups']['participation'] = $dbcon->SelectAll('
                        SELECT `affiliation` AS `value`, COUNT(`affiliation`) AS `count`FROM (
                            SELECT `ffxiv__character`.`characterid`,
                                (CASE
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NOT NULL AND `ffxiv__character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company only\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NULL AND `ffxiv__character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'PvP Team only\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NULL AND `ffxiv__character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Linkshell only\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NOT NULL AND `ffxiv__character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NULL) THEN \'Free Company and PvP Team\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NOT NULL AND `ffxiv__character`.`pvpteamid` IS NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company and Linkshell\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NULL AND `ffxiv__character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'PvP Team and Linkshell\'
                                    WHEN (`ffxiv__character`.`freecompanyid` IS NOT NULL AND `ffxiv__character`.`pvpteamid` IS NOT NULL AND `ffxiv__linkshell_character`.`linkshellid` IS NOT NULL) THEN \'Free Company, PvP Team and Linkshell\'
                                    ELSE \'No groups\'
                                END) AS `affiliation`
                            FROM `ffxiv__character` LEFT JOIN `ffxiv__linkshell_character` ON `ffxiv__linkshell_character`.`characterid` = `ffxiv__character`.`characterid` WHERE `ffxiv__character`.`deleted` IS NULL GROUP BY `ffxiv__character`.`characterid`) `tempresult`
                        GROUP BY `affiliation` ORDER BY `count` DESC;
                    ');
                    #Move count of loners to separate key
                    foreach ($data['characters']['groups']['participation'] as $key=>$row) {
                        if ($row['value'] === 'No groups') {
                            $data['characters']['no_groups'] = $row['count'];
                            unset($data['characters']['groups']['participation'][$key]);
                            break;
                        }
                    }
                }
                #Get characters with most PvP matches. Using regular SQL since we do not count uniqie values, but rather use the regular column values
                if (!$nocache && !empty($json['characters']['most_pvp'])) {
                    $data['characters']['most_pvp'] = $json['characters']['most_pvp'];
                } else {
                    $data['characters']['most_pvp'] = $dbcon->SelectAll('SELECT `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`avatar` AS `icon`, \'character\' AS `type`, `ffxiv__character`.`name`, `pvp_matches` AS `count` FROM `ffxiv__character` ORDER BY `ffxiv__character`.`pvp_matches` DESC LIMIT 20');
                }
                break;
            case 'freecompanies':
                #Get most popular estate locations
                if (!$nocache && !empty($json['freecompany']['estate'])) {
                    $data['freecompany']['estate'] = $json['freecompany']['estate'];
                } else {
                    $data['freecompany']['estate'] = $ArrayHelpers->topAndBottom($dbcon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`, `ffxiv__estate`.`plot`, CONCAT(`ffxiv__estate`.`area`, \', plot \', `ffxiv__estate`.`plot`)'), 20);
                }
                #Get statistics by activity time
                if (!$nocache && !empty($json['freecompany']['active'])) {
                    $data['freecompany']['active'] = $json['freecompany']['active'];
                } else {
                    $data['freecompany']['active'] = $dbcon->sumUnique('ffxiv__freecompany', 'activeid', [1, 2, 3], ['Always', 'Weekdays', 'Weekends'], '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__timeactive', 'INNER', 'activeid', 'IF(`ffxiv__freecompany`.`recruitment`=1, \'Recruting\', \'Not recruting\') AS `recruiting`');
                }
                #Get statistics by activities
                if (!$nocache && !empty($json['freecompany']['activities'])) {
                    $data['freecompany']['activities'] = $json['freecompany']['activities'];
                } else {
                    $data['freecompany']['activities'] = $dbcon->SelectRow('SELECT SUM(`Tank`)/COUNT(`freecompanyid`)*100 AS `Tank`, SUM(`Healer`)/COUNT(`freecompanyid`)*100 AS `Healer`, SUM(`DPS`)/COUNT(`freecompanyid`)*100 AS `DPS`, SUM(`Crafter`)/COUNT(`freecompanyid`)*100 AS `Crafter`, SUM(`Gatherer`)/COUNT(`freecompanyid`)*100 AS `Gatherer`, SUM(`Role-playing`)/COUNT(`freecompanyid`)*100 AS `Role-playing`, SUM(`Leveling`)/COUNT(`freecompanyid`)*100 AS `Leveling`, SUM(`Casual`)/COUNT(`freecompanyid`)*100 AS `Casual`, SUM(`Hardcore`)/COUNT(`freecompanyid`)*100 AS `Hardcore`, SUM(`Dungeons`)/COUNT(`freecompanyid`)*100 AS `Dungeons`, SUM(`Guildhests`)/COUNT(`freecompanyid`)*100 AS `Guildhests`, SUM(`Trials`)/COUNT(`freecompanyid`)*100 AS `Trials`, SUM(`Raids`)/COUNT(`freecompanyid`)*100 AS `Raids`, SUM(`PvP`)/COUNT(`freecompanyid`)*100 AS `PvP` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL');
                }
                #Get statistics by monthly ranks
                if (!$nocache && !empty($json['freecompany']['ranking']['monthly'])) {
                    $data['freecompany']['ranking']['monthly'] = $json['freecompany']['ranking']['monthly'];
                } else {
                    $data['freecompany']['ranking']['monthly'] = $dbcon->SelectAll('SELECT `tempresult`.*, `ffxiv__freecompany`.`name`, `ffxiv__freecompany`.`crest` AS `icon`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`monthly`)*100 AS `ratio` FROM `ffxiv__freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `ffxiv__freecompany_ranking` `sub`)) `tempresult` INNER JOIN `ffxiv__freecompany` ON `ffxiv__freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC');
                    if (count($data['freecompany']['ranking']['monthly']) > 1) {
                        $data['freecompany']['ranking']['monthly'] = $ArrayHelpers->topAndBottom($data['freecompany']['ranking']['monthly'], 20);
                    } else {
                        $data['freecompany']['ranking']['monthly'] = [];
                    }
                }
                #Get statistics by weekly ranks
                if (!$nocache && !empty($json['freecompany']['ranking']['weekly'])) {
                    $data['freecompany']['ranking']['weekly'] = $json['freecompany']['ranking']['weekly'];
                } else {
                    $data['freecompany']['ranking']['weekly'] = $dbcon->SelectAll('SELECT `tempresult`.*, `ffxiv__freecompany`.`name`, `ffxiv__freecompany`.`crest` AS `icon`, \'freecompany\' AS `type` FROM (SELECT `main`.`freecompanyid` AS `id`, 1/(`members`*`weekly`)*100 AS `ratio` FROM `ffxiv__freecompany_ranking` `main` WHERE `main`.`date` = (SELECT MAX(`sub`.`date`) FROM `ffxiv__freecompany_ranking` `sub`)) `tempresult` INNER JOIN `ffxiv__freecompany` ON `ffxiv__freecompany`.`freecompanyid` = `tempresult`.`id` ORDER BY `ratio` DESC');
                    if (count($data['freecompany']['ranking']['weekly']) > 1) {
                        $data['freecompany']['ranking']['weekly'] = $ArrayHelpers->topAndBottom($data['freecompany']['ranking']['weekly'], 20);
                    } else {
                        $data['freecompany']['ranking']['weekly'] = [];
                    }
                }
                #Get most popular crests
                if (!$nocache && !empty($json['freecompany']['crests'])) {
                    $data['freecompany']['crests'] = $json['freecompany']['crests'];
                } else {
                    $data['freecompany']['crests'] = $dbcon->countUnique('ffxiv__freecompany', 'crest', '`ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                }
                break;
            case 'cities':
                #Get statistics by city
                if (!$nocache && !empty($json['cities']['gender'])) {
                    $data['cities']['gender'] = $json['cities']['gender'];
                } else {
                    $data['cities']['gender'] = $dbcon->countUnique('ffxiv__character', 'cityid', '`ffxiv__character`.`deleted` IS NULL','ffxiv__city', 'INNER', 'cityid', '`ffxiv__character`.`genderid`, `ffxiv__city`.`city`', 'DESC', 0, ['`ffxiv__character`.`genderid`']);
                    #Add colors to cities
                    foreach ($data['cities']['gender'] as $key=>$city) {
                        $data['cities']['gender'][$key]['color'] = $Lodestone->colorCities($city['value']);
                    }
                    #Split cities by gender
                    $data['cities']['gender'] = $ArrayHelpers->splitByKey($data['cities']['gender'], 'genderid', ['female', 'male'], [0, 1]);
                }
                #City by free company
                if (!$nocache && !empty($json['cities']['free_company'])) {
                    $data['cities']['free_company'] = $json['cities']['free_company'];
                } else {
                    $data['cities']['free_company'] = $dbcon->countUnique('ffxiv__freecompany', 'estateid', '`ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__estate', 'INNER', 'estateid', '`ffxiv__estate`.`area`');
                    #Add colors to cities
                    foreach ($data['cities']['free_company'] as $key=>$city) {
                        $data['cities']['free_company'][$key]['color'] = $Lodestone->colorCities($city['value']);
                    }
                }
                #Grand companies distribution (characters)
                if (!$nocache && !empty($json['cities']['gc_characters'])) {
                    $data['cities']['gc_characters'] = $json['cities']['gc_characters'];
                } else {
                    $data['cities']['gc_characters'] = $dbcon->SelectAll('SELECT `ffxiv__city`.`city`, `ffxiv__grandcompany_rank`.`gc_name` AS `value`, COUNT(`ffxiv__character`.`characterid`) AS `count` FROM `ffxiv__character` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gcrankid`=`ffxiv__grandcompany_rank`.`gcrankid` WHERE `ffxiv__character`.`deleted` IS NULL AND `ffxiv__grandcompany_rank`.`gc_name` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to companies
                    foreach ($data['cities']['gc_characters'] as $key=>$company) {
                        $data['cities']['gc_characters'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    $data['cities']['gc_characters'] = $ArrayHelpers->splitByKey($data['cities']['gc_characters'], 'city', [], []);
                }
                #Grand companies distribution (free companies)
                if (!$nocache && !empty($json['cities']['gc_fc'])) {
                    $data['cities']['gc_fc'] = $json['cities']['gc_fc'];
                } else {
                    $data['cities']['gc_fc'] = $dbcon->SelectAll('SELECT `ffxiv__city`.`city`, `ffxiv__grandcompany_rank`.`gc_name` AS `value`, COUNT(`ffxiv__freecompany`.`freecompanyid`) AS `count` FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estateid`=`ffxiv__estate`.`estateid` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`cityid`=`ffxiv__city`.`cityid` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany_rank`.`gcrankid` WHERE `ffxiv__freecompany`.`deleted` IS NULL AND `ffxiv__freecompany`.`estateid` IS NOT NULL AND `ffxiv__grandcompany_rank`.`gc_name` IS NOT NULL GROUP BY `city`, `value` ORDER BY `count` DESC');
                    #Add colors to companies
                    foreach ($data['cities']['gc_fc'] as $key=>$company) {
                        $data['cities']['gc_fc'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    $data['cities']['gc_fc'] = $ArrayHelpers->splitByKey($data['cities']['gc_fc'], 'city', [], []);
                }
                break;
            case 'grandcompanies':
                #Get statistics for grand companies
                if (!$nocache && !empty($json['grand_companies']['population'])) {
                    $data['grand_companies']['population'] = $json['grand_companies']['population'];
                } else {
                    $data['grand_companies']['population'] = $dbcon->countUnique('ffxiv__character', 'gcrankid', '`ffxiv__character`.`deleted` IS NULL AND `ffxiv__character`.`gcrankid` IS NOT NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__character`.`genderid`, `ffxiv__grandcompany_rank`.`gc_name`', 'DESC', 0, ['`ffxiv__character`.`genderid`']);
                    #Add colors to companies
                    foreach ($data['grand_companies']['population'] as $key=>$company) {
                        $data['grand_companies']['population'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                    #Split companies by gender
                    $data['grand_companies']['population'] = $ArrayHelpers->splitByKey($data['grand_companies']['population'], 'genderid', ['female', 'male'], [0, 1]);
                    $data['grand_companies']['population']['free_company'] = $dbcon->countUnique('ffxiv__freecompany', 'grandcompanyid', '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__grandcompany_rank`.`gc_name`');
                    #Add colors to cities
                    foreach ($data['grand_companies']['population']['free_company'] as $key=>$company) {
                        $data['grand_companies']['population']['free_company'][$key]['color'] = $Lodestone->colorGC($company['value']);
                    }
                }
                #Grand companies ranks
                if (!$nocache && !empty($json['grand_companies']['ranks'])) {
                    $data['grand_companies']['ranks'] = $json['grand_companies']['ranks'];
                } else {
                    $data['grand_companies']['ranks'] = $ArrayHelpers->splitByKey($dbcon->countUnique('ffxiv__character', 'gcrankid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__grandcompany_rank', 'INNER', 'gcrankid', '`ffxiv__character`.`genderid`, `ffxiv__grandcompany_rank`.`gc_name`, `ffxiv__grandcompany_rank`.`gc_rank`', 'DESC', 0, ['`ffxiv__character`.`genderid`', '`ffxiv__grandcompany_rank`.`gc_name`']), 'gc_name', [], []);
                    #Split by gender
                    foreach ($data['grand_companies']['ranks'] as $key=>$company) {
                        $data['grand_companies']['ranks'][$key] = $ArrayHelpers->splitByKey($company, 'genderid', ['female', 'male'], [0, 1]);
                    }
                }
                break;
            case 'servers':
                #Characters
                if (!$nocache && !empty($json['servers']['female population']) && !empty($json['servers']['male population'])) {
                    $data['servers']['female population'] = $json['servers']['female population'];
                    $data['servers']['male population'] = $json['servers']['male population'];
                } else {
                    $data['servers']['characters'] = $ArrayHelpers->splitByKey($dbcon->countUnique('ffxiv__character', 'serverid', '`ffxiv__character`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__character`.`genderid`, `ffxiv__server`.`server`', 'DESC', 0, ['`ffxiv__character`.`genderid`']), 'genderid', ['female', 'male'], [0, 1]);
                    $data['servers']['female population'] = $ArrayHelpers->topAndBottom($data['servers']['characters']['female'], 20);
                    $data['servers']['male population'] = $ArrayHelpers->topAndBottom($data['servers']['characters']['male'], 20);
                    unset($data['servers']['characters']);
                }
                #Free companies
                if (!$nocache && !empty($json['servers']['Free Companies'])) {
                    $data['servers']['Free Companies'] = $json['servers']['Free Companies'];
                } else {
                    $data['servers']['Free Companies'] = $ArrayHelpers->topAndBottom($dbcon->countUnique('ffxiv__freecompany', 'serverid', '`ffxiv__freecompany`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`'), 20);
                }
                #Linkshells
                if (!$nocache && !empty($json['servers']['Linkshells'])) {
                    $data['servers']['Linkshells'] = $json['servers']['Linkshells'];
                } else {
                    $data['servers']['Linkshells'] = $ArrayHelpers->topAndBottom($dbcon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 0 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`server`'), 20);
                }
                #Crossworld linkshells
                if (!$nocache && !empty($json['servers']['crossworldlinkshell'])) {
                    $data['servers']['crossworldlinkshell'] = $json['servers']['crossworldlinkshell'];
                } else {
                    $data['servers']['crossworldlinkshell'] = $dbcon->countUnique('ffxiv__linkshell', 'serverid', '`ffxiv__linkshell`.`crossworld` = 1 AND `ffxiv__linkshell`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                }
                #PvP teams
                if (!$nocache && !empty($json['servers']['pvpteam'])) {
                    $data['servers']['pvpteam'] = $json['servers']['pvpteam'];
                } else {
                    $data['servers']['pvpteam'] = $dbcon->countUnique('ffxiv__pvpteam', 'datacenterid', '`ffxiv__pvpteam`.`deleted` IS NULL', 'ffxiv__server', 'INNER', 'serverid', '`ffxiv__server`.`datacenter`');
                }
                break;
            case 'achievements':
                #Get achievements statistics
                if (!$nocache && !empty($json['other']['achievements'])) {
                    $data['other']['achievements'] = $json['other']['achievements'];
                } else {
                    $data['other']['achievements'] = $dbcon->SelectAll('SELECT \'achievement\' as `type`, `ffxiv__achievement`.`category`, `ffxiv__achievement`.`achievementid` AS `id`, `ffxiv__achievement`.`icon`, `ffxiv__achievement`.`name` AS `name`, `count` FROM (SELECT `ffxiv__character_achievement`.`achievementid`, count(`ffxiv__character_achievement`.`achievementid`) AS `count` from `ffxiv__character_achievement` GROUP BY `ffxiv__character_achievement`.`achievementid` ORDER BY `count`) `tempresult` INNER JOIN `ffxiv__achievement` ON `tempresult`.`achievementid`=`ffxiv__achievement`.`achievementid` WHERE `ffxiv__achievement`.`category` IS NOT NULL ORDER BY `count` ASC');
                    #Split achievements by categories
                    $data['other']['achievements'] = $ArrayHelpers->splitByKey($data['other']['achievements'], 'category', [], []);
                    #Get only top 20 for each category
                    foreach ($data['other']['achievements'] as $key=>$category) {
                        $data['other']['achievements'][$key] = array_slice($category, 0, 20);
                    }
                }
                break;
            case 'timelines':
                #Get namedays timeline. Using custom SQL, since need special order by `namedayid`, instead of by `count`
                if (!$nocache && !empty($json['timelines']['nameday'])) {
                    $data['timelines']['nameday'] = $json['timelines']['nameday'];
                } else {
                    $data['timelines']['nameday'] = $dbcon->SelectAll('SELECT `ffxiv__nameday`.`nameday` AS `value`, COUNT(`ffxiv__character`.`namedayid`) AS `count` FROM `ffxiv__character` INNER JOIN `ffxiv__nameday` ON `ffxiv__character`.`namedayid`=`ffxiv__nameday`.`namedayid` GROUP BY `value` ORDER BY `ffxiv__nameday`.`namedayid`');
                }
                #Timeline of groups formations
                if (!$nocache && !empty($json['timelines']['formed'])) {
                    $data['timelines']['formed'] = $json['timelines']['formed'];
                } else {
                    $data['timelines']['formed'] = $dbcon->SelectAll(
                        'SELECT `formed` AS `value`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `formed`, COUNT(`formed`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` WHERE `formed` IS NOT NULL GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, COUNT(`formed`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` WHERE `formed` IS NOT NULL GROUP BY `formed`
                            UNION ALL
                            SELECT `formed`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`formed`) AS `pvpteams` FROM `ffxiv__pvpteam` WHERE `formed` IS NOT NULL GROUP BY `formed`
                        ) `tempresults`
                        GROUP BY `formed` ORDER BY `formed`'
                    );
                }
                #Timeline of entities registration
                if (!$nocache && !empty($json['timelines']['registered'])) {
                    $data['timelines']['registered'] = $json['timelines']['registered'];
                } else {
                    $data['timelines']['registered'] = $dbcon->SelectAll(
                        'SELECT `registered` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `registered`, COUNT(`registered`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__character` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, COUNT(`registered`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`registered`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` WHERE `registered` IS NOT NULL GROUP BY `registered`
                            UNION ALL
                            SELECT `registered`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`registered`) AS `pvpteams` FROM `ffxiv__pvpteam` WHERE `registered` IS NOT NULL GROUP BY `registered`
                        ) `tempresults`
                        GROUP BY `registered` ORDER BY `registered` '
                    );
                }
                #Timeline of entities deletion
                if (!$nocache && !empty($json['timelines']['deleted'])) {
                    $data['timelines']['deleted'] = $json['timelines']['deleted'];
                } else {
                    $data['timelines']['deleted'] = $dbcon->SelectAll(
                        'SELECT `deleted` AS `value`, SUM(`characters`) AS `characters`, SUM(`freecompanies`) AS `freecompanies`, SUM(`linkshells`) AS `linkshells`, SUM(`pvpteams`) AS `pvpteams` FROM (
                            SELECT `deleted`, COUNT(`deleted`) AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__character` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, COUNT(`deleted`) AS `freecompanies`, 0 AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__freecompany` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, COUNT(`deleted`) AS `linkshells`, 0 AS `pvpteams` FROM `ffxiv__linkshell` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                            UNION ALL
                            SELECT `deleted`, 0 AS `characters`, 0 AS `freecompanies`, 0 AS `linkshells`, COUNT(`deleted`) AS `pvpteams` FROM `ffxiv__pvpteam` WHERE `deleted` IS NOT NULL GROUP BY `deleted`
                        ) `tempresults`
                        GROUP BY `deleted` ORDER BY `deleted` '
                    );
                }
                break;
            case 'bugs':
                #Characters with no clan/race
                if (!$nocache && !empty($json['bugs']['noclan'])) {
                    $data['bugs']['noclan'] = $json['bugs']['noclan'];
                } else {
                    $data['bugs']['noclan'] = $dbcon->SelectAll('SELECT `characterid` AS `id`, `name`, `avatar` AS `icon`, \'character\' AS `type` FROM `ffxiv__character` WHERE `clanid` IS NULL AND `deleted` IS NULL ORDER BY `name`;');
                }
                #Groups with no members
                if (!$nocache && !empty($json['bugs']['nomembers'])) {
                    $data['bugs']['nomembers'] = $json['bugs']['nomembers'];
                } else {
                    $data['bugs']['nomembers'] = $dbcon->SelectAll(
                        'SELECT `freecompanyid` AS `id`, `name`, \'freecompany\' AS `type`, `crest` AS `icon` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL AND `freecompanyid` NOT IN (SELECT `freecompanyid` FROM `ffxiv__character` WHERE `freecompanyid` IS NOT NULL)
                        UNION
                        SELECT `linkshellid` AS `id`, `name`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, NULL AS `icon` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL AND `linkshellid` NOT IN (SELECT `linkshellid` FROM `ffxiv__linkshell_character`)
                        UNION
                        SELECT `pvpteamid` AS `id`, `name`, \'pvpteam\' AS `type`, `crest` AS `icon` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL AND `pvpteamid` NOT IN (SELECT `pvpteamid` FROM `ffxiv__character` WHERE `pvpteamid` IS NOT NULL)
                        ORDER BY `name`;'
                    );
                }
                break;
            case 'other':
                #Communities
                if (!$nocache && !empty($json['other']['communities'])) {
                    $data['other']['communities'] = $json['other']['communities'];
                } else {
                    $data['other']['communities'] = $ArrayHelpers->splitByKey($dbcon->SelectAll('
                        SELECT `type`, IF(`has_community`=0, \'No community\', \'Community\') AS `value`, count(`has_community`) AS `count` FROM (
                            SELECT \'Free Company\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`communityid` IS NULL, 0, 1) AS `has_community` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `count` DESC
                    '), 'type', [], []);
                    #Sanitize results
                    foreach ($data['other']['communities'] as $key=>$row) {
                        if (!empty($row[0])) {
                            $data['other']['communities'][$key][$row[0]['value']] = $row[0]['count'];
                        }
                        if (!empty($row[1])) {
                            $data['other']['communities'][$key][$row[1]['value']] = $row[1]['count'];
                        }
                        if (empty($data['other']['communities'][$key]['Community'])) {
                            $data['other']['communities'][$key]['Community'] = '0';
                        }
                        if (empty($data['other']['communities'][$key]['No community'])) {
                            $data['other']['communities'][$key]['No community'] = '0';
                        }
                        unset($data['other']['communities'][$key][0], $data['other']['communities'][$key][1]);
                    }
                }
                #Deleted entities statistics
                if (!$nocache && !empty($json['other']['entities'])) {
                    $data['other']['entities'] = $json['other']['entities'];
                } else {
                    $data['other']['entities'] = $dbcon->SelectAll('
                        SELECT CONCAT(IF(`deleted`=0, \'Active\', \'Deleted\'), \' \', `type`) AS `value`, count(`deleted`) AS `count` FROM (
                            SELECT \'Character\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__character`
                            UNION ALL
                            SELECT \'Free Company\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__freecompany`
                            UNION ALL
                            SELECT \'PvP Team\' AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__pvpteam`
                            UNION ALL
                            SELECT IF(`crossworld`=1, \'Crossworld Linkshell\', \'Linkshell\') AS `type`, IF(`deleted` IS NULL, 0, 1) AS `deleted` FROM `ffxiv__linkshell`
                        ) `tempresult`
                        GROUP BY `type`, `value` ORDER BY `count` DESC
                    ');
                }
                if (!$nocache && !empty($json['pvpteam']['crests'])) {
                    $data['pvpteam']['crests'] = $json['pvpteam']['crests'];
                } else {
                    $data['pvpteam']['crests'] = $dbcon->countUnique('ffxiv__pvpteam', 'crest', '`ffxiv__pvpteam`.`deleted` IS NULL AND `ffxiv__pvpteam`.`crest` IS NOT NULL', '', 'INNER', '', '', 'DESC', 20);
                }
                break;
        }
        unset($dbcon, $ArrayHelpers, $Lodestone);
        #Attempt to write to cache
        file_put_contents($cachepath, json_encode(array_merge($json, $data), JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
        return $data;
    }

    #Helper function to replace key names of 'value' with 'name' in
    private function valueToName(array $array): array
    {
        foreach($array as $key=>$row) {
            $array[$key]['name'] = $row['value'];
            unset($array[$key]['value']);
        }
        return $array;
    }

    #Function to show X random entities

    /**
     * @throws \Exception
     */
    public function GetRandomEntities(int $number): array
    {
        return (new Controller)->selectAll('
                (SELECT `characterid` AS `id`, \'character\' as `type`, `name`, `avatar` AS `icon`, 0 AS `crossworld` FROM `ffxiv__character` WHERE `characterid` IN (SELECT `characterid` FROM `ffxiv__character` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `freecompanyid` AS `id`, \'freecompany\' as `type`, `name`, `crest` AS `icon`, 0 AS `crossworld` FROM `ffxiv__freecompany` WHERE `freecompanyid` IN (SELECT `freecompanyid` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `linkshellid` AS `id`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') as `type`, `name`, NULL AS `icon`, `crossworld` FROM `ffxiv__linkshell` WHERE `linkshellid` IN (SELECT `linkshellid` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `pvpteamid` AS `id`, \'pvpteam\' as `type`, `name`, `crest` AS `icon`, 1 AS `crossworld` FROM `ffxiv__pvpteam`WHERE `pvpteamid` IN (SELECT `pvpteamid` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `achievementid` AS `id`, \'achievement\' as `type`, `name`, `icon`, 1 AS `crossworld` FROM `ffxiv__achievement` WHERE `achievementid` IN (SELECT `achievementid` FROM `ffxiv__achievement` ORDER BY RAND()) LIMIT '.$number.')
                ORDER BY RAND() LIMIT '.$number.'
        ');
    }

    #Function to show X fresh entities

    /**
     * @throws \Exception
     */
    public function GetLastEntities(int $number): array
    {
        return (new Controller)->selectAll(
            'SELECT * FROM (SELECT `characterid` as `id`, \'character\' as `type`, `name`, `avatar` AS `icon`, 0 AS `crossworld`, `updated` FROM `ffxiv__character` WHERE `deleted` IS NULL ORDER BY `updated` LIMIT '.$number.') AS `characters`
            UNION ALL
            SELECT * FROM (SELECT `freecompanyid` as `id`, \'freecompany\' as `type`, `name`, `crest` AS `icon`, 0 AS `crossworld`, `updated` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL ORDER BY `updated` LIMIT '.$number.') AS `companies`
            UNION ALL
            SELECT * FROM (SELECT `linkshellid` as `id`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') as `type`, `name`, NULL AS `icon`, `crossworld`, `updated` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL ORDER BY `updated` LIMIT '.$number.') AS `linkshells`
            UNION ALL
            SELECT * FROM (SELECT `pvpteamid` as `id`, \'pvpteam\' as `type`, `name`, `crest` AS `icon`, 0 AS `crossworld`, `updated` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL ORDER BY `updated` LIMIT '.$number.') AS `pvp`
            UNION ALL
            SELECT * FROM (SELECT `achievementid` as `id`, \'achievement\' as `type`, `name`, `icon`, 0 AS `crossworld`, `updated` FROM `ffxiv__achievement` ORDER BY `updated` LIMIT '.$number.') AS `achievements`
            ORDER BY `updated` LIMIT '.$number.'
            ;'
        );
    }
}
