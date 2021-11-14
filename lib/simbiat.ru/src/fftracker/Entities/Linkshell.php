<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class Linkshell extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'linkshell';
    protected const idFormat = '/^\d{10}$/mi';
    protected const crossworld = false;

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `'.self::dbPrefix.'linkshell` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'linkshell`.`serverid`=`'.self::dbPrefix.'server`.`serverid` WHERE `linkshellid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'linkshell_names` WHERE `linkshellid`=:id AND `name`<>:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `'.self::dbPrefix.'linkshell_character`.`characterid`, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`avatar`, `'.self::dbPrefix.'linkshell_rank`.`rank`, `'.self::dbPrefix.'linkshell_rank`.`lsrankid` FROM `'.self::dbPrefix.'linkshell_character` LEFT JOIN `'.self::dbPrefix.'linkshell_rank` ON `'.self::dbPrefix.'linkshell_rank`.`lsrankid`=`'.self::dbPrefix.'linkshell_character`.`rankid` LEFT JOIN `'.self::dbPrefix.'character` ON `'.self::dbPrefix.'linkshell_character`.`characterid`=`'.self::dbPrefix.'character`.`characterid` WHERE `'.self::dbPrefix.'linkshell_character`.`linkshellid`=:id ORDER BY `'.self::dbPrefix.'linkshell_character`.`rankid` , `'.self::dbPrefix.'character`.`name` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['serverid']);
        if ($data['crossworld']) {
            unset($data['server']);
        }
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            if ($data['crossworld'] == '0') {
                (new Cron)->add('ffentityupdate', [$this->id, 'linkshell'], priority: 1, message: 'Updating linkshell with ID '.$this->id);
            } else {
                (new Cron)->add('ffentityupdate', [$this->id, 'crossworldlinkshell'], priority: 1, message: 'Updating crossworldlinkshell with ID '.$this->id);
            }
        }
        unset($dbcon);
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getLinkshellMembers($this->id, 0)->getResult();
        if (empty($data['linkshells'][$this->id]['server']) || (!empty($data['linkshells'][$this->id]['members']) && count($data['linkshells'][$this->id]['members']) < intval($data['linkshells'][$this->id]['memberscount'])) || (empty($data['linkshells'][$this->id]['members']) && intval($data['linkshells'][$this->id]['memberscount']) > 0)) {
            if (@$data['linkshells'][$this->id]['members'] == 404) {
                $data['entitytype'] = (self::crossworld ? 'crossworldlinkshell' : 'linkshell');
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for '.(self::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id;
                } else {
                    return 'Failed to get all necessary data for '.(self::crossworld ? 'Crossworld ' : '').'Linkshell '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['linkshells'][$this->id];
        $data['linkshellid'] = $this->id;
        $data['entitytype'] = (self::crossworld ? 'crossworldlinkshell' : 'linkshell');
        $data['404'] = false;
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {

    }

    #Function to update the entity
    public function update(): string|bool
    {
        try {
            $data = $this->getFromLodestone();
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a Linkshell
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'linkshell`(`linkshellid`, `name`, `crossworld`, `formed`, `registered`, `updated`, `deleted`, `serverid`) VALUES (:linkshellid, :name, :crossworld, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1)) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=NULL, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `serverid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server OR `datacenter`=:server LIMIT 1), `communityid`=:communityid;',
                [
                    ':linkshellid'=>$data['linkshellid'],
                    ':server'=>$data['server'] ?? $data['dataCenter'],
                    ':name'=>$data['name'],
                    ':crossworld'=>[self::crossworld, 'bool'],
                    ':formed'=>[
                        (empty($data['formed']) ? NULL : $data['formed']),
                        (empty($data['formed']) ? 'null' : 'date'),
                    ],
                    ':communityid'=>[
                        (empty($data['communityid']) ? NULL : $data['communityid']),
                        (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Linkshell name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_names`(`linkshellid`, `name`) VALUES (:linkshellid, :name);',
                [
                    ':linkshellid'=>$data['linkshellid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `'.self::dbPrefix.'linkshell_character` WHERE `linkshellid`=:linkshellid', [':linkshellid'=>$data['linkshellid']]);
            #Process members, that left the linkshell
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_x_character` (`characterid`, `linkshellid`) VALUES (:characterid, :linkshellid);',
                        [
                            ':characterid'=>$member,
                            ':linkshellid'=>$data['linkshellid'],
                        ],
                    ];
                    #Remove team details
                    $queries[] = [
                        'DELETE FROM `'.self::dbPrefix.'linkshell_character` WHERE `characterid`=:characterid AND `linkshellid`=:linkshellid;',
                        [
                            ':characterid'=>$member,
                            ':linkshellid'=>$data['linkshellid'],
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if (!$data['members'][$member]['registered']) {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `'.self::dbPrefix.'character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1)
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
                        'INSERT INTO `'.self::dbPrefix.'linkshell_character` (`linkshellid`, `characterid`, `rankid`) VALUES (:linkshellid, :memberid, (SELECT `lsrankid` FROM `'.self::dbPrefix.'linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1)) ON DUPLICATE KEY UPDATE `rankid`=(SELECT `lsrankid` FROM `'.self::dbPrefix.'linkshell_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1);',
                        [
                            ':linkshellid'=>$data['linkshellid'],
                            ':memberid'=>$member,
                            ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank'])
                        ],
                    ];
                }
            }
            #(new \Simbiat\HomeTests)->testDump($queries, false);
            #exit;
            #Running the queries we've accumulated
            (new Controller)->query($queries);
            #Schedule proper update of any newly added characters
            if (!empty($data['members'])) {
                $this->charMassCron($data['members']);
            }
            return true;
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    public function delete(): bool
    {
        try {
            #Cache DB Controller
            $dbController = (new Controller);
            $queries = [];
            #Remove characters from group
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'linkshell_x_character` (`characterid`, `linkshellid`) SELECT `'.self::dbPrefix.'linkshell_character`.`characterid`, `'.self::dbPrefix.'linkshell_character`.`linkshellid` FROM `'.self::dbPrefix.'linkshell_character` WHERE `'.self::dbPrefix.'linkshell_character`.`linkshellid`=:groupId;',
                [
                    ':groupId'=>$this->id,
                ]
            ];
            #Update characters
            $queries[] = [
                'DELETE FROM `'.self::dbPrefix.'linkshell_character` WHERE `linkshellid`=:groupId;',
                [
                    ':groupId'=>$this->id,
                ]
            ];
            #Update linkshell
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'linkshell` SET `deleted` = UTC_DATE() WHERE `linkshellid` = :id',
                [':id'=>$this->id],
            ];
            return $dbController->query($queries);
        } catch (\Throwable $e) {
            error_log($e->getMessage()."\r\n".$e->getTraceAsString());
            return false;
        }
    }
}
