<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class Character extends Entity
{
    use Traits;

    #Custom properties
    public ?string $avatarID = '';
    public array $dates = [];
    public array $biology = [];
    public array $location = [];
    public ?string $biography = null;
    public array $title = [];
    public array $grandCompany = [];
    public array $freeCompany = [];
    public array $pvp = [];
    public array $groups = [];
    public array $jobs = [];
    public array $achievements = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information. Using *, but add name, because otherwise Achievement name overrides Character name, and we do not want that
        $data = $this->dbController->selectRow('SELECT *, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`updated` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character`.`clanid` = `'.self::dbPrefix.'clan`.`clanid` LEFT JOIN `'.self::dbPrefix.'guardian` ON `'.self::dbPrefix.'character`.`guardianid` = `'.self::dbPrefix.'guardian`.`guardianid` LEFT JOIN `'.self::dbPrefix.'nameday` ON `'.self::dbPrefix.'character`.`namedayid` = `'.self::dbPrefix.'nameday`.`namedayid` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'character`.`cityid` = `'.self::dbPrefix.'city`.`cityid` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'character`.`serverid` = `'.self::dbPrefix.'server`.`serverid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'character`.`gcrankid` = `'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'achievement` ON `'.self::dbPrefix.'character`.`titleid` = `'.self::dbPrefix.'achievement`.`achievementid` WHERE `'.self::dbPrefix.'character`.`characterid` = :id;', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names. For now this is commented out due to cases of bullying, when the old names are learnt. They are still being collected, though for statistical purposes.
        #$data['oldnames'] = $this->dbController->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'character_names` WHERE `characterid`=:id AND `name`!=:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get previous known incarnations (combination of gender and race/clan)
        $data['incarnations'] = $this->dbController->selectAll('SELECT `genderid`, `'.self::dbPrefix.'clan`.`race`, `'.self::dbPrefix.'clan`.`clan` FROM `'.self::dbPrefix.'character_clans` LEFT JOIN `'.self::dbPrefix.'clan` ON `'.self::dbPrefix.'character_clans`.`clanid` = `'.self::dbPrefix.'clan`.`clanid` WHERE `'.self::dbPrefix.'character_clans`.`characterid`=:id AND (`'.self::dbPrefix.'character_clans`.`clanid`!=:clanid AND `'.self::dbPrefix.'character_clans`.`genderid`!=:genderid) ORDER BY `genderid` , `race` , `clan` ', [':id'=>$this->id, ':clanid'=>$data['clanid'], ':genderid'=>$data['genderid']]);
        #Get old servers
        $data['servers'] = $this->dbController->selectAll('SELECT `'.self::dbPrefix.'server`.`datacenter`, `'.self::dbPrefix.'server`.`server` FROM `'.self::dbPrefix.'character_servers` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'server`.`serverid`=`'.self::dbPrefix.'character_servers`.`serverid` WHERE `'.self::dbPrefix.'character_servers`.`characterid`=:id AND `'.self::dbPrefix.'character_servers`.`serverid` != :serverid ORDER BY `datacenter` , `server` ', [':id'=>$this->id, ':serverid'=>$data['serverid']]);
        #Get achievements
        $data['achievements'] = $this->dbController->selectAll('SELECT `'.self::dbPrefix.'achievement`.`achievementid`, `'.self::dbPrefix.'achievement`.`category`, `'.self::dbPrefix.'achievement`.`subcategory`, `'.self::dbPrefix.'achievement`.`name`, `time`, `icon` FROM `'.self::dbPrefix.'character_achievement` LEFT JOIN `'.self::dbPrefix.'achievement` ON `'.self::dbPrefix.'character_achievement`.`achievementid`=`'.self::dbPrefix.'achievement`.`achievementid` WHERE `'.self::dbPrefix.'character_achievement`.`characterid` = :id AND `'.self::dbPrefix.'achievement`.`category` IS NOT NULL AND `'.self::dbPrefix.'achievement`.`achievementid` IS NOT NULL ORDER BY `time` DESC, `name` ', [':id'=>$this->id]);
        #Get affiliated groups' details
        $data['groups'] = $this->dbController->selectAll(
            '(SELECT \'freecompany\' AS `type`, `'.self::dbPrefix.'character`.`freecompanyid` AS `id`, `'.self::dbPrefix.'freecompany`.`name` as `name`, 1 AS `current`, `'.self::dbPrefix.'character`.`company_joined` AS `join`, `'.self::dbPrefix.'character`.`company_rank` AS `rankid`, `'.self::dbPrefix.'freecompany_rank`.`rankname`, `'.self::dbPrefix.'freecompany`.`crest` AS `icon` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'character`.`freecompanyid`=`'.self::dbPrefix.'freecompany`.`freecompanyid` LEFT JOIN `'.self::dbPrefix.'freecompany_rank` ON `'.self::dbPrefix.'character`.`freecompanyid`=`'.self::dbPrefix.'freecompany_rank`.`freecompanyid` AND `'.self::dbPrefix.'character`.`company_rank`=`'.self::dbPrefix.'freecompany_rank`.`rankid` WHERE `characterid`=:id AND `'.self::dbPrefix.'character`.`freecompanyid` IS NOT NULL)
            UNION ALL
            (SELECT \'freecompany\' AS `type`, `'.self::dbPrefix.'freecompany_x_character`.`freecompanyid` AS `id`, `'.self::dbPrefix.'freecompany`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `'.self::dbPrefix.'freecompany`.`crest` AS `icon` FROM `'.self::dbPrefix.'freecompany_x_character` LEFT JOIN `'.self::dbPrefix.'freecompany` ON `'.self::dbPrefix.'freecompany_x_character`.`freecompanyid`=`'.self::dbPrefix.'freecompany`.`freecompanyid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `'.self::dbPrefix.'linkshell_character`.`linkshellid` AS `id`, `'.self::dbPrefix.'linkshell`.`name` as `name`, 1 AS `current`, NULL AS `join`, `'.self::dbPrefix.'linkshell_character`.`rankid`, `'.self::dbPrefix.'linkshell_rank`.`rank` AS `rankname`, NULL AS `icon` FROM `'.self::dbPrefix.'linkshell_character` LEFT JOIN `'.self::dbPrefix.'linkshell` ON `'.self::dbPrefix.'linkshell_character`.`linkshellid`=`'.self::dbPrefix.'linkshell`.`linkshellid` LEFT JOIN `'.self::dbPrefix.'linkshell_rank` ON `'.self::dbPrefix.'linkshell_character`.`rankid`=`'.self::dbPrefix.'linkshell_rank`.`lsrankid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') AS `type`, `'.self::dbPrefix.'linkshell_x_character`.`linkshellid` AS `id`, `'.self::dbPrefix.'linkshell`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, NULL AS `icon` FROM `'.self::dbPrefix.'linkshell_x_character` LEFT JOIN `'.self::dbPrefix.'linkshell` ON `'.self::dbPrefix.'linkshell_x_character`.`linkshellid`=`'.self::dbPrefix.'linkshell`.`linkshellid` WHERE `characterid`=:id)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `'.self::dbPrefix.'character`.`pvpteamid` AS `id`, `'.self::dbPrefix.'pvpteam`.`name` as `name`, 1 AS `current`, NULL AS `join`, `'.self::dbPrefix.'character`.`pvp_rank` AS `rankid`, `'.self::dbPrefix.'pvpteam_rank`.`rank` AS `rankname`, `'.self::dbPrefix.'pvpteam`.`crest` AS `icon` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'pvpteam` ON `'.self::dbPrefix.'character`.`pvpteamid`=`'.self::dbPrefix.'pvpteam`.`pvpteamid` LEFT JOIN `'.self::dbPrefix.'pvpteam_rank` ON `'.self::dbPrefix.'character`.`pvp_rank`=`'.self::dbPrefix.'pvpteam_rank`.`pvprankid` WHERE `characterid`=:id AND `'.self::dbPrefix.'character`.`pvpteamid` IS NOT NULL)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, `'.self::dbPrefix.'pvpteam_x_character`.`pvpteamid` AS `id`, `'.self::dbPrefix.'pvpteam`.`name` as `name`, 0 AS `current`, NULL AS `join`, NULL AS `rankid`, NULL AS `rankname`, `'.self::dbPrefix.'pvpteam`.`crest` AS `icon` FROM `'.self::dbPrefix.'pvpteam_x_character` LEFT JOIN `'.self::dbPrefix.'pvpteam` ON `'.self::dbPrefix.'pvpteam_x_character`.`pvpteamid`=`'.self::dbPrefix.'pvpteam`.`pvpteamid` WHERE `characterid`=:id)
            ORDER BY `current` DESC, `name` ASC;',
            [':id'=>$this->id]
        );
        #Clean up the data from unnecessary (technical) clutter
        unset($data['clanid'], $data['namedayid'], $data['achievementid'], $data['category'], $data['subcategory'], $data['howto'], $data['points'], $data['icon'], $data['item'], $data['itemicon'], $data['itemid'], $data['serverid']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffUpdateEntity', [$this->id, 'character'], priority: 1, message: 'Updating character with ID '.$this->id);
        }
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getCharacter($this->id)->getCharacterJobs($this->id)->getCharacterAchievements($this->id, false, 0, false, false, true)->getResult();
        if (empty($data['characters'][$this->id]['server'])) {
            if (@$data['characters'][$this->id] == 404) {
                return ['404' => true];
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Character '.$this->id;
                } else {
                    return 'Failed to get all necessary data for Character '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['characters'][$this->id];
        $data['404'] = false;
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->avatarID = $fromDB['avatar'];
        $this->dates = [
            'registered' => strtotime($fromDB['registered']),
            'updated' => strtotime($fromDB['updated']),
            'deleted' => (empty($fromDB['deleted']) ? null : strtotime($fromDB['deleted'])),
        ];
        $this->biology = [
            'gender' => intval($fromDB['genderid']),
            'race' => $fromDB['race'],
            'clan' => $fromDB['clan'],
            'nameday' => $fromDB['nameday'],
            'guardian' => $fromDB['guardian'],
            'guardianid' => $fromDB['guardianid'],
            'incarnations' => $fromDB['incarnations'],
        ];
        $this->location = [
            'datacenter' => $fromDB['datacenter'],
            'server' => $fromDB['server'],
            'region' => $fromDB['region'],
            'city' => $fromDB['city'],
            'cityid' => $fromDB['cityid'],
            'previousServers' => $fromDB['servers'],
        ];
        $this->biography = $fromDB['biography'];
        $this->title = [
            'title' => $fromDB['title'],
            'id' => $fromDB['titleid'],
        ];
        $this->grandCompany = [
            'name' => $fromDB['gc_name'],
            'rank' => $fromDB['gc_rank'],
            'gcrankid' => $fromDB['gcrankid'],
        ];
        $this->freeCompany = [
            'id' => $fromDB['freecompanyid'],
            'joined' => (empty($fromDB['company_joined']) ? null : strtotime($fromDB['company_joined'])),
            'rank' => $fromDB['company_rank'],
        ];
        $this->pvp = [
            'id' => $fromDB['pvpteamid'],
            'joined' => (empty($fromDB['pvp_joined']) ? null : strtotime($fromDB['pvp_joined'])),
            'rank' => $fromDB['pvp_rank'],
            'matches' => intval($fromDB['pvp_matches']),
        ];
        $this->groups = $fromDB['groups'];
        foreach ($this->groups as $key=>$group) {
            $this->groups[$key]['current'] = boolval($group['current']);
            $this->groups[$key]['join'] = (empty($group['join']) ? null : strtotime($group['join']));
        }
        $this->achievements = $fromDB['achievements'];
        foreach ($this->achievements as $key=>$achievement) {
            $this->achievements[$key]['time'] = (empty($achievement['time']) ? null : strtotime($achievement['time']));
        }
        #Remove all already processed elements to converts the rest to jobs array
        unset(
            $fromDB['characterid'], $fromDB['name'], $fromDB['avatar'], $fromDB['biography'], $fromDB['genderid'], $fromDB['datacenter'],
            $fromDB['registered'], $fromDB['updated'],$fromDB['deleted'], $fromDB['clan'], $fromDB['race'], $fromDB['server'],
            $fromDB['region'], $fromDB['city'], $fromDB['cityid'], $fromDB['nameday'], $fromDB['guardian'], $fromDB['guardianid'],
            $fromDB['servers'], $fromDB['incarnations'], $fromDB['title'], $fromDB['titleid'], $fromDB['dbid'], $fromDB['company_rank'],
            $fromDB['gcrankid'], $fromDB['freecompanyid'], $fromDB['company_joined'], $fromDB['gc_rank'], $fromDB['gc_name'],
            $fromDB['groups'], $fromDB['achievements'], $fromDB['pvpteamid'], $fromDB['pvp_joined'], $fromDB['pvp_rank'], $fromDB['pvp_matches'],
        );
        $this->jobs = $fromDB;
        foreach ($this->jobs as $key=>$job) {
            $this->jobs[$key] = intval($job);
        }
    }

    #Function to update the entity
    protected function updateDB(): string|bool
    {
        try {
            #Try to get current values of Free Company or PvP Team
            $this->lodestone['tracker_groups'] = $this->dbController->selectRow('SELECT `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches` FROM `'.self::dbPrefix.'character` WHERE `characterid` = :id', [':id' => $this->id]);
            #If this is a new character, ensure that array is properly populated
            if (empty($this->lodestone['tracker_groups'])) {
                $this->lodestone['tracker_groups'] = ['freecompanyid' => NULL, 'company_joined' => NULL, 'company_rank' => NULL, 'pvpteamid' => NULL, 'pvp_joined' => NULL, 'pvp_rank' => NULL, 'pvp_matches' => NULL];
            }
            #If character on Lodestone is registered in Free Company or PvP Team - check if they are registered on Tracker as well
            #If character on Lodestone is not registered in Free Company or PvP Team, add their IDs as NULL for consistency
            if (empty($this->lodestone['freeCompany']['id'])) {
                $this->lodestone['freeCompany']['id'] = NULL;
                $this->lodestone['freeCompany']['registered'] = false;
            } else {
                $this->lodestone['freeCompany']['registered'] = $this->dbController->check('SELECT `freecompanyid` FROM `'.self::dbPrefix.'freecompany` WHERE `freecompanyid` = :id', [':id' => $this->lodestone['freeCompany']['id']]);
            }
            if (empty($this->lodestone['pvp']['id'])) {
                $this->lodestone['pvp']['id'] = NULL;
                $this->lodestone['pvp']['registered'] = false;
            } else {
                $this->lodestone['pvp']['registered'] = $this->dbController->check('SELECT `pvpteamid` FROM `'.self::dbPrefix.'pvpteam` WHERE `pvpteamid` = :id', [':id' => $this->lodestone['pvp']['id']]);
            }
            #Flags to schedule Free Company or PvPTeam updates
            if ($this->lodestone['tracker_groups']['freecompanyid'] !== $this->lodestone['freeCompany']['id'] && $this->lodestone['freeCompany']['id'] !== NULL) {
                $fcCron = true;
            } else {
                $fcCron = false;
            }
            if ($this->lodestone['tracker_groups']['pvpteamid'] !== $this->lodestone['pvp']['id'] && $this->lodestone['pvp']['id'] !== NULL) {
                $pvpCron = true;
            } else {
                $pvpCron = false;
            }
            #Insert Free Companies and PvP Team if they are not registered
            if ($this->lodestone['freeCompany']['id'] !== NULL && $this->lodestone['freeCompany']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany` (`freecompanyid`, `name`, `serverid`, `updated`) VALUES (:fcId, :fcName, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':fcId' => $this->lodestone['freeCompany']['id'],
                        ':fcName' => $this->lodestone['freeCompany']['name'],
                        ':server'=>$this->lodestone['server'],
                    ],
                ];
            }
            if ($this->lodestone['pvp']['id'] !== NULL && $this->lodestone['pvp']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam` (`pvpteamid`, `name`, `datacenterid`, `updated`) VALUES (:pvpId, :pvpName, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()));',
                    [
                        ':pvpId' => $this->lodestone['pvp']['id'],
                        ':pvpName' => $this->lodestone['pvp']['name'],
                        ':server'=>$this->lodestone['server'],
                    ],
                ];
            }
            #Reduce number of <br>s in biography
            $this->lodestone['bio'] = $this->removeBrs($this->lodestone['bio'] ?? '');
            #Main query to insert or update a character
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'character`(
                    `characterid`, `serverid`, `name`, `registered`, `updated`, `deleted`, `biography`, `titleid`, `avatar`, `clanid`, `genderid`, `namedayid`, `guardianid`, `cityid`, `gcrankid`, `freecompanyid`, `company_joined`, `company_rank`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches`
                )
                VALUES (
                    :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), UTC_TIMESTAMP(), NULL, :biography, (SELECT `achievementid` as `titleid` FROM `'.self::dbPrefix.'achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), :avatar, (SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan), :genderid, (SELECT `namedayid` FROM `'.self::dbPrefix.'nameday` WHERE `nameday`=:nameday), (SELECT `guardianid` FROM `'.self::dbPrefix.'guardian` WHERE `guardian`=:guardian), (SELECT `cityid` FROM `'.self::dbPrefix.'city` WHERE `city`=:city), `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :fcId, :fcDate, NULL, :pvpId, :pvpDate, NULL, 0
                )
                ON DUPLICATE KEY UPDATE
                    `serverid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), `name`=:name, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `biography`=:biography, `titleid`=(SELECT `achievementid` as `titleid` FROM `'.self::dbPrefix.'achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), `avatar`=:avatar, `clanid`=(SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan), `genderid`=:genderid, `namedayid`=(SELECT `namedayid` FROM `'.self::dbPrefix.'nameday` WHERE `nameday`=:nameday), `guardianid`=(SELECT `guardianid` FROM `'.self::dbPrefix.'guardian` WHERE `guardian`=:guardian), `cityid`=(SELECT `cityid` FROM `'.self::dbPrefix.'city` WHERE `city`=:city), `gcrankid`=(SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), `freecompanyid`=:fcId, `company_joined`=:fcDate, `company_rank`=:fcRank, `pvpteamid`=:pvpId, `pvp_joined`=:pvpDate, `pvp_rank`=:pvpRank;',
                [
                    ':characterid'=>$this->id,
                    ':server'=>$this->lodestone['server'],
                    ':name'=>$this->lodestone['name'],
                    ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $this->lodestone['avatar']),
                    ':biography'=>[
                        (($this->lodestone['bio'] == '-') ? NULL : $this->lodestone['bio']),
                        (empty($this->lodestone['bio']) ? 'null' : (($this->lodestone['bio'] == '-') ? 'null' : 'string')),
                    ],
                    ':title'=>(empty($this->lodestone['title']) ? '' : $this->lodestone['title']),
                    ':clan'=>$this->lodestone['clan'],
                    ':genderid'=>($this->lodestone['gender']==='male' ? '1' : '0'),
                    ':nameday'=>$this->lodestone['nameday'],
                    ':guardian'=>$this->lodestone['guardian']['name'],
                    ':city'=>$this->lodestone['city']['name'],
                    ':gcRank'=>(empty($this->lodestone['grandCompany']['rank']) ? '' : $this->lodestone['grandCompany']['rank']),
                    ':fcId' => [$this->lodestone['freeCompany']['id'], ($this->lodestone['freeCompany']['id'] === NULL ? 'null' : 'string')],
                    ':fcDate' => [
                        ($this->lodestone['freeCompany']['id'] === NULL ? NULL : time()),
                        ($this->lodestone['freeCompany']['id'] === NULL ? 'null' : 'date'),
                    ],
                    ':fcRank' => [
                        ($this->lodestone['freeCompany']['id'] === NULL ? NULL : ($this->lodestone['tracker_groups']['company_rank'] === NULL ? NULL : $this->lodestone['tracker_groups']['company_rank'])),
                        ($this->lodestone['freeCompany']['id'] === NULL ? 'null' : ($this->lodestone['tracker_groups']['company_rank'] === NULL ? 'null' : 'int')),
                    ],
                    ':pvpId' => [$this->lodestone['pvp']['id'], ($this->lodestone['pvp']['id'] === NULL ? 'null' : 'string')],
                    ':pvpDate' => [
                        ($this->lodestone['pvp']['id'] === NULL ? NULL : time()),
                        ($this->lodestone['pvp']['id'] === NULL ? 'null' : 'date'),
                    ],
                    ':pvpRank' => [
                        ($this->lodestone['pvp']['id'] === NULL ? NULL : ($this->lodestone['tracker_groups']['pvp_rank'] === NULL ? NULL : $this->lodestone['tracker_groups']['pvp_rank'])),
                        ($this->lodestone['pvp']['id'] === NULL ? 'null' : ($this->lodestone['tracker_groups']['pvp_rank'] === NULL ? 'null' : 'int')),
                    ],
                ],
            ];
            #Update levels. Doing this in cycle since columns can vary. This can reduce performance, but so far this is the best idea I have to make it as automated as possible
            if (!empty($this->lodestone['jobs'])) {
                foreach ($this->lodestone['jobs'] as $job=>$level) {
                    #Remove spaces from the job name
                    $jobNoSpace = preg_replace('/\s*/', '', $job);
                    #Check if column exists in order to avoid errors. Checking that level is not empty to not waste time on updating zeros
                    if ($this->dbController->checkColumn(''.self::dbPrefix.'character', $jobNoSpace) && !empty($level['level'])) {
                        #Update level
                        /** @noinspection SqlResolve */
                        $queries[] = [
                            'UPDATE `'.self::dbPrefix.'character` SET `'.$jobNoSpace.'`=:level WHERE `characterid`=:characterid;',
                            [
                                ':characterid' => $this->id,
                                ':level' => [intval($level['level']), 'int'],
                            ],
                        ];
                    }
                }
            }
            #Insert server, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'character_servers`(`characterid`, `serverid`) VALUES (:characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server));',
                [
                    ':characterid'=>$this->id,
                    ':server'=>$this->lodestone['server'],
                ],
            ];
            #Insert name, if it has not been inserted yet
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'character_names`(`characterid`, `name`) VALUES (:characterid, :name);',
                [
                    ':characterid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                ],
            ];
            #Insert race, clan and sex combination, if it has not been inserted yet
            if (!empty($this->lodestone['clan'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'character_clans`(`characterid`, `genderid`, `clanid`) VALUES (:characterid, :genderid, (SELECT `clanid` FROM `'.self::dbPrefix.'clan` WHERE `clan`=:clan));',
                    [
                        ':characterid'=>$this->id,
                        ':genderid'=>($this->lodestone['gender']==='male' ? '1' : '0'),
                        ':clan'=>$this->lodestone['clan'],
                    ],
                ];
            }
            #Check if change of Free Company has been detected
            if ($fcCron || ($this->lodestone['tracker_groups']['freecompanyid'] !== NULL && $this->lodestone['freeCompany']['id'] === NULL)) {
                #Register previous Free Company
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                    [
                        ':characterid'=>$this->id,
                        ':fcId'=>$this->lodestone['tracker_groups']['freecompanyid'],
                    ],
                ];
            }
            #Check if change of PvP Team has been detected
            if ($fcCron || ($this->lodestone['tracker_groups']['pvpteamid'] !== NULL && $this->lodestone['pvp']['id'] === NULL)) {
                #Register previous PvP Team
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpId);',
                    [
                        ':characterid'=>$this->id,
                        ':pvpId'=>$this->lodestone['tracker_groups']['pvpteamid'],
                    ],
                ];
            }
            #Achievements
            if (!empty($this->lodestone['achievements']) && is_array($this->lodestone['achievements'])) {
                foreach ($this->lodestone['achievements'] as $achievementid=>$item) {
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points ON DUPLICATE KEY UPDATE `updated`=`updated`, `name`=:name, `icon`=:icon, `points`=:points;',
                        [
                            ':achievementid'=>$achievementid,
                            ':name'=>$item['name'],
                            ':icon'=>str_replace("https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/", "", $item['icon']),
                            ':points'=>$item['points'],
                        ],
                    ];
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'character_achievement` SET `characterid`=:characterid, `achievementid`=:achievementid, `time`=UTC_DATE() ON DUPLICATE KEY UPDATE `time`=:time;',
                        [
                            ':characterid'=>$this->id,
                            ':achievementid'=>$achievementid,
                            ':time'=>[$item['time'], 'date'],
                        ],
                    ];
                }
            }
            $this->dbController->query($queries);
            #Register Free Company update if change was detected
            if ($fcCron === true || $pvpCron === true) {
                #Cache CRON object
                $cron = (new Cron);
            }
            if ($fcCron) {
                $cron->add('ffUpdateEntity', [$this->lodestone['freeCompany']['id'], 'freecompany'], priority: 1, message: 'Updating free company with ID '.$this->lodestone['freeCompany']['id']);
            }
            #Register PvP Team update if change was detected
            if ($pvpCron) {
                $cron->add('ffUpdateEntity', [$this->lodestone['pvp']['id'], 'pvpteam'], priority: 1, message: 'Updating PvP team with ID '.$this->lodestone['pvp']['id']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    protected function delete(): bool
    {
        try {
            $queries = [];
            #Try to get current values of Free Company or PvP Team
            $groups = $this->dbController->selectRow('SELECT `freecompanyid`, `pvpteamid` FROM `'.self::dbPrefix.'character` WHERE `characterid` = :id', [':id' => $this->id]);
            #Remove from Free Company
            if (!empty($groups['freecompanyid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                    [
                        ':characterid'=>$this->id,
                        ':fcId'=>$groups['freecompanyid'],
                    ],
                ];
            }
            #Remove from PvP Team
            if (!empty($groups['pvpteamid'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpteamid);',
                    [
                        ':characterid'=>$this->id,
                        ':pvpteamid'=>$groups['pvpteamid'],
                    ],
                ];
            }
            #Remove from Linkshells
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_x_character` (`characterid`, `linkshellid`) SELECT `'.self::dbPrefix.'linkshell_character`.`characterid`, `'.self::dbPrefix.'linkshell_character`.`linkshellid` FROM `'.self::dbPrefix.'linkshell_character` WHERE `'.self::dbPrefix.'linkshell_character`.`characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            $queries[] = [
                'DELETE FROM `'.self::dbPrefix.'linkshell_character` WHERE `characterid`=:characterid;',
                [
                    ':characterid'=>$this->id,
                ],
            ];
            #Update character
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'character` SET `deleted` = UTC_DATE(), `freecompanyid` = NULL, `company_joined` = NULL, `company_rank` = NULL, `pvpteamid` = NULL, `pvp_joined` = NULL, `pvp_rank` = NULL WHERE `characterid` = :id',
                [':id'=>$this->id],
            ];
            return $this->dbController->query($queries);
        } catch (\Throwable $e) {
            error_log($e->getMessage()."\r\n".$e->getTraceAsString());
            return false;
        }
    }
}
