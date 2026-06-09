<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity\FFXIV;

use App\Service\Errors;
use App\Service\Sanitization;
use Simbiat\ArrayHelpers\Splitters;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;
use function count;

/**
 * Class representing a FFXIV free company (guild)
 */
class FreeCompany extends AbstractEntity
{
    #Custom properties
    protected const string ENTITY_TYPE = 'freecompany';
    public array $dates = [];
    public ?string $tag = null;
    public array $crest = [];
    public int $rank = 0;
    public ?string $slogan = null;
    public bool $recruiting = false;
    public ?string $community = null;
    public ?string $grand_company = null;
    public ?string $active = null;
    public array $location = [];
    public array $focus = [];
    public array $seeking = [];
    public array $old_names = [];
    public array $ranking = [];
    public array $members = [];
    public array $past_members = [];
    
    /**
     * Function to get initial data from DB
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = Query::query('SELECT * FROM `ffxiv__freecompany` LEFT JOIN `ffxiv__server` ON `ffxiv__freecompany`.`server_id`=`ffxiv__server`.`server_id` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__freecompany`.`gc_id`=`ffxiv__grandcompany`.`gc_id` LEFT JOIN `ffxiv__timeactive` ON `ffxiv__freecompany`.`active_id`=`ffxiv__timeactive`.`active_id` LEFT JOIN `ffxiv__estate` ON `ffxiv__freecompany`.`estate_id`=`ffxiv__estate`.`estate_id` LEFT JOIN `ffxiv__city` ON `ffxiv__estate`.`city_id`=`ffxiv__city`.`city_id` WHERE `fc_id`=:id', [':id' => $this->id], return: 'row');
        #Return empty if nothing was found
        if ($data === []) {
            return [];
        }
        #Get old names
        $data['old_names'] = Query::query('SELECT `name` FROM `ffxiv__freecompany_names` WHERE `fc_id`=:id AND `name`!=:name', [':id' => $this->id, ':name' => $data['name']], return: 'column');
        #Get members
        $data['members'] = Query::query('SELECT \'character\' AS `type`, `ffxiv__freecompany_character`.`character_id` AS `id`, `ffxiv__freecompany_rank`.`rank_id`, `rankname` AS `rank`, `name`, `current`, `ffxiv__character`.`avatar` AS `icon`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE uc__user_to_ff_character.`character_id`=`ffxiv__freecompany_character`.`character_id`) AS `user_id` FROM `ffxiv__freecompany_character`LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`rank_id`=`ffxiv__freecompany_character`.`rank_id` AND `ffxiv__freecompany_rank`.`fc_id`=`ffxiv__freecompany_character`.`fc_id` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`character_id`=`ffxiv__freecompany_character`.`character_id` LEFT JOIN (SELECT `rank_id`, COUNT(*) AS `total` FROM `ffxiv__freecompany_character` WHERE `ffxiv__freecompany_character`.`fc_id`=:id GROUP BY `rank_id`) `ranklist` ON `ranklist`.`rank_id` = `ffxiv__freecompany_character`.`rank_id` WHERE `ffxiv__freecompany_character`.`fc_id`=:id ORDER BY `ranklist`.`total`, `ranklist`.`rank_id` , `ffxiv__character`.`name`;', [':id' => $this->id], return: 'all');
        #History of ranks. Ensuring that we get only the freshest 100 entries sorted from latest to newest
        $data['ranks_history'] = Query::query('SELECT `date`, `weekly`, `monthly`, `members` FROM `ffxiv__freecompany_ranking` WHERE `fc_id`=:id ORDER BY `date` DESC LIMIT 100;', [':id' => $this->id], return: 'all');
        #Clean up the data from unnecessary (technical) clutter
        unset($data['gc_id'], $data['estate_id'], $data['gc_icon'], $data['active_id'], $data['city_id'], $data['left'], $data['top'], $data['city_icon']);
        return $data;
    }
    
    /**
     * Get data from Lodestone
     *
     * @param bool $allow_sleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     * @internal
     * @return string|array
     */
    public function getFromLodestone(bool $allow_sleep = false): string|array
    {
        $lodestone = new Lodestone();
        try {
            $data = $lodestone->getFreeCompany($this->id)->getFreeCompanyMembers($this->id, 0)->getResult();
        } catch (\Throwable $exception) {
            if (\preg_match('/Lodestone has throttled the request/ui', $exception->getMessage()) === 1) {
                if ($allow_sleep) {
                    #Take a pause if we were throttled, and pause is allowed
                    \sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (\preg_match('/Lodestone not available/ui', $exception->getMessage()) !== 1) {
                Errors::error_log($exception, ['last_error' => $lodestone->getLastError(), 'all_errors' => $lodestone->getErrors()]);
            }
            return 'Failed to get all necessary data for Free Company '.$this->id;
        }
        if (empty($data['freecompanies'][$this->id]['server']) || (empty($data['freecompanies'][$this->id]['members']) && (int)($data['freecompanies'][$this->id]['members_count'] ?? 0) > 0) || (!empty($data['freecompanies'][$this->id]['members']) && count($data['freecompanies'][$this->id]['members']) < (int)($data['freecompanies'][$this->id]['members_count'] ?? 0))) {
            if (!empty($data['freecompanies'][$this->id]) && (int)$data['freecompanies'][$this->id] === 404) {
                $this->delete();
                return ['404' => true];
            }
            Errors::error_log(new \RuntimeException('Failed to get all necessary data for Free Company '.$this->id), ['last_error' => $lodestone->getLastError(), 'all_errors' => $lodestone->getErrors()]);
            return 'Failed to get all necessary data for Free Company '.$this->id;
        }
        if (empty($data['freecompanies'][$this->id]['crest'][2]) && !empty($data['freecompanies'][$this->id]['crest'][1])) {
            $data['freecompanies'][$this->id]['crest'][2] = $data['freecompanies'][$this->id]['crest'][1];
            $data['freecompanies'][$this->id]['crest'][1] = null;
        }
        $data = $data['freecompanies'][$this->id];
        $data['id'] = $this->id;
        $data['404'] = false;
        return $data;
    }
    
    /**
     * Function to process data from DB
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->name = $from_db['name'];
        $this->dates = [
            'formed' => \strtotime($from_db['formed']),
            'registered' => \strtotime($from_db['registered']),
            'updated' => \strtotime($from_db['updated']),
            'deleted' => (empty($from_db['deleted']) ? null : \strtotime($from_db['deleted'])),
        ];
        $this->location = [
            'data_center' => $from_db['data_center'],
            'server' => $from_db['server'],
            'estate' => [
                'region' => $from_db['region'],
                'city' => $from_db['city'],
                'area' => $from_db['area'],
                'ward' => (int)$from_db['ward'],
                'plot' => (int)$from_db['plot'],
                'name' => $from_db['estate_zone'],
                'size' => (int)$from_db['size'],
                'message' => $from_db['estate_message'],
            ],
        ];
        $this->tag = $from_db['tag'];
        $this->crest = [
            0 => $from_db['crest_part_1'],
            1 => $from_db['crest_part_2'],
            2 => $from_db['crest_part_3'],
        ];
        $this->rank = (int)$from_db['rank'];
        $this->slogan = $from_db['slogan'];
        $this->recruiting = (bool)$from_db['recruitment'];
        $this->community = $from_db['community_id'];
        $this->grand_company = $from_db['gc_name'];
        $this->active = $from_db['active'];
        $this->focus = [
            'role-playing' => (bool)$from_db['role_playing'],
            'leveling' => (bool)$from_db['leveling'],
            'casual' => (bool)$from_db['casual'],
            'hardcore' => (bool)$from_db['hardcore'],
            'dungeons' => (bool)$from_db['dungeons'],
            'guildhests' => (bool)$from_db['guildhests'],
            'trials' => (bool)$from_db['trials'],
            'raids' => (bool)$from_db['raids'],
            'PvP' => (bool)$from_db['pvp'],
        ];
        $this->seeking = [
            'tank' => (bool)$from_db['tank'],
            'healer' => (bool)$from_db['healer'],
            'DPS' => (bool)$from_db['dps'],
            'crafter' => (bool)$from_db['crafter'],
            'gatherer' => (bool)$from_db['gatherer'],
        ];
        $this->old_names = $from_db['old_names'];
        $this->ranking = $from_db['ranks_history'];
        #Adjust types for ranking
        foreach ($this->ranking as $key => $rank) {
            $this->ranking[$key]['date'] = \strtotime($rank['date']);
            $this->ranking[$key]['weekly'] = (int)$rank['weekly'];
            $this->ranking[$key]['monthly'] = (int)$rank['monthly'];
            $this->ranking[$key]['members'] = (int)$rank['members'];
        }
        $members = Splitters::splitByKey($from_db['members'], 'current');
        $this->members = $members[1] ?? [];
        $this->past_members = $members[0] ?? [];
    }
    
    /**
     * Function to update the free company
     *
     * @return bool
     */
    protected function updateDB(): bool
    {
        try {
            #Download crest components
            $this->downloadCrestComponents($this->lodestone['crest']);
            if ($this->lodestone['active'] === 'Not specified') {
                $this->lodestone['active'] = null;
            }
            #Main query to insert or update a Free Company
            $queries[] = [
                'INSERT INTO `ffxiv__freecompany` (
                    `fc_id`, `name`, `server_id`, `formed`, `registered`, `updated`, `deleted`, `gc_id`, `tag`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `rank`, `slogan`, `active_id`, `recruitment`, `community_id`, `estate_zone`, `estate_id`, `estate_message`, `role_playing`, `leveling`, `casual`, `hardcore`, `dungeons`, `guildhests`, `trials`, `raids`, `pvp`, `tank`, `healer`, `dps`, `crafter`, `gatherer`
                )
                VALUES (
                    :fc_id, :name, (SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), :formed, CURRENT_TIMESTAMP(6), CURRENT_TIMESTAMP(6), NULL, (SELECT `gc_id` FROM `ffxiv__grandcompany` WHERE `gc_name`=:grand_company), :tag, :crest_part_1, :crest_part_2, :crest_part_3, :rank, :slogan, (SELECT `active_id` FROM `ffxiv__timeactive` WHERE `active`=:active LIMIT 1), :recruitment, :community_id, :estate_zone, (SELECT `estate_id` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), :estate_message, :role_playing, :leveling, :casual, :hardcore, :dungeons, :guildhests, :trials, :raids, :pvp, :tank, :healer, :dps, :crafter, :gatherer
                )
                ON DUPLICATE KEY UPDATE
                    `name`=:name, `server_id`=(SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), `formed`=:formed, `updated`=CURRENT_TIMESTAMP(6), `deleted`=NULL, `gc_id`=(SELECT `gc_id` FROM `ffxiv__grandcompany` WHERE `gc_name`=:grand_company), `tag`=:tag, `crest_part_1`=:crest_part_1, `crest_part_2`=:crest_part_2, `crest_part_3`=:crest_part_3, `rank`=:rank, `slogan`=:slogan, `active_id`=(SELECT `active_id` FROM `ffxiv__timeactive` WHERE `active`=:active AND `active` IS NOT NULL LIMIT 1), `recruitment`=:recruitment, `community_id`=:community_id, `estate_zone`=:estate_zone, `estate_id`=(SELECT `estate_id` FROM `ffxiv__estate` WHERE CONCAT(\'Plot \', `plot`, \', \', `ward`, \' Ward, \', `area`, \' (\', CASE WHEN `size` = 1 THEN \'Small\' WHEN `size` = 2 THEN \'Medium\' WHEN `size` = 3 THEN \'Large\' END, \')\')=:estate_address LIMIT 1), `estate_message`=:estate_message, `role_playing`=:role_playing, `leveling`=:leveling, `casual`=:casual, `hardcore`=:hardcore, `dungeons`=:dungeons, `guildhests`=:guildhests, `trials`=:trials, `raids`=:raids, `pvp`=:pvp, `tank`=:tank, `healer`=:healer, `dps`=:dps, `crafter`=:crafter, `gatherer`=:gatherer;',
                [
                    ':fc_id' => $this->id,
                    ':name' => $this->lodestone['name'],
                    ':server' => $this->lodestone['server'],
                    ':formed' => [$this->lodestone['formed'], 'datetime'],
                    ':grand_company' => $this->lodestone['grand_company'],
                    ':tag' => $this->lodestone['tag'],
                    ':crest_part_1' => [
                        (empty($this->lodestone['crest'][0]) ? NULL : $this->lodestone['crest'][0]),
                        (empty($this->lodestone['crest'][0]) ? 'null' : 'string'),
                    ],
                    ':crest_part_2' => [
                        (empty($this->lodestone['crest'][1]) ? NULL : $this->lodestone['crest'][1]),
                        (empty($this->lodestone['crest'][1]) ? 'null' : 'string'),
                    ],
                    ':crest_part_3' => [
                        (empty($this->lodestone['crest'][2]) ? NULL : $this->lodestone['crest'][2]),
                        (empty($this->lodestone['crest'][2]) ? 'null' : 'string'),
                    ],
                    ':rank' => $this->lodestone['rank'],
                    ':slogan' => [
                        (empty($this->lodestone['slogan']) ? NULL : Sanitization::sanitizeHTML($this->lodestone['slogan'])),
                        (empty($this->lodestone['slogan']) ? 'null' : 'string'),
                    ],
                    ':active' => [
                        (empty($this->lodestone['active']) ? NULL : $this->lodestone['active']),
                        (empty($this->lodestone['active']) ? 'null' : 'string'),
                    ],
                    ':recruitment' => (\strcasecmp($this->lodestone['recruitment'], 'Open') === 0 ? 1 : 0),
                    ':estate_zone' => [
                        (empty($this->lodestone['estate']['name']) ? NULL : $this->lodestone['estate']['name']),
                        (empty($this->lodestone['estate']['name']) ? 'null' : 'string'),
                    ],
                    ':estate_address' => [
                        (empty($this->lodestone['estate']['address']) ? NULL : $this->lodestone['estate']['address']),
                        (empty($this->lodestone['estate']['address']) ? 'null' : 'string'),
                    ],
                    ':estate_message' => [
                        (empty($this->lodestone['estate']['greeting']) ? NULL : Sanitization::sanitizeHTML($this->lodestone['estate']['greeting'])),
                        (empty($this->lodestone['estate']['greeting']) ? 'null' : 'string'),
                    ],
                    ':role_playing' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Role-playing', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':leveling' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Leveling', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':casual' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Casual', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':hardcore' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Hardcore', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':dungeons' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Dungeons', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':guildhests' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Guildhests', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':trials' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Trials', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':raids' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('Raids', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':pvp' => (empty($this->lodestone['focus']) ? 0 : $this->lodestone['focus'][\array_search('PvP', \array_column($this->lodestone['focus'], 'name'), true)]['enabled']),
                    ':tank' => (empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][\array_search('Tank', \array_column($this->lodestone['seeking'], 'name'), true)]['enabled']),
                    ':healer' => (empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][\array_search('Healer', \array_column($this->lodestone['seeking'], 'name'), true)]['enabled']),
                    ':dps' => (empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][\array_search('DPS', \array_column($this->lodestone['seeking'], 'name'), true)]['enabled']),
                    ':crafter' => (empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][\array_search('Crafter', \array_column($this->lodestone['seeking'], 'name'), true)]['enabled']),
                    ':gatherer' => (empty($this->lodestone['seeking']) ? 0 : $this->lodestone['seeking'][\array_search('Gatherer', \array_column($this->lodestone['seeking'], 'name'), true)]['enabled']),
                    ':community_id' => [
                        (empty($this->lodestone['community_id']) ? NULL : $this->lodestone['community_id']),
                        (empty($this->lodestone['community_id']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Register the Free Company name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__freecompany_names`(`fc_id`, `name`) VALUES (:fc_id, :name);',
                [
                    ':fc_id' => $this->id,
                    ':name' => $this->lodestone['name'],
                ],
            ];
            #Adding ranking
            if (!empty($this->lodestone['members']) && !empty($this->lodestone['weekly_rank']) && !empty($this->lodestone['monthly_rank'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__freecompany_ranking` (`fc_id`, `date`, `weekly`, `monthly`, `members`) SELECT * FROM (SELECT :fc_id AS `fc_id`, CURRENT_DATE() AS `date`, :weekly AS `weekly`, :monthly AS `monthly`, :members AS `members` FROM DUAL WHERE :fc_id NOT IN (SELECT `fc_id` FROM (SELECT * FROM `ffxiv__freecompany_ranking` WHERE `fc_id`=:fc_id ORDER BY `date` DESC LIMIT 1) `lastrecord` WHERE `weekly`=:weekly AND `monthly`=:monthly) LIMIT 1) `actualinsert`;',
                    [
                        ':fc_id' => $this->id,
                        ':weekly' => [$this->lodestone['weekly_rank'], 'int'],
                        ':monthly' => [$this->lodestone['monthly_rank'], 'int'],
                        ':members' => [count($this->lodestone['members']), 'int'],
                    ],
                ];
            }
            #Get members as registered on the tracker
            $track_members = Query::query('SELECT `character_id` FROM `ffxiv__freecompany_character` WHERE `fc_id`=:fc_id AND `current`=1;', [':fc_id' => $this->id], return: 'column');
            #Process members that left the company
            foreach ($track_members as $member) {
                #Check if member from tracker is present in a Lodestone list
                if (!\array_key_exists('members', $this->lodestone) || !\array_key_exists($member, $this->lodestone['members'])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `fc_id`=:fc_id AND `character_id`=:character_id;',
                        [
                            ':character_id' => $member,
                            ':fc_id' => $this->id,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($this->lodestone['members'])) {
                foreach ($this->lodestone['members'] as $member => $details) {
                    #Register or update rank name
                    $queries[] = [
                        'INSERT INTO `ffxiv__freecompany_rank` (`fc_id`, `rank_id`, `rankname`) VALUE (:fc_id, :rank_id, :rank_name) ON DUPLICATE KEY UPDATE `rankname`=:rank_name',
                        [
                            ':fc_id' => $this->id,
                            ':rank_id' => $details['rank_id'],
                            ':rank_name' => (empty($details['rank']) ? '' : $details['rank']),
                        ],
                    ];
                    $this->charQuickRegister($member, $this->lodestone['members'], $queries);
                    #Link the character to the company
                    $queries[] = [
                        'INSERT INTO `ffxiv__freecompany_character` (`fc_id`, `character_id`, `rank_id`, `current`) VALUES (:fc_id, :character_id, :rank_id, 1) ON DUPLICATE KEY UPDATE `current`=1, `rank_id`=:rank_id;',
                        [
                            ':character_id' => $member,
                            ':fc_id' => $this->id,
                            ':rank_id' => $details['rank_id'],
                        ],
                    ];
                }
            }
            #Running the queries we've accumulated
            Query::query($queries);
            #Schedule the proper update of any newly added characters
            if (!empty($this->lodestone['members'])) {
                $this->charMassCron($this->lodestone['members']);
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'fc_id: '.$this->id);
            return false;
        }
    }
    
    /** Delete free company
     * @return bool
     */
    protected function delete(): bool
    {
        try {
            $queries = [];
            #Remove characters from the group
            $queries[] = [
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `fc_id`=:group_id;',
                [':group_id' => $this->id,]
            ];
            #Update Free Company
            $queries[] = [
                'UPDATE `ffxiv__freecompany` SET `deleted` = COALESCE(`deleted`, CURRENT_TIMESTAMP(6)), `updated`=CURRENT_TIMESTAMP(6) WHERE `fc_id` = :id',
                [':id' => $this->id],
            ];
            return Query::query($queries);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $this->debug);
            return false;
        }
    }
}