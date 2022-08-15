<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Errors;
use Simbiat\fftracker\Entity;
use Simbiat\HomePage;
use Simbiat\Lodestone;
use Simbiat\Security;

class FreeCompany extends Entity
{
    #Custom properties
    protected const entityType = 'freecompany';
    public array $dates = [];
    public ?string $tag = null;
    public ?string $crest = null;
    public int $rank = 0;
    public ?string $slogan = null;
    public bool $recruiting = false;
    public ?string $community = null;
    public ?string $grandCompany = null;
    public ?string $active = null;
    public array $location = [];
    public array $focus = [];
    public array $seeking = [];
    public array $oldNames = [];
    public array $ranking = [];
    public array $members = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = HomePage::$dbController->selectRow('SELECT * FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__server` ON `ffxiv__freecompany`.`serverid`=`ffxiv__server`.`serverid` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`grandcompanyid`=`ffxiv__grandcompany`.`gcId` LEFT JOIN `ffxiv__timeactive` ON `ffxiv__freecompany`.`activeid`=`ffxiv__timeactive`.`activeid` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estateid`=`ffxiv__estate`.`estateid` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`cityid`=`ffxiv__city`.`cityid` WHERE `freecompanyid`=:id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }

        #Get old names
        $data['oldnames'] = HomePage::$dbController->selectColumn('SELECT `name` FROM `ffxiv__freecompany_names` WHERE `freecompanyid`=:id AND `name`!=:name', [':id'=>$this->id, ':name'=>$data['name']]);
        #Get members
        $data['members'] = HomePage::$dbController->selectAll('SELECT \'character\' AS `type`, `ffxiv__freecompany_character`.`characterid` AS `id`, `ffxiv__freecompany_rank`.`rankid`, `rankname` AS `rank`, `name`, `ffxiv__character`.`avatar` AS `icon`, `userid` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`rankid`=`ffxiv__freecompany_character`.`rankid` AND `ffxiv__freecompany_rank`.`freecompanyid`=`ffxiv__freecompany_character`.`freecompanyid` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`characterid`=`ffxiv__freecompany_character`.`characterid` LEFT JOIN (SELECT `rankid`, COUNT(*) AS `total` FROM `ffxiv__freecompany_character` WHERE `ffxiv__freecompany_character`.`freecompanyid`=:id GROUP BY `rankid`) `ranklist` ON `ranklist`.`rankid` = `ffxiv__freecompany_character`.`rankid` WHERE `ffxiv__freecompany_character`.`freecompanyid`=:id AND `current`=1 ORDER BY `ranklist`.`total`, `ranklist`.`rankid` , `ffxiv__character`.`name`;', [':id'=>$this->id]);
        #History of ranks. Ensuring that we get only the freshest 100 entries sorted from latest to newest
        $data['ranks_history'] = HomePage::$dbController->selectAll('SELECT * FROM (SELECT `date`, `weekly`, `monthly`, `members` FROM `ffxiv__freecompany_ranking` WHERE `freecompanyid`=:id ORDER BY `date` DESC LIMIT 100) `lastranks` ORDER BY `date` ', [':id'=>$this->id]);
        #Clean up the data from unnecessary (technical) clutter
        unset($data['manual'], $data['gcId'], $data['estateid'], $data['gc_icon'], $data['activeid'], $data['cityid'], $data['left'], $data['top'], $data['cityicon']);
        #In case the entry is old enough (at least 1 day old) and register it for update. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot'])) {
            if (empty($data['deleted']) && (time() - strtotime($data['updated'])) >= 86400) {
                (new Cron)->add('ffUpdateEntity', [$this->id, 'freecompany'], priority: 1, message: 'Updating free company with ID ' . $this->id);
            }
        }
        return $data;
    }

    public function getFromLodestone(): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getFreeCompany($this->id)->getFreeCompanyMembers($this->id, 0)->getResult();
        if (empty($data['freecompanies'][$this->id]['server']) || (!empty($data['freecompanies'][$this->id]['members']) && count($data['freecompanies'][$this->id]['members']) < intval($data['freecompanies'][$this->id]['members_count'])) || (empty($data['freecompanies'][$this->id]['members']) && intval($data['freecompanies'][$this->id]['members_count']) > 0)) {
            if (!empty($data['freecompanies'][$this->id]) && $data['freecompanies'][$this->id] == 404) {
                return ['404' => true];
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Free Company '.$this->id;
                } else {
                    return 'Failed to get all necessary data for Free Company '.$this->id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['freecompanies'][$this->id];
        $data['id'] = $this->id;
        $data['404'] = false;
        return $data;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->dates = [
            'formed' => strtotime($fromDB['formed']),
            'registered' => strtotime($fromDB['registered']),
            'updated' => strtotime($fromDB['updated']),
            'deleted' => (empty($fromDB['deleted']) ? null : strtotime($fromDB['deleted'])),
        ];
        $this->location = [
            'dataCenter' => $fromDB['datacenter'],
            'server' => $fromDB['server'],
            'estate' => [
                'region' => $fromDB['region'],
                'city' => $fromDB['city'],
                'area' => $fromDB['area'],
                'ward' => intval($fromDB['ward']),
                'plot' => intval($fromDB['plot']),
                'name' => $fromDB['estate_zone'],
                'size' => intval($fromDB['size']),
                'message' => $fromDB['estate_message'],
            ],
        ];
        $this->tag = $fromDB['tag'];
        $this->crest = $fromDB['crest'];
        $this->rank = intval($fromDB['rank']);
        $this->slogan = $fromDB['slogan'];
        $this->recruiting = boolval($fromDB['recruitment']);
        $this->community = $fromDB['communityid'];
        $this->grandCompany = $fromDB['gcName'];
        $this->active = $fromDB['active'];
        $this->focus = [
            'Role-playing' => boolval($fromDB['Role-playing']),
            'Leveling' => boolval($fromDB['Leveling']),
            'Casual' => boolval($fromDB['Casual']),
            'Hardcore' => boolval($fromDB['Hardcore']),
            'Dungeons' => boolval($fromDB['Dungeons']),
            'Guildhests' => boolval($fromDB['Guildhests']),
            'Trials' => boolval($fromDB['Trials']),
            'Raids' => boolval($fromDB['Raids']),
            'PvP' => boolval($fromDB['PvP']),
        ];
        $this->seeking = [
            'Tank' => boolval($fromDB['Tank']),
            'Healer' => boolval($fromDB['Healer']),
            'DPS' => boolval($fromDB['DPS']),
            'Crafter' => boolval($fromDB['Crafter']),
            'Gatherer' => boolval($fromDB['Gatherer']),
        ];
        $this->oldNames = $fromDB['oldnames'];
        $this->ranking = $fromDB['ranks_history'];
        #Adjust types for ranking
        foreach ($this->ranking as $key=>$rank) {
            $this->ranking[$key]['date'] = strtotime($rank['date']);
            $this->ranking[$key]['weekly'] = intval($rank['weekly']);
            $this->ranking[$key]['monthly'] = intval($rank['monthly']);
            $this->ranking[$key]['members'] = intval($rank['members']);
        }
        $this->members = $fromDB['members'];
    }

    #Function to update the entity
    protected function updateDB(bool $manual = false): string|bool
    {
        try {
            #Attempt to get crest
            $this->lodestone['crest'] = $this->CrestMerge($this->id, $this->lodestone['crest']);
            #Main query to insert or update a Free Company
            $queries[] = [
                'INSERT INTO `ffxiv__freecompany` (
                    `freecompanyid`, `name`, `manual`, `serverid`, `formed`, `registered`, `updated`, `deleted`, `grandcompanyid`, `tag`, `crest`, `rank`, `slogan`, `activeid`, `recruitment`, `communityid`, `estate_zone`, `estateid`, `estate_message`, `Role-playing`, `Leveling`, `Casual`, `Hardcore`, `Dungeons`, `Guildhests`, `Trials`, `Raids`, `PvP`, `Tank`, `Healer`, `DPS`, `Crafter`, `Gatherer`
                )
                VALUES (
                    :freecompanyid, :name, :manual, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :formed, UTC_DATE(), UTC_TIMESTAMP(), NULL, (SELECT `gcId` FROM `ffxiv__grandcompany` WHERE `gcName`=:grandCompany), :tag, :crest, :rank, :slogan, (SELECT `activeid` FROM `ffxiv__timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), :recruitment, :communityid, :estate_zone, (SELECT `estateid` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), :estate_message, :rolePlaying, :leveling, :casual, :hardcore, :dungeons, :guildhests, :trials, :raids, :pvp, :tank, :healer, :dps, :crafter, :gatherer
                )
                ON DUPLICATE KEY UPDATE
                    `name`=:name, `serverid`=(SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), `updated`=UTC_TIMESTAMP(), `deleted`=NULL, `grandcompanyid`=(SELECT `gcId` FROM `ffxiv__grandcompany` WHERE `gcName`=:grandCompany), `tag`=:tag, `crest`=COALESCE(:crest, `crest`), `rank`=:rank, `slogan`=:slogan, `activeid`=(SELECT `activeid` FROM `ffxiv__timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), `recruitment`=:recruitment, `communityid`=:communityid, `estate_zone`=:estate_zone, `estateid`=(SELECT `estateid` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), `estate_message`=:estate_message, `Role-playing`=:rolePlaying, `Leveling`=:leveling, `Casual`=:casual, `Hardcore`=:hardcore, `Dungeons`=:dungeons, `Guildhests`=:guildhests, `Trials`=:trials, `Raids`=:raids, `PvP`=:pvp, `Tank`=:tank, `Healer`=:healer, `DPS`=:dps, `Crafter`=:crafter, `Gatherer`=:gatherer;',
                [
                    ':freecompanyid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                    ':manual'=>[$manual, 'bool'],
                    ':server'=>$this->lodestone['server'],
                    ':formed'=>[$this->lodestone['formed'], 'date'],
                    ':grandCompany'=>$this->lodestone['grandCompany'],
                    ':tag'=>$this->lodestone['tag'],
                    ':crest'=>[
                        (empty($this->lodestone['crest']) ? NULL : $this->lodestone['crest']),
                        (empty($this->lodestone['crest']) ? 'null' : 'string'),
                    ],
                    ':rank'=>$this->lodestone['rank'],
                    ':slogan'=>[
                        (empty($this->lodestone['slogan']) ? NULL : Security::sanitizeHTML($this->lodestone['slogan'])),
                        (empty($this->lodestone['slogan']) ? 'null' : 'string'),
                    ],
                    ':active'=>[
                        (($this->lodestone['active'] == 'Not specified') ? NULL : (empty($this->lodestone['active']) ? NULL : $this->lodestone['active'])),
                        (($this->lodestone['active'] == 'Not specified') ? 'null' : (empty($this->lodestone['active']) ? 'null' : 'string')),
                    ],
                    ':recruitment'=>(strcasecmp($this->lodestone['recruitment'], 'Open') === 0 ? 1 : 0),
                    ':estate_zone'=>[
                        (empty($this->lodestone['estate']['name']) ? NULL : $this->lodestone['estate']['name']),
                        (empty($this->lodestone['estate']['name']) ? 'null' : 'string'),
                    ],
                    ':estate_address'=>[
                        (empty($this->lodestone['estate']['address']) ? NULL : $this->lodestone['estate']['address']),
                        (empty($this->lodestone['estate']['address']) ? 'null' : 'string'),
                    ],
                    ':estate_message'=>[
                        (empty($this->lodestone['estate']['greeting']) ? NULL : Security::sanitizeHTML($this->lodestone['estate']['greeting'])),
                        (empty($this->lodestone['estate']['greeting']) ? 'null' : 'string'),
                    ],
                    ':rolePlaying'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Role-playing', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':leveling'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Leveling', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':casual'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Casual', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':hardcore'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Hardcore', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':dungeons'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Dungeons', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':guildhests'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Guildhests', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':trials'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Trials', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':raids'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('Raids', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':pvp'=>(empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][array_search('PvP', array_column($this->lodestone['focus'], 'name'))]['enabled']),
                    ':tank'=>(empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][array_search('Tank', array_column($this->lodestone['seeking'], 'name'))]['enabled']),
                    ':healer'=>(empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][array_search('Healer', array_column($this->lodestone['seeking'], 'name'))]['enabled']),
                    ':dps'=>(empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][array_search('DPS', array_column($this->lodestone['seeking'], 'name'))]['enabled']),
                    ':crafter'=>(empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][array_search('Crafter', array_column($this->lodestone['seeking'], 'name'))]['enabled']),
                    ':gatherer'=>(empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][array_search('Gatherer', array_column($this->lodestone['seeking'], 'name'))]['enabled']),
                    ':communityid'=>[
                        (empty($this->lodestone['communityid']) ? NULL : $this->lodestone['communityid']),
                        (empty($this->lodestone['communityid']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register Free Company name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__freecompany_names`(`freecompanyid`, `name`) VALUES (:freecompanyid, :name);',
                [
                    ':freecompanyid'=>$this->id,
                    ':name'=>$this->lodestone['name'],
                ],
            ];
            if (!empty($this->lodestone['members'])) {
                #Adding ranking
                if (!empty($this->lodestone['weekly_rank']) && !empty($this->lodestone['monthly_rank'])) {
                    $queries[] = [
                        'INSERT IGNORE INTO `ffxiv__freecompany_ranking` (`freecompanyid`, `date`, `weekly`, `monthly`, `members`) SELECT * FROM (SELECT :freecompanyid AS `freecompanyid`, UTC_DATE() AS `date`, :weekly AS `weekly`, :monthly AS `monthly`, :members AS `members` FROM DUAL WHERE :freecompanyid NOT IN (SELECT `freecompanyid` FROM (SELECT * FROM `ffxiv__freecompany_ranking` WHERE `freecompanyid`=:freecompanyid ORDER BY `date` DESC LIMIT 1) `lastrecord` WHERE `weekly`=:weekly AND `monthly`=:monthly) LIMIT 1) `actualinsert`;',
                        [
                            ':freecompanyid' => $this->id,
                            ':weekly' => $this->lodestone['weekly_rank'],
                            ':monthly' => $this->lodestone['monthly_rank'],
                            ':members' => count($this->lodestone['members']),
                        ],
                    ];
                }
            }
            #Get members as registered on tracker
            $trackMembers = HomePage::$dbController->selectColumn('SELECT `characterid` FROM `ffxiv__freecompany_character` WHERE `freecompanyid`=:fcId AND `current`=1;', [':fcId'=>$this->id]);
            #Process members, that left the company
            foreach ($trackMembers as $member) {
                #Check if member from tracker is present in Lodestone list
                if (!isset($this->lodestone['members'][$member])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `freecompanyid`=:fcId AND `characterid`=:characterid;',
                        [
                            ':characterid'=>$member,
                            ':fcId'=>$this->id,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($this->lodestone['members'])) {
                foreach ($this->lodestone['members'] as $member=>$details) {
                    #Register or update rank name
                    $queries[] = [
                        'INSERT INTO `ffxiv__freecompany_rank` (`freecompanyid`, `rankid`, `rankname`) VALUE (:freecompanyid, :rankid, :rankName) ON DUPLICATE KEY UPDATE `rankname`=:rankName',
                        [
                            ':freecompanyid'=>$this->id,
                            ':rankid'=>$details['rankid'],
                            ':rankName'=>(empty($details['rank']) ? '' : $details['rank']),
                        ],
                    ];
                    #Check if member is registered on tracker, while saving the status for future use
                    $this->lodestone['members'][$member]['registered'] = HomePage::$dbController->check('SELECT `characterid` FROM `ffxiv__character` WHERE `characterid`=:characterid', [':characterid'=>$member]);
                    if (!$this->lodestone['members'][$member]['registered']) {
                        #Create basic entry of the character
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__character`(
                                `characterid`, `serverid`, `name`, `registered`, `updated`, `avatar`, `gcrankid`
                            )
                            VALUES (
                                :characterid, (SELECT `serverid` FROM `ffxiv__server` WHERE `server`=:server), :name, UTC_DATE(), TIMESTAMPADD(SECOND, -3600, UTC_TIMESTAMP()), :avatar, `gcrankid` = (SELECT `gcrankid` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank`=:gcRank ORDER BY `gcrankid` LIMIT 1)
                            );',
                            [
                                ':characterid' => $member,
                                ':server' => $details['server'],
                                ':name' => $details['name'],
                                ':avatar' => str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg'], '', $details['avatar']),
                                ':gcRank' => (empty($details['grandCompany']['rank']) ? '' : $details['grandCompany']['rank']),
                            ]
                        ];
                    }
                    #Link the character to company
                    $queries[] = [
                        'INSERT INTO `ffxiv__freecompany_character` (`freecompanyid`, `characterid`, `rankid`, `current`) VALUES (:fcId, :characterid, :rankid, 1) ON DUPLICATE KEY UPDATE `current`=1, `rankid`=:rankid;',
                        [
                            ':characterid'=>$member,
                            ':fcId'=>$this->id,
                            ':rankid'=>$details['rankid'],
                        ],
                    ];
                }
            }
            #Running the queries we've accumulated
            HomePage::$dbController->query($queries);
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
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `freecompanyid`=:groupId;',
                [':groupId' => $this->id,]
            ];
            #Update Free Company
            $queries[] = [
                'UPDATE `ffxiv__freecompany` SET `deleted` = UTC_DATE() WHERE `freecompanyid` = :id',
                [':id' => $this->id],
            ];
            return HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
}
