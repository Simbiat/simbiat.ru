<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class PvPTeam extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'pvpteam';
    protected const idFormat = '/^[a-zA-Z0-9]{40}$/mi';

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `'.self::dbPrefix.'pvpteam` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'pvpteam`.`datacenterid`=`'.self::dbPrefix.'server`.`serverid` WHERE `pvpteamid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'pvpteam_names` WHERE `pvpteamid`=:id AND `name`<>:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `'.self::dbPrefix.'character`.`characterid`, `'.self::dbPrefix.'character`.`pvp_matches` AS `matches`, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`avatar`, `'.self::dbPrefix.'pvpteam_rank`.`rank`, `'.self::dbPrefix.'pvpteam_rank`.`pvprankid` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'pvpteam_rank` ON `'.self::dbPrefix.'pvpteam_rank`.`pvprankid`=`'.self::dbPrefix.'character`.`pvp_rank` WHERE `'.self::dbPrefix.'character`.`pvpteamid`=:id ORDER BY `'.self::dbPrefix.'character`.`pvp_rank` , `'.self::dbPrefix.'character`.`name` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['datacenterid'], $data['serverid'], $data['server']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$this->id, 'pvpteam'], priority: 1, message: 'Updating PvP team with ID '.$this->id);
        }
        unset($dbcon);
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getPvPTeam($this->id)->getResult();
        if (empty($data['pvpteams'][$this->id]['dataCenter']) || empty($data['pvpteams'][$this->id]['members'])) {
            if (@$data['pvpteams'][$this->id]['members'] == 404) {
                $data['entitytype'] = 'pvpteam';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for PvP Team '.$this->id;
                } else {
                    return 'Failed to get all necessary data for PvP Team '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['pvpteams'][$this->id];
        $data['pvpteamid'] = $this->id;
        $data['entitytype'] = 'pvpteam';
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
            #Attempt to get crest
            $data['crest'] = $this->CrestMerge($data['pvpteamid'], $data['crest']);
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a PvP Team
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'pvpteam` (`pvpteamid`, `name`, `formed`, `registered`, `updated`, `deleted`, `datacenterid`, `communityid`, `crest`) VALUES (:pvpteamid, :name, :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), :communityid, :crest) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=:formed, `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `datacenterid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `datacenter`=:datacenter ORDER BY `serverid` LIMIT 1), `communityid`=:communityid, `crest`=COALESCE(:crest, `crest`);',
                [
                    ':pvpteamid'=>$data['pvpteamid'],
                    ':datacenter'=>$data['dataCenter'],
                    ':name'=>$data['name'],
                    ':formed'=>[$data['formed'], 'date'],
                    ':communityid'=>[
                        (empty($data['communityid']) ? NULL : $data['communityid']),
                        (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                    ':crest'=>[
                        (empty($data['crest']) ? NULL : $data['crest']),
                        (empty($data['crest']) ? 'null' : 'string'),
                    ]
                ],
            ];
            #Register PvP Team name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_names`(`pvpteamid`, `name`) VALUES (:pvpteamid, :name);',
                [
                    ':pvpteamid'=>$data['pvpteamid'],
                    ':name'=>$data['name'],
                ],
            ];
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `pvpteamid`=:pvpteamid', [':pvpteamid'=>$data['pvpteamid']]);
            #Process members, that left the team
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) VALUES (:characterid, :pvpteamid);',
                        [
                            ':characterid'=>$member,
                            ':pvpteamid'=>$data['pvpteamid'],
                        ],
                    ];
                    #Remove team details
                    $queries[] = [
                        'UPDATE `'.self::dbPrefix.'character` SET `pvpteamid`=NULL, `pvp_joined`=NULL, `pvp_rank`=NULL WHERE `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if ($data['members'][$member]['registered']) {
                        #Update team status
                        $queries[] = [
                            'UPDATE `'.self::dbPrefix.'character` SET `pvpteamid`=:pvpteamid, `pvp_joined`=COALESCE(`pvp_joined`, UTC_DATE()), `pvp_rank`=(SELECT `pvprankid` FROM `'.self::dbPrefix.'pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), `pvp_matches`=:matches WHERE `characterid`=:characterid;',
                            [
                                ':characterid'=>$member,
                                ':pvpteamid'=>$data['pvpteamid'],
                                ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank']),
                                ':matches'=>(empty($details['feasts']) ? 0 : $details['feasts']),
                            ],
                        ];
                    } else {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `'.self::dbPrefix.'character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`, `pvpteamid`, `pvp_joined`, `pvp_rank`, `pvp_matches`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :pvpteamid, UTC_DATE(), (SELECT `pvprankid` FROM `'.self::dbPrefix.'pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1), :matches
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                                ':pvpteamid'=>$data['pvpteamid'],
                                ':rank'=>(empty($details['rank']) ? 'Member' : $details['rank']),
                                ':matches'=>(empty($details['feasts']) ? 0 : $details['feasts']),
                            ]
                        ];
                    }
                }
            }
            #Running the queries we've accumulated
            $dbController->query($queries);
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
                'INSERT IGNORE INTO `'.self::dbPrefix.'pvpteam_x_character` (`characterid`, `pvpteamid`) SELECT `'.self::dbPrefix.'character`.`characterid`, `'.self::dbPrefix.'character`.`pvpteamid` FROM `'.self::dbPrefix.'character` WHERE `'.self::dbPrefix.'character`.`pvpteamid`=:groupId;',
                [':groupId'=>$this->id,]
            ];
            #Update characters
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'character` SET `pvpteamid`=NULL, `pvp_joined`=NULL, `pvp_rank`=NULL WHERE `pvpteamid`=:groupId;', [':groupId'=>$this->id,]
            ];
            #Update PvP Team
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'pvpteam` SET `deleted` = UTC_DATE() WHERE `pvpteamid` = :id', [':id'=>$this->id],
            ];
            return $dbController->query($queries);
        } catch (\Throwable $e) {
            error_log($e->getMessage()."\r\n".$e->getTraceAsString());
            return false;
        }
    }
}
