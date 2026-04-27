<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity\FFXIV;

use App\Service\Errors;
use Simbiat\ArrayHelpers\Splitters;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;

/**
 * Class representing a FFXIV PvP Team
 */
class PvPTeam extends AbstractEntity
{
    #Custom properties
    protected const string ENTITY_TYPE = 'pvpteam';
    protected string $id_format = '/^[a-z\d]{40}$/m';
    public array $dates = [];
    public ?string $community = null;
    public array $crest = [];
    public string $data_center;
    public array $old_names = [];
    public array $members = [];
    public array $past_members = [];
    
    /**
     * Function to get initial data from DB
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = Query::query('SELECT * FROM `ffxiv__pvpteam` LEFT JOIN `ffxiv__server` ON `ffxiv__pvpteam`.`data_center_id`=`ffxiv__server`.`server_id` WHERE `pvp_id`=:id', [':id' => $this->id], return: 'row');
        #Return empty if nothing was found
        if ($data === []) {
            return [];
        }
        #Get old names
        $data['old_names'] = Query::query('SELECT `name` FROM `ffxiv__pvpteam_names` WHERE `pvp_id`=:id AND `name`<>:name', [':id' => $this->id, ':name' => $data['name']], return: 'column');
        #Get members
        $data['members'] = Query::query('SELECT \'character\' AS `type`, `ffxiv__pvpteam_character`.`character_id` AS `id`, `ffxiv__character`.`pvp_matches` AS `matches`, `ffxiv__character`.`name`, `current`, `ffxiv__character`.`avatar` AS `icon`, `ffxiv__pvpteam_rank`.`rank`, `ffxiv__pvpteam_rank`.`pvp_rank_id`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE uc__user_to_ff_character.`character_id`=`ffxiv__pvpteam_character`.`character_id`) AS `user_id` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_rank`.`pvp_rank_id`=`ffxiv__pvpteam_character`.`rank_id` LEFT JOIN `ffxiv__character` ON `ffxiv__pvpteam_character`.`character_id`=`ffxiv__character`.`character_id` WHERE `ffxiv__pvpteam_character`.`pvp_id`=:id ORDER BY `ffxiv__pvpteam_character`.`rank_id` , `ffxiv__character`.`name` ', [':id' => $this->id], return: 'all');
        #Clean up the data from unnecessary (technical) clutter
        unset($data['data_center_id'], $data['server_id'], $data['server']);
        return $data;
    }
    
    /**
     * Get PvP team data from Lodestone
     *
     * @param bool $allow_sleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     * @internal
     * @return string|array
     */
    public function getFromLodestone(bool $allow_sleep = false): string|array
    {
        $lodestone = new Lodestone();
        try {
            $data = $lodestone->getPvPTeam($this->id)->getResult();
        } catch (\Throwable $exception) {
            if (\preg_match('/Lodestone has throttled the request/ui', $exception->getMessage()) === 1) {
                if ($allow_sleep) {
                    #Take a pause if we were throttled, and pause is allowed
                    \sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (\preg_match('/Lodestone not available/ui', $exception->getMessage()) !== 1) {
                Errors::error_log($exception, $lodestone->getErrors());
            }
            return 'Failed to get all necessary data for PvPTeam '.$this->id;
        }
        if (empty($data['pvpteams'][$this->id]['data_center']) || empty($data['pvpteams'][$this->id]['members'])) {
            if (!empty($data['pvpteams'][$this->id]['members']) && (int)$data['pvpteams'][$this->id]['members'] === 404) {
                $this->delete();
                return ['404' => true];
            }
            Errors::error_log(new \RuntimeException('Failed to get all necessary data for PvP Team '.$this->id), $lodestone->getErrors());
            return 'Failed to get all necessary data for PvP Team '.$this->id;
        }
        if (empty($data['pvpteams'][$this->id]['crest'][2]) && !empty($data['pvpteams'][$this->id]['crest'][1])) {
            $data['pvpteams'][$this->id]['crest'][2] = $data['pvpteams'][$this->id]['crest'][1];
            $data['pvpteams'][$this->id]['crest'][1] = null;
        }
        $data = $data['pvpteams'][$this->id];
        $data['id'] = $this->id;
        $data['404'] = false;
        unset($data['page_current'], $data['page_total']);
        return $data;
    }
    
    /**
     * Process data from DB
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->name = $from_db['name'];
        $this->dates = [
            'formed' => (empty($from_db['formed']) ? null : \strtotime($from_db['formed'])),
            'registered' => \strtotime($from_db['registered']),
            'updated' => \strtotime($from_db['updated']),
            'deleted' => (empty($from_db['deleted']) ? null : \strtotime($from_db['deleted'])),
        ];
        $this->community = $from_db['community_id'];
        $this->crest = [
            0 => $from_db['crest_part_1'],
            1 => $from_db['crest_part_2'],
            2 => $from_db['crest_part_3'],
        ];
        $this->data_center = $from_db['data_center'];
        $this->old_names = $from_db['old_names'];
        foreach ($from_db['members'] as $key => $member) {
            $from_db['members'][$key]['matches'] = (int)$member['matches'];
        }
        $members = Splitters::splitByKey($from_db['members'], 'current');
        $this->members = $members[1] ?? [];
        $this->past_members = $members[0] ?? [];
    }
    
    /**
     * Function to update the entity
     *
     * @return bool
     */
    protected function updateDB(): bool
    {
        try {
            #Download crest components
            $this->downloadCrestComponents($this->lodestone['crest']);
            #Main query to insert or update a PvP Team
            $queries[] = [
                'INSERT INTO `ffxiv__pvpteam` (`pvp_id`, `name`, `formed`, `registered`, `updated`, `deleted`, `data_center_id`, `community_id`, `crest_part_1`, `crest_part_2`, `crest_part_3`) VALUES (:pvp_id, :name, :formed, CURRENT_TIMESTAMP(6), CURRENT_TIMESTAMP(6), NULL, (SELECT `server_id` FROM `ffxiv__server` WHERE `data_center`=:data_center ORDER BY `server_id` LIMIT 1), :community_id, :crest_part_1, :crest_part_2, :crest_part_3) ON DUPLICATE KEY UPDATE `name`=:name, `formed`=:formed, `updated`=CURRENT_TIMESTAMP(6), `deleted`=NULL, `data_center_id`=(SELECT `server_id` FROM `ffxiv__server` WHERE `data_center`=:data_center ORDER BY `server_id` LIMIT 1), `community_id`=:community_id, `crest_part_1`=:crest_part_1, `crest_part_2`=:crest_part_2, `crest_part_3`=:crest_part_3;',
                [
                    ':pvp_id' => $this->id,
                    ':data_center' => $this->lodestone['data_center'],
                    ':name' => $this->lodestone['name'],
                    ':formed' => [$this->lodestone['formed'], 'datetime'],
                    ':community_id' => [
                        (empty($this->lodestone['community_id']) ? NULL : $this->lodestone['community_id']),
                        (empty($this->lodestone['community_id']) ? 'null' : 'string'),
                    ],
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
                ],
            ];
            #Register PvP Team name if it's not registered already
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__pvpteam_names`(`pvp_id`, `name`) VALUES (:pvp_id, :name);',
                [
                    ':pvp_id' => $this->id,
                    ':name' => $this->lodestone['name'],
                ],
            ];
            #Get members as registered on the tracker
            $track_members = Query::query('SELECT `character_id` FROM `ffxiv__pvpteam_character` WHERE `pvp_id`=:pvp_id AND `current`=1;', [':pvp_id' => $this->id], return: 'column');
            #Process members that left the team
            foreach ($track_members as $member) {
                #Check if member from tracker is present in a Lodestone list
                if (!\array_key_exists('members', $this->lodestone) || !\array_key_exists($member, $this->lodestone['members'])) {
                    #Update status for the character
                    $queries[] = [
                        'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `pvp_id`=:pvp_id AND `character_id`=:character_id;',
                        [
                            ':character_id' => $member,
                            ':pvp_id' => $this->id,
                        ],
                    ];
                }
            }
            #Process Lodestone members
            if (!empty($this->lodestone['members'])) {
                foreach ($this->lodestone['members'] as $member => $details) {
                    $this->charQuickRegister($member, $this->lodestone['members'], $queries);
                    #Link the character to the team
                    $queries[] = [
                        'INSERT INTO `ffxiv__pvpteam_character` (`pvp_id`, `character_id`, `rank_id`, `current`) VALUES (:pvp_id, :character_id, (SELECT `pvp_rank_id` FROM `ffxiv__pvpteam_rank` WHERE `rank`=:rank LIMIT 1), 1) ON DUPLICATE KEY UPDATE `current`=1, `rank_id`=(SELECT `pvp_rank_id` FROM `ffxiv__pvpteam_rank` WHERE `rank`=:rank AND `rank` IS NOT NULL LIMIT 1);',
                        [
                            ':character_id' => $member,
                            ':pvp_id' => $this->id,
                            ':rank' => $details['rank'] ?? 'Member',
                        ],
                    ];
                }
            }
            #Running the queries we've accumulated
            Query::query($queries);
            #Schedule a proper update of any newly added characters
            if (!empty($this->lodestone['members'])) {
                $this->charMassCron($this->lodestone['members']);
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'pvp_id: '.$this->id);
            return false;
        }
    }
    
    /**
     * Delete PvP Team
     * @return bool
     */
    protected function delete(): bool
    {
        try {
            $queries = [];
            #Remove characters from the group
            $queries[] = [
                'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `pvp_id`=:group_id;',
                [':group_id' => $this->id,]
            ];
            #Update PvP Team
            $queries[] = [
                'UPDATE `ffxiv__pvpteam` SET `deleted` = COALESCE(`deleted`, CURRENT_TIMESTAMP(6)), `updated`=CURRENT_TIMESTAMP(6) WHERE `pvp_id` = :id', [':id' => $this->id],
            ];
            return Query::query($queries);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $this->debug);
            return false;
        }
    }
}