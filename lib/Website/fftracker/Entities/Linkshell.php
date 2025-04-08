<?php
declare(strict_types = 1);

namespace Simbiat\Website\fftracker\Entities;

use Simbiat\Cron\TaskInstance;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\fftracker\Entity;
use Simbiat\Lodestone;

/**
 * Class representing a FFXIV linkshell (chat group)
 */
class Linkshell extends Entity
{
    #Custom properties
    protected const string entityType = 'linkshell';
    protected const bool crossworld = false;
    public array $dates = [];
    public ?string $community = null;
    public ?string $server = null;
    public ?string $dataCenter = null;
    public array $oldNames = [];
    public array $members = [];
    
    /**Function to get initial data from DB
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = Config::$dbController->selectRow('SELECT * FROM `ffxiv__linkshell` LEFT JOIN `ffxiv__server` ON `ffxiv__linkshell`.`serverid`=`ffxiv__server`.`serverid` WHERE `linkshellid`=:id', [':id' => $this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = Config::$dbController->selectColumn('SELECT `name` FROM `ffxiv__linkshell_names` WHERE `linkshellid`=:id AND `name`<>:name', [':id' => $this->id, ':name' => $data['name']]);
        #Get members
        $data['members'] = Config::$dbController->selectAll('SELECT \'character\' AS `type`, `ffxiv__linkshell_character`.`characterid` AS `id`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar` AS `icon`, `ffxiv__linkshell_rank`.`rank`, `ffxiv__linkshell_rank`.`lsrankid`, `userid` FROM `ffxiv__linkshell_character` LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`characterid`=`ffxiv__linkshell_character`.`characterid` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_rank`.`lsrankid`=`ffxiv__linkshell_character`.`rankid` LEFT JOIN `ffxiv__character` ON `ffxiv__linkshell_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `ffxiv__linkshell_character`.`linkshellid`=:id AND `current`=1 ORDER BY `ffxiv__linkshell_character`.`rankid` , `ffxiv__character`.`name` ', [':id' => $this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['serverid']);
        if ($data['crossworld']) {
            unset($data['server']);
        }
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot']) && (time() - strtotime($data['updated'])) >= 86400) {
            if ((int)$data['crossworld'] === 0) {
                (new TaskInstance())->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$this->id, 'linkshell'], 'message' => 'Updating linkshell with ID '.$this->id, 'priority' => 1])->add();
            } else {
                (new TaskInstance())->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$this->id, 'crossworldlinkshell'], 'message' => 'Updating crossworld linkshell with ID '.$this->id, 'priority' => 1])->add();
            }
        }
        return $data;
    }
    
    /**
     * Get linkshell data from Lodestone
     * @param bool $allowSleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     *
     * @return string|array
     */
    public function getFromLodestone(bool $allowSleep = false): string|array
    {
        $Lodestone = (new Lodestone());
        $data = $Lodestone->getLinkshellMembers($this->id, 0)->getResult();
        if (empty($data['linkshells']) || empty($data['linkshells'][$this->id]['server']) || (empty($data['linkshells'][$this->id]['members']) && (int)$data['linkshells'][$this->id]['memberscount'] > 0) || (!empty($data['linkshells'][$this->id]['members']) && \count($data['linkshells'][$this->id]['members']) < (int)$data['linkshells'][$this->id]['memberscount'])) {
            if (!empty($data['linkshells'][$this->id]['members']) && $data['linkshells'][$this->id]['members'] === 404) {
                $this->delete();
                return ['404' => true];
            }
            #Take a pause if we were throttled, and pause is allowed
            if (!empty($Lodestone->getLastError()['error']) && preg_match('/Lodestone has throttled the request, 429/', $Lodestone->getLastError()['error']) === 1) {
                if ($allowSleep) {
                    sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (empty($data['linkshells']) || empty($data['linkshells'][$this->id]) || !isset($data['linkshells'][$this->id]['pageTotal']) || $data['linkshells'][$this->id]['pageTotal'] !== 0) {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for '.($this::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id;
                }
                return 'Failed to get all necessary data for '.($this::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
            }
            #At some point empty linkshells became possible on lodestone, those that have a page, but no members at all, and are not searchable by name. Possibly private linkshells or something like that
            $data['linkshells'][$this->id]['empty'] = true;
        }
        $data = $data['linkshells'][$this->id];
        $data['id'] = $this->id;
        $data['404'] = false;
        unset($data['pageCurrent'], $data['pageTotal']);
        return $data;
    }
    
    /**
     * Function to process data from DB
     * @param array $fromDB
     *
     * @return void
     */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->community = $fromDB['communityid'];
        $this->dates = [
            'formed' => (empty($fromDB['formed']) ? null : strtotime($fromDB['formed'])),
            'registered' => strtotime($fromDB['registered']),
            'updated' => strtotime($fromDB['updated']),
            'deleted' => (empty($fromDB['deleted']) ? null : strtotime($fromDB['deleted'])),
        ];
        $this->oldNames = $fromDB['oldnames'];
        $this->members = $fromDB['members'];
        if ($this::crossworld) {
            $this->dataCenter = $fromDB['datacenter'];
        } else {
            $this->server = $fromDB['server'];
        }
    }
    
    /**
     * Function to update the entity
     *
     * @return bool
     */
    protected function updateDB(): bool
    {
        try {
            #If `empty` flag is set, it means that Lodestone page is empty, so we can't update anything besides name, data center and formed date
            if (isset($this->lodestone['empty']) && $this->lodestone['empty'] === true) {
                $queries[] = [
                    'UPDATE `ffxiv__linkshell` SET `name`=:name, `formed`=:formed, `updated`=CURRENT_TIMESTAMP(), `deleted`=NULL WHERE `linkshellid`=:linkshellid',
                    [
                        ':linkshellid' => $this->id,
                        ':name' => $this->lodestone['name'],
                        ':crossworld' => [$this::crossworld, 'bool'],
                        ':formed' => [
                            (empty($this->lodestone['formed']) ? null : $this->lodestone['formed']),
                            (empty($this->lodestone['formed']) ? 'null' : 'date'),
                        ],
                    ],
                ];
            } else {
                #Main query to insert or update a Linkshell
                $queries[] = [
                    'INSERT INTO `ffxiv__linkshell`(`linkshellid`, `name`, `crossworld`, `formed`, `registered`, `updated`, `deleted`, `serverid`, `communityid`) VALUES (:linkshellid, :name, :crossworld, :formed, UTC_DATE(), CURRENT_TIMESTAMP(), NULL, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server OR `datacenter`=:server ORDER BY `serverid` LIMIT 1), :communityid) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=:formed, `updated`=CURRENT_TIMESTAMP(), `deleted`=NULL, `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server OR `datacenter`=:server ORDER BY `serverid` LIMIT 1), `communityid`=:communityid;',
                    [
                        ':linkshellid' => $this->id,
                        ':server' => $this->lodestone['server'] ?? $this->lodestone['dataCenter'],
                        ':name' => $this->lodestone['name'],
                        ':crossworld' => [$this::crossworld, 'bool'],
                        ':formed' => [
                            (empty($this->lodestone['formed']) ? null : $this->lodestone['formed']),
                            (empty($this->lodestone['formed']) ? 'null' : 'date'),
                        ],
                        ':communityid' => [
                            (empty($this->lodestone['communityid']) ? null : $this->lodestone['communityid']),
                            (empty($this->lodestone['communityid']) ? 'null' : 'string'),
                        ],
                    ],
                ];
            }
            #Register Linkshell name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__linkshell_names`(`linkshellid`, `name`) VALUES (:linkshellid, :name);',
                [
                    ':linkshellid' => $this->id,
                    ':name' => $this->lodestone['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = Config::$dbController->selectColumn('SELECT `characterid` FROM `ffxiv__linkshell_character` WHERE `linkshellid`=:linkshellid AND `current`=1;', [':linkshellid' => $this->id]);
            #Process members, that left the linkshell
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($this->lodestone['members'][$member])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `ffxiv__linkshell_character` SET `current`=0 WHERE `linkshellid`=:linkshellId AND `characterid`=:characterid;',
                        [
                            ':characterid' => $member,
                            ':linkshellId' => $this->id,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($this->lodestone['members'])) {
                foreach ($this->lodestone['members'] as $member => $details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $this->lodestone['members'][$member]['registered'] = Config::$dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid`=:characterid', [':characterid' => $member]);
                    if (!$this->lodestone['members'][$member]['registered']) {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__character`(
                            `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`
                        )
                        VALUES (
                            :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, CURRENT_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1)
                        ) ON DUPLICATE KEY UPDATE `deleted`=NULL;',
                            [
                                ':characterid' => $member,
                                ':server' => $details['server'],
                                ':name' => $details['name'],
                                ':avatar' => str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0.jpg'], '', $details['avatar']),
                                ':gcRank' => (empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                            ]
                        ];
                    }
                    #Insert/update character relationship with linkshell
                    $queries[] = [
                        'INSERT INTO `ffxiv__linkshell_character` (`linkshellid`, `characterid`, `rankid`, `current`) VALUES (:linkshellid, :memberid, (SELECT `lsrankid` FROM `ffxiv__linkshell_rank` WHERE `rank`=:rank LIMIT 1), 1) ON DUPLICATE KEY UPDATE `rankid`=(SELECT `lsrankid` FROM `ffxiv__linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), `current`=1;',
                        [
                            ':linkshellid' => $this->id,
                            ':memberid' => $member,
                            ':rank' => (empty($details['lsrank']) ? 'Member' : $details['lsrank'])
                        ],
                    ];
                }
            }
            #Running the queries we've accumulated
            Config::$dbController->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($this->lodestone['members'])) {
                $this->charMassCron($this->lodestone['members']);
            }
            return true;
        } catch (\Throwable $e) {
            Errors::error_log($e, 'linkshellid: '.$this->id);
            return false;
        }
    }
    
    /**
     * Delete linkshell
     * @return bool
     */
    protected function delete(): bool
    {
        try {
            $queries = [];
            #Remove characters from group
            $queries[] = [
                'UPDATE `ffxiv__linkshell_character` SET `current`=0 WHERE `linkshellid`=:groupId;',
                [':groupId' => $this->id,]
            ];
            #Update linkshell
            $queries[] = [
                'UPDATE `ffxiv__linkshell` SET `deleted` = COALESCE(`deleted`, UTC_DATE()), `updated`=CURRENT_TIMESTAMP() WHERE `linkshellid` = :id',
                [':id' => $this->id],
            ];
            return Config::$dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return false;
        }
    }
}