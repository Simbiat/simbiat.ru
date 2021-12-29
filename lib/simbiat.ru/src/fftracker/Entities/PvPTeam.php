<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\HomePage;
use Simbiat\Lodestone;

class PvPTeam extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'pvpteam';
    protected string $idFormat = '/^[a-z0-9]{40}$/m';
    public array $dates = [];
    public ?string $community = null;
    public ?string $crest = null;
    public string $dataCenter;
    public array $oldNames = [];
    public array $members = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = $this->dbController->selectRow('SELECT * FROM `'.self::dbPrefix.'pvpteam` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'pvpteam`.`datacenterid`=`'.self::dbPrefix.'server`.`serverid` WHERE `pvpteamid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $this->dbController->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'pvpteam_names` WHERE `pvpteamid`=:id AND `name`<>:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $this->dbController->selectAll('SELECT \'character\' AS `type`, `'.self::dbPrefix.'pvpteam_character`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`pvp_matches` AS `matches`, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, `'.self::dbPrefix.'pvpteam_rank`.`rank`, `'.self::dbPrefix.'pvpteam_rank`.`pvprankid` FROM `'.self::dbPrefix.'pvpteam_character` LEFT JOIN `'.self::dbPrefix.'pvpteam_rank` ON `'.self::dbPrefix.'pvpteam_rank`.`pvprankid`=`'.self::dbPrefix.'pvpteam_character`.`rankid` LEFT JOIN `'.self::dbPrefix.'character` ON `'.self::dbPrefix.'pvpteam_character`.`characterid`=`'.self::dbPrefix.'character`.`characterid` WHERE `'.self::dbPrefix.'pvpteam_character`.`pvpteamid`=:id AND `current`=1 ORDER BY `'.self::dbPrefix.'pvpteam_character`.`rankid` , `'.self::dbPrefix.'character`.`name` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['datacenterid'], $data['serverid'], $data['server']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot'])) {
            if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400) {
                (new Cron)->add('ffUpdateEntity', [$this->id, 'pvpteam'], priority: 1, message: 'Updating PvP team with ID ' . $this->id);
            }
        }
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getPvPTeam($this->id)->getResult();
        if (empty($data['pvpteams'][$this->id]['dataCenter']) || empty($data['pvpteams'][$this->id]['members'])) {
            if (@$data['pvpteams'][$this->id]['members'] == 404) {
                return ['404' => true];
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for PvP Team '.$this->id;
                } else {
                    return 'Failed to get all necessary data for PvP Team '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['pvpteams'][$this->id];
        $data['404'] = false;
        unset($data['pageCurrent'], $data['pageTotal']);
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->dates = [
            'formed' => (empty($fromDB['formed']) ? null : strtotime($fromDB['formed'])),
            'registered' => strtotime($fromDB['registered']),
            'updated' => strtotime($fromDB['updated']),
            'deleted' => (empty($fromDB['deleted']) ? null : strtotime($fromDB['deleted'])),
        ];
        $this->community = $fromDB['communityid'];
        $this->crest = $fromDB['crest'];
        $this->dataCenter = $fromDB['datacenter'];
        $this->oldNames = $fromDB['oldnames'];
        $this->members = $fromDB['members'];
        foreach ($this->members as $key=>$member) {
            $this->members[$key]['matches'] = intval($member['matches']);
        }
    }

    #Function to update the entity
    protected function updateDB(): string|bool
    {
        try {
            #Attempt to get crest
            $this->lodestone['crest'] = $this->CrestMerge($this->id, $this->lodestone['crest']);
            #Main query to insert or update a PvP Team
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'pvpteam` (`pvpteamid`, `name`, `formed`, `registered`, `updated`, `deleted`, `datacenterid`, `communityid`, `crest`) VALUES (:pvpteamid, :name, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), :communityid, :crest) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=:formed, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `datacenterid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), `communityid`=:communityid, `crest`=COALESCE(:crest, `crest`);',
                [
                    ':pvpteamid'=>$this->id,
                    ':datacenter'=>$this->lodestone['dataCenter'],
                    ':name'=>$this->lodestone['name'],
                    ':formed'=>[$this->lodestone['formed'], 'date'],
                    ':communityid'=>[
                        (empty($this->lodestone['communityid']) ? NULL : $this->lodestone['communityid']),
                        (empty($this->lodestone['communityid']) ? 'null' : 'string'),
                    ],
                    ':crest'=>[
                        (empty($this->lodestone['crest']) ? NULL : $this->lodestone['crest']),
                        (empty($this->lodestone['crest']) ? 'null' : 'string'),
                    ]
                ],
            ];
            #Register PvP Team name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_names`(`pvpteamid`, `name`) VALUES (:pvpteamid, :name);',
                [
                    ':pvpteamid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $this->dbController->selectColumn('SELECT `characterid` FROM `'.self::dbPrefix.'pvpteam_character` WHERE `pvpteamid`=:pvpteamid AND `current`=1;', [':pvpteamid'=>$this->id]);
            #Process members, that left the team
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($this->lodestone['members'][$member])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `'.self::dbPrefix.'pvpteam_character` SET `current`=0 WHERE `pvpteamid`=:pvpId AND `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                            ':pvpId'=>$this->id,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($this->lodestone['members'])) {
                foreach ($this->lodestone['members'] as $member=>$details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $this->lodestone['members'][$member]['registered'] = $this->dbController->check('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if (!$this->lodestone['members'][$member]['registered']) {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `'.self::dbPrefix.'character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`, `pvp_matches`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :matches
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                                ':matches'=>(empty($details['feasts']) ? 0 : $details['feasts']),
                            ]
                        ];
                    }
                    #Link the character to team
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'pvpteam_character` (`pvpteamid`, `characterid`, `rankid`, `current`) VALUES (:pvpteamId, :characterid, (SELECT `pvprankid` FROM `'.self::dbPrefix.'pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), 1) ON DUPLICATE KEY UPDATE `current`=1, `rankid`=(SELECT `pvprankid` FROM `'.self::dbPrefix.'pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1);',
                        [
                            ':characterid'=>$member,
                            ':pvpteamId'=>$this->id,
                            ':rank'=>$details['rank'] ?? 'Member',
                        ],
                    ];
                }
            }
            #Running the queries we've accumulated
            $this->dbController->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($this->lodestone['members'])) {
                $this->charMassCron($this->lodestone['members']);
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
            #Remove characters from group
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'pvpteam_character` SET `current`=0 WHERE `pvpteamid`=:groupId;',
                [':groupId' => $this->id,]
            ];
            #Update PvP Team
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'pvpteam` SET `deleted` = UTC_DATE() WHERE `pvpteamid` = :id', [':id'=>$this->id],
            ];
            return $this->dbController->query($queries);
        } catch (\Throwable $e) {
            HomePage::error_log($e);
            return false;
        }
    }
}
