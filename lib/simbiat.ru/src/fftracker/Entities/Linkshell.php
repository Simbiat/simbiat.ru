<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Errors;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class Linkshell extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'linkshell';
    protected const crossworld = false;
    public array $dates = [];
    public ?string $community = null;
    public ?string $server = null;
    public ?string $dataCenter = null;
    public array $oldNames = [];
    public array $members = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = $this->dbController->selectRow('SELECT * FROM `'.self::dbPrefix.'linkshell` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'linkshell`.`serverid`=`'.self::dbPrefix.'server`.`serverid` WHERE `linkshellid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $this->dbController->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'linkshell_names` WHERE `linkshellid`=:id AND `name`<>:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $this->dbController->selectAll('SELECT \'character\' AS `type`, `'.self::dbPrefix.'linkshell_character`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`characterid` AS `icon`, `'.self::dbPrefix.'linkshell_rank`.`rank`, `'.self::dbPrefix.'linkshell_rank`.`lsrankid` FROM `'.self::dbPrefix.'linkshell_character` LEFT JOIN `'.self::dbPrefix.'linkshell_rank` ON `'.self::dbPrefix.'linkshell_rank`.`lsrankid`=`'.self::dbPrefix.'linkshell_character`.`rankid` LEFT JOIN `'.self::dbPrefix.'character` ON `'.self::dbPrefix.'linkshell_character`.`characterid`=`'.self::dbPrefix.'character`.`characterid` WHERE `'.self::dbPrefix.'linkshell_character`.`linkshellid`=:id AND `current`=1 ORDER BY `'.self::dbPrefix.'linkshell_character`.`rankid` , `'.self::dbPrefix.'character`.`name` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['manual'], $data['serverid']);
        if ($data['crossworld']) {
            unset($data['server']);
        }
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot'])) {
            if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400) {
                if ($data['crossworld'] == '0') {
                    (new Cron)->add('ffUpdateEntity', [$this->id, 'linkshell'], priority: 1, message: 'Updating linkshell with ID ' . $this->id);
                } else {
                    (new Cron)->add('ffUpdateEntity', [$this->id, 'crossworldlinkshell'], priority: 1, message: 'Updating crossworldlinkshell with ID ' . $this->id);
                }
            }
        }
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getLinkshellMembers($this->id, 0)->getResult();
        if (empty($data['linkshells'][$this->id]['server']) || (!empty($data['linkshells'][$this->id]['members']) && count($data['linkshells'][$this->id]['members']) < intval($data['linkshells'][$this->id]['memberscount'])) || (empty($data['linkshells'][$this->id]['members']) && intval($data['linkshells'][$this->id]['memberscount']) > 0)) {
            if (!empty($data['linkshells'][$this->id]['members']) && $data['linkshells'][$this->id]['members'] == 404) {
                return ['404' => true];
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for '.($this::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id;
                } else {
                    return 'Failed to get all necessary data for '.($this::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['linkshells'][$this->id];
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
        $this->oldNames = $fromDB['oldnames'];
        $this->members = $fromDB['members'];
        if ($this::crossworld) {
            $this->dataCenter = $fromDB['datacenter'];
        } else {
            $this->server = $fromDB['server'];
        }
    }

    #Function to update the entity
    protected function updateDB(bool $manual = false): string|bool
    {
        try {
            #Main query to insert or update a Linkshell
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'linkshell`(`linkshellid`, `name`, `manual`, `crossworld`, `formed`, `registered`, `updated`, `deleted`, `serverid`, `communityid`) VALUES (:linkshellid, :name, :manual, :crossworld, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1), :communityid) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=NULL, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `serverid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1), `communityid`=:communityid;',
                [
                    ':linkshellid'=>$this->id,
                    ':server'=>$this->lodestone['server'] ?? $this->lodestone['dataCenter'],
                    ':name'=>$this->lodestone['name'],
                    ':manual'=>[$manual, 'bool'],
                    ':crossworld'=>[$this::crossworld, 'bool'],
                    ':formed'=>[
                        (empty($this->lodestone['formed']) ? NULL : $this->lodestone['formed']),
                        (empty($this->lodestone['formed']) ? 'null' : 'date'),
                    ],
                    ':communityid'=>[
                        (empty($this->lodestone['communityid']) ? NULL : $this->lodestone['communityid']),
                        (empty($this->lodestone['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Linkshell name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_names`(`linkshellid`, `name`) VALUES (:linkshellid, :name);',
                [
                    ':linkshellid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $this->dbController->selectColumn('SELECT `characterid` FROM `'.self::dbPrefix.'linkshell_character` WHERE `linkshellid`=:linkshellid AND `current`=1;', [':linkshellid'=>$this->id]);
            #Process members, that left the linkshell
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($this->lodestone['members'][$member])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `'.self::dbPrefix.'linkshell_character` SET `current`=0 WHERE `linkshellid`=:linkshellId AND `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                            ':linkshellId'=>$this->id,
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
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1)
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                            ]
                        ];
                    }
                    #Insert/update character relationship with linkshell
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'linkshell_character` (`linkshellid`, `characterid`, `rankid`, `current`) VALUES (:linkshellid, :memberid, (SELECT `lsrankid` FROM `'.self::dbPrefix.'linkshell_rank` WHERE `rank`=:rank LIMIT 1), 1) ON DUPLICATE KEY UPDATE `rankid`=(SELECT `lsrankid` FROM `'.self::dbPrefix.'linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), `current`=1;',
                        [
                            ':linkshellid'=>$this->id,
                            ':memberid'=>$member,
                            ':rank'=>(empty($details['lsrank']) ? 'Member' : $details['lsrank'])
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
                'UPDATE `'.self::dbPrefix.'linkshell_character` SET `current`=0 WHERE `linkshellid`=:groupId;',
                [':groupId' => $this->id,]
            ];
            #Update linkshell
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'linkshell` SET `deleted` = UTC_DATE() WHERE `linkshellid` = :id',
                [':id'=>$this->id],
            ];
            return $this->dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
}
