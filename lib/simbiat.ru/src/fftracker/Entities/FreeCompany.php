<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class FreeCompany extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'freecompany';
    protected const idFormat = '/^\d{10}$/mi';

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT * FROM `'.self::dbPrefix.'freecompany` LEFT JOIN `'.self::dbPrefix.'server` ON `'.self::dbPrefix.'freecompany`.`serverid`=`'.self::dbPrefix.'server`.`serverid` LEFT JOIN `'.self::dbPrefix.'grandcompany_rank` ON `'.self::dbPrefix.'freecompany`.`grandcompanyid`=`'.self::dbPrefix.'grandcompany_rank`.`gcrankid` LEFT JOIN `'.self::dbPrefix.'timeactive` ON `'.self::dbPrefix.'freecompany`.`activeid`=`'.self::dbPrefix.'timeactive`.`activeid` LEFT JOIN `'.self::dbPrefix.'estate` ON `'.self::dbPrefix.'freecompany`.`estateid`=`'.self::dbPrefix.'estate`.`estateid` LEFT JOIN `'.self::dbPrefix.'city` ON `'.self::dbPrefix.'estate`.`cityid`=`'.self::dbPrefix.'city`.`cityid` WHERE `freecompanyid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }

        #Get old names
        $data['oldnames'] = $dbcon->selectColumn('SELECT `name` FROM `'.self::dbPrefix.'freecompany_names` WHERE `freecompanyid`=:id AND `name`!=:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = $dbcon->selectAll('SELECT `'.self::dbPrefix.'character`.`characterid`, `company_joined` AS `join`, `'.self::dbPrefix.'freecompany_rank`.`rankid`, `rankname` AS `rank`, `name`, `avatar` FROM `'.self::dbPrefix.'character` LEFT JOIN `'.self::dbPrefix.'freecompany_rank` ON `'.self::dbPrefix.'freecompany_rank`.`rankid`=`'.self::dbPrefix.'character`.`company_rank` AND `'.self::dbPrefix.'freecompany_rank`.`freecompanyid`=`'.self::dbPrefix.'character`.`freecompanyid` JOIN (SELECT `company_rank`, COUNT(*) AS `total` FROM `'.self::dbPrefix.'character` WHERE `'.self::dbPrefix.'character`.`freecompanyid`=:id GROUP BY `company_rank`) `ranklist` ON `ranklist`.`company_rank` = `'.self::dbPrefix.'character`.`company_rank` WHERE `'.self::dbPrefix.'character`.`freecompanyid`=:id ORDER BY `ranklist`.`total` , `ranklist`.`company_rank` , `'.self::dbPrefix.'character`.`name` ', [':id'=>$this->id]);
        #History of ranks. Ensuring that we get only the freshest 100 entries sorted from latest to newest
        $data['ranks_history'] = $dbcon->selectAll('SELECT * FROM (SELECT `date`, `weekly`, `monthly`, `members` FROM `'.self::dbPrefix.'freecompany_ranking` WHERE `freecompanyid`=:id ORDER BY `date` DESC LIMIT 100) `lastranks` ORDER BY `date` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['grandcompanyid'], $data['estateid'], $data['gcrankid'], $data['gc_rank'], $data['gc_icon'], $data['activeid'], $data['cityid'], $data['left'], $data['top'], $data['cityicon']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400 && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$this->id, 'freecompany'], priority: 1, message: 'Updating free company with ID '.$this->id);
        }
        unset($dbcon);
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getFreeCompany($this->id)->getFreeCompanyMembers($this->id, 0)->getResult();
        if (empty($data['freecompanies'][$this->id]['server']) || (!empty($data['freecompanies'][$this->id]['members']) && count($data['freecompanies'][$this->id]['members']) < intval($data['freecompanies'][$this->id]['members_count'])) || (empty($data['freecompanies'][$this->id]['members']) && intval($data['freecompanies'][$this->id]['members_count']) > 0)) {
            if (@$data['freecompanies'][$this->id] == 404) {
                $data['entitytype'] = 'freecompany';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Free Company '.$this->id;
                } else {
                    return 'Failed to get all necessary data for Free Company '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['freecompanies'][$this->id];
        $data['freecompanyid'] = $this->id;
        $data['entitytype'] = 'freecompany';
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
            $data['crest'] = $this->CrestMerge($data['freecompanyid'], $data['crest']);
            #Cache controller
            $dbController = (new Controller);
            #Main query to insert or update a Free Company
            $queries[] = [
                'INSERT INTO `'.self::dbPrefix.'freecompany` (
                    `freecompanyid`, `name`, `serverid`, `formed`, `registered`, `updated`, `deleted`, `grandcompanyid`, `tag`, `crest`, `rank`, `slogan`, `activeid`, `recruitment`, `communityid`, `estate_zone`, `estateid`, `estate_message`, `Role-playing`, `Leveling`, `Casual`, `Hardcore`, `Dungeons`, `Guildhests`, `Trials`, `Raids`, `PvP`, `Tank`, `Healer`, `DPS`, `Crafter`, `Gatherer`
                )
                VALUES (
                    :freecompanyid, :name, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_name`=:grandCompany ORDER BY `gcrankid` LIMIT 1), :tag, :crest, :rank, :slogan, (SELECT `activeid` FROM `'.self::dbPrefix.'timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), :recruitment, :communityid, :estate_zone, (SELECT `estateid` FROM `'.self::dbPrefix.'estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), :estate_message, :rolePlaying, :leveling, :casual, :hardcore, :dungeons, :guildhests, :trials, :raids, :pvp, :tank, :healer, :dps, :crafter, :gatherer
                )
                ON DUPLICATE KEY UPDATE
                    `name`=:name, `serverid`=(SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `grandcompanyid`=(SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_name`=:grandCompany ORDER BY `gcrankid` LIMIT 1), `tag`=:tag, `crest`=COALESCE(:crest, `crest`), `rank`=:rank, `slogan`=:slogan, `activeid`=(SELECT `activeid` FROM `'.self::dbPrefix.'timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), `recruitment`=:recruitment, `communityid`=:communityid, `estate_zone`=:estate_zone, `estateid`=(SELECT `estateid` FROM `'.self::dbPrefix.'estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), `estate_message`=:estate_message, `Role-playing`=:rolePlaying, `Leveling`=:leveling, `Casual`=:casual, `Hardcore`=:hardcore, `Dungeons`=:dungeons, `Guildhests`=:guildhests, `Trials`=:trials, `Raids`=:raids, `PvP`=:pvp, `Tank`=:tank, `Healer`=:healer, `DPS`=:dps, `Crafter`=:crafter, `Gatherer`=:gatherer;',
                [
                    ':freecompanyid'=>$data['freecompanyid'],
                    ':name'=>$data['name'],
                    ':server'=>$data['server'],
                    ':formed'=>[$data['formed'], 'date'],
                    ':grandCompany'=>$data['grandCompany'],
                    ':tag'=>$data['tag'],
                    ':crest'=>[
                        (empty($data['crest']) ? NULL : $data['crest']),
                        (empty($data['crest']) ? 'null' : 'string'),
                    ],
                    ':rank'=>$data['rank'],
                    ':slogan'=>[
                        (empty($data['slogan']) ? NULL : $data['slogan']),
                        (empty($data['slogan']) ? 'null' : 'string'),
                    ],
                    ':active'=>[
                        (($data['active'] == 'Not specified') ? NULL : (empty($data['active']) ? NULL : $data['active'])),
                        (($data['active'] == 'Not specified') ? 'null' : (empty($data['active']) ? 'null' : 'string')),
                    ],
                    ':recruitment'=>(strcasecmp($data['recruitment'], 'Open') === 0 ? 1 : 0),
                    ':estate_zone'=>[
                        (empty($data['estate']['name']) ? NULL : $data['estate']['name']),
                        (empty($data['estate']['name']) ? 'null' : 'string'),
                    ],
                    ':estate_address'=>[
                        (empty($data['estate']['address']) ? NULL : $data['estate']['address']),
                        (empty($data['estate']['address']) ? 'null' : 'string'),
                    ],
                    ':estate_message'=>[
                        (empty($data['estate']['greeting']) ? NULL : $data['estate']['greeting']),
                        (empty($data['estate']['greeting']) ? 'null' : 'string'),
                    ],
                    ':rolePlaying'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Role-playing', array_column($data['focus'], 'name'))]['enabled']),
                    ':leveling'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Leveling', array_column($data['focus'], 'name'))]['enabled']),
                    ':casual'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Casual', array_column($data['focus'], 'name'))]['enabled']),
                    ':hardcore'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Hardcore', array_column($data['focus'], 'name'))]['enabled']),
                    ':dungeons'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Dungeons', array_column($data['focus'], 'name'))]['enabled']),
                    ':guildhests'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Guildhests', array_column($data['focus'], 'name'))]['enabled']),
                    ':trials'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Trials', array_column($data['focus'], 'name'))]['enabled']),
                    ':raids'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('Raids', array_column($data['focus'], 'name'))]['enabled']),
                    ':pvp'=>(empty($data['focus']) ? 0 : $data['focus'][array_search('PvP', array_column($data['focus'], 'name'))]['enabled']),
                    ':tank'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Tank', array_column($data['seeking'], 'name'))]['enabled']),
                    ':healer'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Healer', array_column($data['seeking'], 'name'))]['enabled']),
                    ':dps'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('DPS', array_column($data['seeking'], 'name'))]['enabled']),
                    ':crafter'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Crafter', array_column($data['seeking'], 'name'))]['enabled']),
                    ':gatherer'=>(empty($data['seeking']) ? 0 : $data['seeking'][array_search('Gatherer', array_column($data['seeking'], 'name'))]['enabled']),
                    ':communityid'=>[
                        (empty($data['communityid']) ? NULL : $data['communityid']),
                        (empty($data['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Free Company name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_names`(`freecompanyid`, `name`) VALUES (:freecompanyid, :name);',
                [
                    ':freecompanyid'=>$data['freecompanyid'],
                    ':name'=>$data['name'],
                ],
            ];
            if (!empty($data['members'])) {
                #Adding ranking
                if (!empty($data['weekly_rank']) && !empty($data['monthly_rank'])) {
                    $queries[] = [
                        'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_ranking` (`freecompanyid`, `date`, `weekly`, `monthly`, `members`) SELECT * FROM (SELECT :freecompanyid AS `freecompanyid`, UTC_DATE() AS `date`, :weekly AS `weekly`, :monthly AS `monthly`, :members AS `members` FROM DUAL WHERE :freecompanyid NOT IN (SELECT `freecompanyid` FROM (SELECT * FROM `'.self::dbPrefix.'freecompany_ranking` WHERE `freecompanyid`=:freecompanyid ORDER BY `date` DESC LIMIT 1) `lastrecord` WHERE `weekly`=:weekly AND `monthly`=:monthly) LIMIT 1) `actualinsert`;',
                        [
                            ':freecompanyid' => $data['freecompanyid'],
                            ':weekly' => $data['weekly_rank'],
                            ':monthly' => $data['monthly_rank'],
                            ':members' => count($data['members']),
                        ],
                    ];
                }
            }
            #Get members as registered on tracker
            $trackMembers = $dbController->selectColumn('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `freecompanyid`=:fcId', [':fcId'=>$data['freecompanyid']]);
            #Process members, that left the company
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($data['members'][$member])) {
                    #Insert to list of ex-members
                    $queries[] = [
                        'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) VALUES (:characterid, :fcId);',
                        [
                            ':characterid'=>$member,
                            ':fcId'=>$data['freecompanyid'],
                        ],
                    ];
                    #Remove company details
                    $queries[] = [
                        'UPDATE `'.self::dbPrefix.'character` SET `freecompanyid`=NULL, `company_joined`=NULL, `company_rank`=NULL WHERE `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($data['members'])) {
                foreach ($data['members'] as $member=>$details) {
                    #Register or update rank name
                    $queries[] = [
                        'INSERT INTO `'.self::dbPrefix.'freecompany_rank` (`freecompanyid`, `rankid`, `rankname`) VALUE (:freecompanyid, :rankid, :rankName) ON DUPLICATE KEY UPDATE `rankname`=:rankName',
                        [
                            ':freecompanyid'=>$data['freecompanyid'],
                            ':rankid'=>$details['rankid'],
                            ':rankName'=>(empty($details['rank']) ? '' : $details['rank']),
                        ],
                    ];
                    #Check if member is registered on tracker, while saving the status for future use
                    $data['members'][$member]['registered'] = $dbController->check('SELECT `characterid` FROM `'.self::dbPrefix.'character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if ($data['members'][$member]['registered']) {
                        #Update company status
                        $queries[] = [
                            'UPDATE `'.self::dbPrefix.'character` SET `freecompanyid`=:fcId, `company_joined`=COALESCE(`company_joined`, UTC_DATE()), `company_rank`=:rankid WHERE `characterid`=:characterid;',
                            [
                                ':characterid'=>$member,
                                ':fcId'=>$data['freecompanyid'],
                                ':rankid'=>$details['rankid'],
                            ],
                        ];
                    } else {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `'.self::dbPrefix.'character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`, `freecompanyid`, `company_joined`, `company_rank`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `'.self::dbPrefix.'server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `'.self::dbPrefix.'grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1), :fcId, UTC_DATE(), :rankid
                            )',
                            [
                                ':characterid'=>$member,
                                ':server'=>$details['server'],
                                ':name'=>$details['name'],
                                ':avatar'=>str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank'=>(empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                                ':fcId'=>$data['freecompanyid'],
                                ':rankid'=>$details['rankid'],
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
                'INSERT IGNORE INTO `'.self::dbPrefix.'freecompany_x_character` (`characterid`, `freecompanyid`) SELECT `'.self::dbPrefix.'character`.`characterid`, `'.self::dbPrefix.'character`.`freecompanyid` FROM `'.self::dbPrefix.'character` WHERE `'.self::dbPrefix.'character`.`freecompanyid`=:groupId;',
                [':groupId' => $this->id,]
            ];
            #Update characters
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'character` SET `freecompanyid`=NULL, `company_joined`=NULL, `company_rank`=NULL WHERE `freecompanyid`=:groupId;', [':groupId' => $this->id,]
            ];
            #Remove ranks (not ranking!)
            $queries[] = [
                'DELETE FROM `'.self::dbPrefix.'freecompany_rank` WHERE `freecompanyid` = :id',
                [':id' => $this->id],
            ];
            #Update Free Company
            $queries[] = [
                'UPDATE `'.self::dbPrefix.'freecompany` SET `deleted` = UTC_DATE() WHERE `freecompanyid` = :id',
                [':id' => $this->id],
            ];
            return $dbController->query($queries);
        } catch (\Throwable $e) {
            error_log($e->getMessage()."\r\n".$e->getTraceAsString());
            return false;
        }
    }
}
