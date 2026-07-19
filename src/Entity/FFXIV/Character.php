<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity\FFXIV;

use App\Entity\User;
use App\Enum\LogType;
use App\Security\Security;
use App\Service\Config;
use App\Service\Errors;
use App\Service\Images;
use App\Service\Sanitization;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;
use function is_array;

/**
 * Class representing a FFXIV character
 */
class Character extends AbstractEntity
{
    #Custom properties
    public ?string $avatar_id = '';
    public array $dates = [];
    public array $biology = [];
    public array $location = [];
    public ?string $biography = null;
    public array $title = [];
    public array $grand_company = [];
    public int $pvp = 0;
    public array $groups = [];
    public array $jobs = [];
    public array $achievements = [];
    public array $friends = [];
    public array $following = [];
    public int $achievement_points = 0;
    public array $owned = [];
    
    /**
     * Function to get initial data from DB
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information. Using *, but add name, because otherwise Achievement name overrides Character name, and we do not want that
        $data = Query::query('SELECT *, `ffxiv__character`.`character_id`, `ffxiv__achievement`.`icon` AS `title_icon`, `ffxiv__character`.`name`, `ffxiv__character`.`registered`, `ffxiv__character`.`updated`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id` FROM `ffxiv__character` LEFT JOIN `ffxiv__clan` ON `ffxiv__character`.`clan_id` = `ffxiv__clan`.`clan_id` LEFT JOIN `ffxiv__guardian` ON `ffxiv__character`.`guardian_id` = `ffxiv__guardian`.`guardian_id` LEFT JOIN `ffxiv__nameday` ON `ffxiv__character`.`nameday_id` = `ffxiv__nameday`.`nameday_id` LEFT JOIN `ffxiv__city` ON `ffxiv__character`.`city_id` = `ffxiv__city`.`city_id` LEFT JOIN `ffxiv__server` ON `ffxiv__character`.`server_id` = `ffxiv__server`.`server_id` LEFT JOIN `ffxiv__grandcompany_rank` ON `ffxiv__character`.`gc_rank_id` = `ffxiv__grandcompany_rank`.`gc_rank_id` LEFT JOIN `ffxiv__grandcompany` ON `ffxiv__grandcompany_rank`.`gc_id` = `ffxiv__grandcompany`.`gc_id` LEFT JOIN `ffxiv__achievement` ON `ffxiv__character`.`title_id` = `ffxiv__achievement`.`achievement_id` WHERE `ffxiv__character`.`character_id` = :id;', [':id' => $this->id], return: 'row');
        if (!empty($data['hidden'])) {
            foreach ($data as $key => $value) {
                if (!\in_array($key, ['avatar', 'registered', 'updated', 'deleted', 'hidden', 'name'])) {
                    unset($data[$key]);
                }
            }
        } else {
            #Return empty if nothing was found
            if ($data === []) {
                return [];
            }
            #Get username if character is linked to a user
            if (!empty($data['user_id'])) {
                $data['username'] = Query::query('SELECT `username` FROM `uc__users` WHERE `user_id`=:user_id;', [':user_id' => $data['user_id']], return: 'value');
            } else {
                $data['username'] = null;
            }
            #Get jobs
            $data['jobs'] = Query::query('SELECT `name`, `level`, `last_change` FROM `ffxiv__character_jobs` LEFT JOIN `ffxiv__jobs` ON `ffxiv__character_jobs`.`job_id`=`ffxiv__jobs`.`job_id` WHERE `ffxiv__character_jobs`.`character_id`=:id ORDER BY `name`;', [':id' => $this->id], return: 'all');
            #Get old names. For now, returning only the count due to cases of bullying, when the old names are learnt. They are still being collected, though, for statistical purposes.
            $data['old_names'] = Query::query('SELECT `name` FROM `ffxiv__character_names` WHERE `character_id`=:id AND `name`!=:name', [':id' => $this->id, ':name' => $data['name']], return: 'column');
            #Get previous known incarnations (combination of gender and race/clan)
            $data['incarnations'] = Query::query('SELECT `gender`, `ffxiv__clan`.`race`, `ffxiv__clan`.`clan` FROM `ffxiv__character_clans` LEFT JOIN `ffxiv__clan` ON `ffxiv__character_clans`.`clan_id` = `ffxiv__clan`.`clan_id` WHERE `ffxiv__character_clans`.`character_id`=:id AND !(`ffxiv__character_clans`.`clan_id`=:clan_id AND `ffxiv__character_clans`.`gender`=:gender) ORDER BY `gender` , `race` , `clan` ', [':id' => $this->id, ':clan_id' => $data['clan_id'], ':gender' => $data['gender']], return: 'all');
            #Get old servers
            $data['servers'] = Query::query('SELECT `ffxiv__server`.`data_center`, `ffxiv__server`.`server` FROM `ffxiv__character_servers` LEFT JOIN `ffxiv__server` ON `ffxiv__server`.`server_id`=`ffxiv__character_servers`.`server_id` WHERE `ffxiv__character_servers`.`character_id`=:id AND `ffxiv__character_servers`.`server_id` != :server_id ORDER BY `data_center` , `server` ', [':id' => $this->id, ':server_id' => $data['server_id']], return: 'all');
            #Get achievements
            $data['achievements'] = Query::query('SELECT \'achievement\' AS `type`, `achievement_id` AS `id`, `category`, `subcategory`,`name`, `points`, (SELECT `time` FROM `ffxiv__character_achievement` WHERE `character_id` = :id AND `ffxiv__character_achievement`.`achievement_id`=`ffxiv__achievement`.`achievement_id`) AS `time`, `icon` FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`category` IS NOT NULL AND `achievement_id` IN (SELECT `achievement_id` FROM `ffxiv__character_achievement` WHERE `character_id` = :id) ORDER BY `time` DESC, `name`;', [':id' => $this->id], return: 'all');
            #Get friends
            $data['friends'] = Query::query('SELECT \'character\' AS `type`, `outer`.`friend` AS `id`, `ffxiv__character`.`name` AS `name`, `avatar` AS `icon`, `current`, EXISTS(SELECT `character_id` FROM `ffxiv__character_friends` WHERE `ffxiv__character_friends`.`character_id`=`outer`.`friend` AND `ffxiv__character_friends`.`friend`=`outer`.`character_id` AND `ffxiv__character_friends`.`current`=`outer`.`current`) AS `mutual`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id`, NULL AS `rank_id` FROM `ffxiv__character_friends` AS `outer` LEFT JOIN `ffxiv__character` ON `outer`.`friend`=`ffxiv__character`.`character_id` WHERE `outer`.`character_id`=:id', [':id' => $this->id], return: 'all');
            #Get characters being followed by the character
            $data['following'] = Query::query('SELECT \'character\' AS `type`, `outer`.`following` AS `id`, `ffxiv__character`.`name` AS `name`, `avatar` AS `icon`, `current`, EXISTS(SELECT `character_id` FROM `ffxiv__character_following` WHERE `ffxiv__character_following`.`character_id`=`outer`.`following` AND `ffxiv__character_following`.`following`=`outer`.`character_id` AND `ffxiv__character_following`.`current`=`outer`.`current`) AS `mutual`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id`, NULL AS `rank_id` FROM `ffxiv__character_following` AS `outer` LEFT JOIN `ffxiv__character` ON `outer`.`following`=`ffxiv__character`.`character_id` WHERE `outer`.`character_id`=:id', [':id' => $this->id], return: 'all');
            #Get affiliated groups' details
            $data['groups'] = AbstractEntity::cleanCrestResults(Query::query(
            /** @lang SQL */ '(SELECT \'freecompany\' AS `type`, 0 AS `crossworld`, `ffxiv__freecompany_character`.`fc_id` AS `id`, `ffxiv__freecompany`.`name` AS `name`, `current`, `ffxiv__freecompany_character`.`rank_id`, `ffxiv__freecompany_rank`.`rankname` AS `rank`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `gc_id` FROM `ffxiv__freecompany_character` LEFT JOIN `ffxiv__freecompany` ON `ffxiv__freecompany_character`.`fc_id`=`ffxiv__freecompany`.`fc_id` LEFT JOIN `ffxiv__freecompany_rank` ON `ffxiv__freecompany_rank`.`fc_id`=`ffxiv__freecompany`.`fc_id` AND `ffxiv__freecompany_character`.`rank_id`=`ffxiv__freecompany_rank`.`rank_id` WHERE `character_id`=:id)
            UNION ALL
            (SELECT \'linkshell\' AS `type`, `crossworld`, `ffxiv__linkshell_character`.`ls_id` AS `id`, `ffxiv__linkshell`.`name` AS `name`, `current`, `ffxiv__linkshell_character`.`rank_id`, `ffxiv__linkshell_rank`.`rank` AS `rank`, NULL AS `crest_part_1`, NULL AS `crest_part_2`, NULL AS `crest_part_3`, NULL AS `gc_id` FROM `ffxiv__linkshell_character` LEFT JOIN `ffxiv__linkshell` ON `ffxiv__linkshell_character`.`ls_id`=`ffxiv__linkshell`.`ls_id` LEFT JOIN `ffxiv__linkshell_rank` ON `ffxiv__linkshell_character`.`rank_id`=`ffxiv__linkshell_rank`.`ls_rank_id` WHERE `character_id`=:id)
            UNION ALL
            (SELECT \'pvpteam\' AS `type`, 1 AS `crossworld`, `ffxiv__pvpteam_character`.`pvp_id` AS `id`, `ffxiv__pvpteam`.`name` AS `name`, `current`, `ffxiv__pvpteam_character`.`rank_id`, `ffxiv__pvpteam_rank`.`rank` AS `rank`, `crest_part_1`, `crest_part_2`, `crest_part_3`, NULL AS `gc_id` FROM `ffxiv__pvpteam_character` LEFT JOIN `ffxiv__pvpteam` ON `ffxiv__pvpteam_character`.`pvp_id`=`ffxiv__pvpteam`.`pvp_id` LEFT JOIN `ffxiv__pvpteam_rank` ON `ffxiv__pvpteam_character`.`rank_id`=`ffxiv__pvpteam_rank`.`pvp_rank_id` WHERE `character_id`=:id)
            ORDER BY `current` DESC, `name`;',
                [':id' => $this->id], return: 'all'
            ));
            #Clean up the data from unnecessary (technical) clutter
            unset($data['clan_id'], $data['nameday_id'], $data['achievement_id'], $data['category'], $data['subcategory'], $data['how_to'], $data['points'], $data['icon'], $data['item'], $data['item_icon'], $data['item_id'], $data['server_id']);
        }
        return $data;
    }
    
    /**
     * Get character data from Lodestone
     *
     * @param bool $allow_sleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     * @internal
     * @return string|array
     */
    public function getFromLodestone(bool $allow_sleep = false): string|array
    {
        $lodestone = (new Lodestone());
        try {
            $data = $lodestone->getCharacter($this->id)->getResult();
        } catch (\Throwable $exception) {
            if (\preg_match('/Lodestone has throttled the request/ui', $exception->getMessage()) === 1) {
                if ($allow_sleep) {
                    #Take a pause if we were throttled, and pause is allowed
                    \sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (\preg_match('/Lodestone not available/ui', $exception->getMessage()) === 1) {
                return 'Lodestone not available';
            }
            Errors::error_log($exception, ['last_error' => $lodestone->getLastError(), 'all_errors' => $lodestone->getErrors()]);
            return 'Failed to get all necessary data for Character '.$this->id;
        }
        #Check if the character is private
        $private = false;
        if (!empty($data['characters'][$this->id]) && is_array($data['characters'][$this->id]) && \array_key_exists('private', $data['characters'][$this->id]) && $data['characters'][$this->id]['private'] === true) {
            $this->markPrivate();
            $private = true;
        }
        #Check for possible errors
        if (!$private && empty($data['characters'][$this->id]['server'])) {
            if (!empty($data['characters'][$this->id]) && (int)$data['characters'][$this->id] === 404) {
                $this->delete();
                return ['404' => true];
            }
            Errors::error_log(new \RuntimeException('Failed to get all necessary data for Character '.$this->id), ['last_error' => $lodestone->getLastError(), 'all_errors' => $lodestone->getErrors()]);
            return 'Failed to get all necessary data for Character '.$this->id;
        }
        #Try to get jobs and achievements now, that we got basic information, and there were no issues with it.
        try {
            $data = $lodestone->getCharacterJobs($this->id)
                ->getCharacterFriends($this->id, 0)
                ->getCharacterFollowing($this->id, 0)
                ->getCharacterAchievements($this->id, false, 0, false, false, true)
                ->getResult();
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
        }
        unset($data['characters'][$this->id]['page_current'], $data['characters'][$this->id]['page_total'], $data['characters'][$this->id]['members_count']);
        $data = $data['characters'][$this->id];
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
        $this->avatar_id = $from_db['avatar'];
        $this->dates = [
            'registered' => \strtotime($from_db['registered']),
            'updated' => \strtotime($from_db['updated']),
            'hidden' => (empty($from_db['hidden']) ? null : \strtotime($from_db['hidden'])),
            'hidden_achievements' => (empty($from_db['hidden_achievements']) ? null : \strtotime($from_db['hidden_achievements'])),
            'hidden_friends' => (empty($from_db['hidden_friends']) ? null : \strtotime($from_db['hidden_friends'])),
            'hidden_following' => (empty($from_db['hidden_following']) ? null : \strtotime($from_db['hidden_following'])),
            'deleted' => (empty($from_db['deleted']) ? null : \strtotime($from_db['deleted'])),
        ];
        $this->biology = [
            'gender' => (int)($from_db['gender'] ?? 0),
            'race' => $from_db['race'] ?? null,
            'clan' => $from_db['clan'] ?? null,
            'nameday' => $from_db['nameday'] ?? null,
            'guardian' => $from_db['guardian'] ?? null,
            'guardian_id' => $from_db['guardian_id'] ?? null,
            'incarnations' => $from_db['incarnations'] ?? null,
            'old_names' => $from_db['old_names'] ?? [],
        ];
        $this->location = [
            'data_center' => $from_db['data_center'] ?? null,
            'server' => $from_db['server'] ?? null,
            'region' => $from_db['region'] ?? null,
            'city' => $from_db['city'] ?? null,
            'city_id' => $from_db['city_id'] ?? null,
            'previous_servers' => $from_db['servers'] ?? [],
        ];
        $this->biography = $from_db['biography'] ?? null;
        if ($this->biography) {
            $this->biography = Sanitization::sanitizeHTML($this->biography);
        }
        $this->title = [
            'title' => $from_db['title'] ?? null,
            'icon' => $from_db['title_icon'] ?? null,
            'id' => $from_db['title_id'] ?? null,
        ];
        $this->grand_company = [
            'name' => $from_db['gc_name'] ?? null,
            'rank' => $from_db['gc_rank'] ?? null,
            'gc_id' => $from_db['gc_id'] ?? null,
            'gc_rank_id' => $from_db['gc_rank_id'] ?? null,
        ];
        $this->pvp = (int)($from_db['pvp_matches'] ?? 0);
        $this->groups = $from_db['groups'] ?? [];
        $this->friends = $from_db['friends'] ?? [];
        if ($this->dates['hidden_friends'] !== null) {
            $this->friends = [];
        }
        $this->following = $from_db['following'] ?? [];
        if ($this->dates['hidden_following'] !== null) {
            $this->following = [];
        }
        $this->owned = [
            'id' => $from_db['user_id'] ?? null,
            'name' => $from_db['username'] ?? null
        ];
        foreach ($this->groups as $key => $group) {
            $this->groups[$key]['current'] = (bool)$group['current'];
        }
        foreach ($this->friends as $key => $character) {
            $this->friends[$key]['current'] = (bool)$character['current'];
        }
        foreach ($this->following as $key => $character) {
            $this->following[$key]['current'] = (bool)$character['current'];
        }
        $this->achievements = $from_db['achievements'] ?? [];
        if ($this->dates['hidden_achievements'] !== null) {
            $this->achievements = [];
        }
        foreach ($this->achievements as $key => $achievement) {
            $this->achievements[$key]['time'] = (empty($achievement['time']) ? null : \strtotime($achievement['time']));
        }
        $this->achievement_points = $from_db['achievement_points'] ?? 0;
        $this->jobs = $from_db['jobs'] ?? [];
    }
    
    /**
     * Function to update the entity
     *
     * @return bool
     */
    protected function updateDB(): bool
    {
        try {
            #Get time of the last update for the character if it exists on the tracker
            $updated = Query::query('SELECT `updated` FROM `ffxiv__character` WHERE `character_id`=:character_id', [':character_id' => $this->id], return: 'value');
            #If a character on Lodestone is not registered in Free Company or PvP Team, add their IDs as NULL for consistency
            if (empty($this->lodestone['free_company']['id'])) {
                $this->lodestone['free_company']['id'] = NULL;
                $this->lodestone['free_company']['registered'] = false;
            } else {
                $this->lodestone['free_company']['registered'] = Query::query('SELECT `fc_id` FROM `ffxiv__freecompany` WHERE `fc_id` = :id', [':id' => $this->lodestone['free_company']['id']], return: 'check');
            }
            if (empty($this->lodestone['pvp']['id'])) {
                $this->lodestone['pvp']['id'] = NULL;
                $this->lodestone['pvp']['registered'] = false;
            } else {
                $this->lodestone['pvp']['registered'] = Query::query('SELECT `pvp_id` FROM `ffxiv__pvpteam` WHERE `pvp_id` = :id', [':id' => $this->lodestone['pvp']['id']], return: 'check');
            }
            #Insert Free Companies and PvP Team if they are not registered
            if ($this->lodestone['free_company']['id'] !== NULL && $this->lodestone['free_company']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__freecompany` (`fc_id`, `name`, `server_id`, `updated`) VALUES (:fc_id, :fc_name, (SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, CURRENT_TIMESTAMP(6)));',
                    [
                        ':fc_id' => $this->lodestone['free_company']['id'],
                        ':fc_name' => $this->lodestone['free_company']['name'],
                        ':server' => $this->lodestone['server'],
                    ],
                ];
            }
            if ($this->lodestone['pvp']['id'] !== NULL && $this->lodestone['pvp']['registered'] === false) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__pvpteam` (`pvp_id`, `name`, `data_center_id`, `updated`) VALUES (:pvp_id, :pvp_name, (SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), TIMESTAMPADD(SECOND, -3600, CURRENT_TIMESTAMP(6)));',
                    [
                        ':pvp_id' => $this->lodestone['pvp']['id'],
                        ':pvp_name' => $this->lodestone['pvp']['name'],
                        ':server' => $this->lodestone['server'],
                    ],
                ];
            }
            #Reduce the number of <br>s in biography
            $this->lodestone['bio'] = Sanitization::sanitizeHTML($this->lodestone['bio'] ?? '');
            if (($this->lodestone['bio'] === '-')) {
                $this->lodestone['bio'] = null;
            }
            #Get total achievements points. Using foreach for speed
            $achievement_points = 0;
            $hidden_achievements = false;
            if (!empty($this->lodestone['achievements']) && is_array($this->lodestone['achievements'])) {
                if (\array_key_exists('private', $this->lodestone['achievements']) && $this->lodestone['achievements']['private']) {
                    $hidden_achievements = true;
                } else {
                    unset($this->lodestone['achievements']['private']);
                    foreach ($this->lodestone['achievements'] as $item) {
                        $achievement_points += (int)$item['points'];
                    }
                }
            }
            #Check friends/following lists
            $hidden_friends = false;
            $hidden_following = false;
            if (!empty($this->lodestone['friends']) && is_array($this->lodestone['friends'])) {
                if (\array_key_exists('private', $this->lodestone['friends']) && $this->lodestone['friends']['private']) {
                    $hidden_friends = true;
                } else {
                    unset($this->lodestone['friends']['private']);
                }
            }
            if (!empty($this->lodestone['following']) && is_array($this->lodestone['following'])) {
                if (\array_key_exists('private', $this->lodestone['following']) && $this->lodestone['following']['private']) {
                    $hidden_following = true;
                } else {
                    unset($this->lodestone['following']['private']);
                }
            }
            #Main query to insert or update a character
            $queries[] = [
                'INSERT INTO `ffxiv__character`(
                    `character_id`, `server_id`, `name`, `registered`, `updated`, `hidden`, `hidden_achievements`, `hidden_friends`, `hidden_following`, `deleted`, `biography`, `title_id`, `avatar`, `clan_id`, `gender`, `nameday_id`, `guardian_id`, `city_id`, `gc_rank_id`, `pvp_matches`, `achievement_points`
                )
                VALUES (
                    :character_id, (SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), :name, CURRENT_TIMESTAMP(6), CURRENT_TIMESTAMP(6), NULL, :hidden_achievements, :hidden_friends, :hidden_following, NULL, :biography, (SELECT `achievement_id` as `title_id` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), :avatar, (SELECT `clan_id` FROM `ffxiv__clan` WHERE `clan`=:clan), :gender, (SELECT `nameday_id` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), (SELECT `guardian_id` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), (SELECT `city_id` FROM `ffxiv__city` WHERE `city`=:city), `gc_rank_id` = (SELECT `gc_rank_id` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank`=:gcRank ORDER BY `gc_rank_id` LIMIT 1), 0, :achievement_points
                )
                ON DUPLICATE KEY UPDATE
                    `server_id`=(SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server), `name`=:name, `updated`=CURRENT_TIMESTAMP(6), `hidden`=NULL, `hidden_achievements`=:hidden_achievements, `hidden_friends`=:hidden_friends, `hidden_following`=:hidden_following, `deleted`=NULL, `biography`=:biography, `title_id`=(SELECT `achievement_id` as `title_id` FROM `ffxiv__achievement` WHERE `title` IS NOT NULL AND `title`=:title LIMIT 1), `avatar`=:avatar, `clan_id`=(SELECT `clan_id` FROM `ffxiv__clan` WHERE `clan`=:clan), `gender`=:gender, `nameday_id`=(SELECT `nameday_id` FROM `ffxiv__nameday` WHERE `nameday`=:nameday), `guardian_id`=(SELECT `guardian_id` FROM `ffxiv__guardian` WHERE `guardian`=:guardian), `city_id`=(SELECT `city_id` FROM `ffxiv__city` WHERE `city`=:city), `gc_rank_id`=(SELECT `gc_rank_id` FROM `ffxiv__grandcompany_rank` WHERE `gc_rank` IS NOT NULL AND `gc_rank`=:gcRank ORDER BY `gc_rank_id` LIMIT 1), `achievement_points`=:achievement_points;',
                [
                    ':character_id' => $this->id,
                    ':server' => $this->lodestone['server'],
                    ':name' => $this->lodestone['name'],
                    ':avatar' => \str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg', 'c0.jpg'], '', $this->lodestone['avatar']),
                    ':biography' => [
                        (empty($this->lodestone['bio']) ? NULL : $this->lodestone['bio']),
                        (empty($this->lodestone['bio']) ? 'null' : 'string'),
                    ],
                    ':title' => (empty($this->lodestone['title']) ? '' : $this->lodestone['title']),
                    ':clan' => $this->lodestone['clan'],
                    ':gender' => ($this->lodestone['gender'] === 'male' ? '1' : '0'),
                    ':nameday' => $this->lodestone['nameday'],
                    ':guardian' => $this->lodestone['guardian']['name'],
                    ':city' => $this->lodestone['city']['name'],
                    ':gcRank' => (empty($this->lodestone['grand_company']['rank']) ? '' : $this->lodestone['grand_company']['rank']),
                    ':achievement_points' => [$achievement_points, 'int'],
                    ':hidden_achievements' => [$hidden_achievements ? \microtime(true) : null, $hidden_achievements ? 'datetime' : 'null'],
                    ':hidden_friends' => [$hidden_friends ? \microtime(true) : null, $hidden_friends ? 'datetime' : 'null'],
                    ':hidden_following' => [$hidden_following ? \microtime(true) : null, $hidden_following ? 'datetime' : 'null'],
                ],
            ];
            #Update levels. Doing this in a cycle since columns can vary. This can reduce performance, but so far this is the best idea I have to make it as automated as possible
            if (!empty($this->lodestone['jobs'])) {
                foreach ($this->lodestone['jobs'] as $job => $level) {
                    #Insert job, in case it's missing
                    $queries[] = ['INSERT IGNORE INTO `ffxiv__jobs` (`name`) VALUES (:job);', [':job' => $job]];
                    #Update level, but only if it's more than 0
                    if ((int)$level['level'] > 0) {
                        $queries[] = [
                            'INSERT INTO `ffxiv__character_jobs` (`character_id`, `job_id`, `level`, `last_change`) VALUES (:character_id, (SELECT `job_id` FROM `ffxiv__jobs` WHERE `name`=:jobname), :level, CURRENT_TIMESTAMP(6)) ON DUPLICATE KEY UPDATE `level`=:level, `last_change`=IF(`level`=:level, `last_change`, CURRENT_TIMESTAMP(6));',
                            [
                                ':character_id' => $this->id,
                                ':jobname' => $job,
                                ':level' => [(int)$level['level'], 'int'],
                            ],
                        ];
                    }
                }
            }
            $this->insertServerAndName($queries);
            #Insert race, clan and sex combination if it has not been inserted yet
            if (!empty($this->lodestone['clan'])) {
                $queries[] = [
                    'INSERT IGNORE INTO `ffxiv__character_clans`(`character_id`, `gender`, `clan_id`) VALUES (:character_id, :gender, (SELECT `clan_id` FROM `ffxiv__clan` WHERE `clan`=:clan));',
                    [
                        ':character_id' => $this->id,
                        ':gender' => ($this->lodestone['gender'] === 'male' ? '1' : '0'),
                        ':clan' => $this->lodestone['clan'],
                    ],
                ];
            }
            #Update company information
            $queries[] = [
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `character_id`=:character_id AND `fc_id` '.(empty($this->lodestone['free_company']['id']) ? 'IS NOT ' : '!= ').' :fc_id;',
                [
                    ':character_id' => $this->id,
                    ':fc_id' => [
                        $this->lodestone['free_company']['id'],
                        (empty($this->lodestone['free_company']['id']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Update PvP Team information
            $queries[] = [
                'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `character_id`=:character_id AND `pvp_id` '.(empty($this->lodestone['pvp']['id']) ? 'IS NOT ' : '!= ').' :pvp_id;',
                [
                    ':character_id' => $this->id,
                    ':pvp_id' => [
                        $this->lodestone['pvp']['id'],
                        (empty($this->lodestone['pvp']['id']) ? 'null' : 'string'),
                    ],
                ],
            ];
            #Achievements
            if (!$hidden_achievements && !empty($this->lodestone['achievements']) && is_array($this->lodestone['achievements'])) {
                foreach ($this->lodestone['achievements'] as $achievement_id => $item) {
                    $icon = self::removeLodestoneDomain($item['icon']);
                    #Download the icon if it's not already present
                    if (\is_file(\str_replace('.png', '.webp', Config::$icons.$icon))) {
                        $webp = true;
                    } else {
                        $webp = Images::download($item['icon'], Config::$icons.$icon);
                    }
                    if ($webp) {
                        $icon = \str_replace('.png', '.webp', $icon);
                        $queries[] = [
                            'INSERT INTO `ffxiv__achievement` SET `achievement_id`=:achievement_id, `name`=:name, `icon`=:icon, `points`=:points ON DUPLICATE KEY UPDATE `updated`=`updated`, `name`=:name, `icon`=:icon, `points`=:points;',
                            [
                                ':achievement_id' => $achievement_id,
                                ':name' => $item['name'],
                                ':icon' => $icon,
                                ':points' => $item['points'],
                            ],
                        ];
                        $queries[] = [
                            'INSERT INTO `ffxiv__character_achievement` SET `character_id`=:character_id, `achievement_id`=:achievement_id, `time`=:time ON DUPLICATE KEY UPDATE `time`=:time;',
                            [
                                ':character_id' => $this->id,
                                ':achievement_id' => $achievement_id,
                                ':time' => [$item['time'], 'datetime'],
                            ],
                        ];
                        #If the achievement is new since the last check, or if this is the first time the character is being processed, add and count the achievement
                        if (!empty($updated) && (int)$item['time'] > \strtotime($updated)) {
                            $queries[] = [
                                'UPDATE `ffxiv__achievement` SET `earned_by`=`earned_by`+1 WHERE `achievement_id`=:achievement_id;',
                                [
                                    ':achievement_id' => $achievement_id,
                                ],
                            ];
                        }
                    }
                }
            }
            #Process friends/following
            if (!$hidden_friends && !empty($this->lodestone['friends']) && is_array($this->lodestone['friends'])) {
                #Get current friends from tracker
                $friends_list = Query::query('SELECT `character_id` FROM `ffxiv__character_friends` WHERE `character_id`=:character_id AND `current`=1;', [':character_id' => $this->id], return: 'column');
                #Update status of removed friends
                foreach ($friends_list as $friend) {
                    if (!\array_key_exists($friend, $this->lodestone['friends'])) {
                        $queries[] = [
                            'UPDATE `ffxiv__character_friends` SET `current`=0 WHERE `character_id`=:character_id AND `friend`=:friend_id;',
                            [
                                ':friend_id' => $friend,
                                ':character_id' => $this->id,
                            ],
                        ];
                    }
                }
                foreach ($this->lodestone['friends'] as $friend_id => $character_data) {
                    $this->charQuickRegister($friend_id, $this->lodestone['friends'], $queries);
                    $queries[] = [
                        'INSERT INTO `ffxiv__character_friends` (`character_id`, `friend`, `current`) VALUES (:character_id, :friend, 1) ON DUPLICATE KEY UPDATE `current`=1;',
                        [
                            ':character_id' => $this->id,
                            ':friend' => $friend_id,
                        ],
                    ];
                }
            }
            if (!$hidden_following && !empty($this->lodestone['following']) && is_array($this->lodestone['following'])) {
                #Get current friends from tracker
                $following_list = Query::query('SELECT `character_id` FROM `ffxiv__character_following` WHERE `character_id`=:character_id AND `current`=1;', [':character_id' => $this->id], return: 'column');
                #Update status of removed friends
                foreach ($following_list as $following) {
                    if (!\array_key_exists($following, $this->lodestone['following'])) {
                        $queries[] = [
                            'UPDATE `ffxiv__character_following` SET `current`=0 WHERE `character_id`=:character_id AND `following`=:following_id;',
                            [
                                ':following_id' => $following,
                                ':character_id' => $this->id,
                            ],
                        ];
                    }
                }
                foreach ($this->lodestone['following'] as $following_id => $character_data) {
                    $this->charQuickRegister($following_id, $this->lodestone['following'], $queries);
                    $queries[] = [
                        'INSERT INTO `ffxiv__character_following` (`character_id`, `following`, `current`) VALUES (:character_id, :following, 1) ON DUPLICATE KEY UPDATE `current`=1;',
                        [
                            ':character_id' => $this->id,
                            ':following' => $following_id,
                        ],
                    ];
                }
            }
            Query::query($queries);
            #Schedule the proper update of any newly added characters
            if (!$hidden_friends && !empty($this->lodestone['friends'])) {
                $this->charMassCron($this->lodestone['friends']);
            }
            if (!$hidden_following && !empty($this->lodestone['following'])) {
                $this->charMassCron($this->lodestone['following']);
            }
            #Register the Free Company update if a change was detected
            if (!empty($this->lodestone['free_company']['id']) && !Query::query('SELECT `character_id` FROM `ffxiv__freecompany_character` WHERE `character_id`=:character_id AND `fc_id`=:fcID;', [':character_id' => $this->id, ':fcID' => $this->lodestone['free_company']['id']], return: 'check') && new FreeCompany($this->lodestone['free_company']['id'])->update() !== true) {
                try {
                    new TaskInstance()->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$this->lodestone['free_company']['id'], 'freecompany'], 'message' => 'Updating free company with ID '.$this->lodestone['free_company']['id'], 'priority' => 2])->add();
                } catch (\Throwable) {
                    #Do nothing, not as critical
                }
            }
            #Register PvP Team update if a change was detected
            if (!empty($this->lodestone['pvp']['id']) && !Query::query('SELECT `character_id` FROM `ffxiv__pvpteam_character` WHERE `character_id`=:character_id AND `pvp_id`=:pvpID;', [':character_id' => $this->id, ':pvpID' => $this->lodestone['pvp']['id']], return: 'check') && new PvPTeam($this->lodestone['pvp']['id'])->update() !== true) {
                try {
                    new TaskInstance()->settingsFromArray(['task' => 'ff_update_entity', 'arguments' => [(string)$this->lodestone['pvp']['id'], 'pvpteam'], 'message' => 'Updating PvP team with ID '.$this->lodestone['pvp']['id'], 'priority' => 2])->add();
                } catch (\Throwable) {
                    #Do nothing, not as critical
                }
            }
            #Check if a character is linked to a user
            $character = Query::query('SELECT `character_id`, `user_id` FROM `uc__user_to_ff_character` WHERE `character_id`=:id;', [':id' => $this->id], return: 'row');
            if (!empty($character['user_id'])) {
                #Download avatar
                new User($character['user_id'])->addAvatar(false, $this->lodestone['avatar'], (int)$this->id);
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'character_id: '.$this->id);
            return false;
        }
    }
    
    /**
     * Function to mark character as private
     * @return bool
     */
    protected function markPrivate(): bool
    {
        try {
            #In some cases, we may have a server, name and avatar
            if (!empty($this->lodestone['server']) && !empty($this->lodestone['name']) && !empty($this->lodestone['avatar'])) {
                $queries = [];
                $queries[] = [
                    'UPDATE `ffxiv__character` SET `hidden` = COALESCE(`hidden`, CURRENT_TIMESTAMP(6)), `updated`=CURRENT_TIMESTAMP(6) WHERE `character_id` = :character_id',
                    [
                        ':character_id' => $this->id,
                        ':server' => $this->lodestone['server'],
                        ':name' => $this->lodestone['name'],
                        ':avatar' => \str_replace(['https://img2.finalfantasyxiv.com/f/', 'c0_96x96.jpg', 'c0.jpg'], '', $this->lodestone['avatar']),
                    ],
                ];
                $this->insertServerAndName($queries);
                return Query::query($queries);
            }
            $result = Query::query(
                'UPDATE `ffxiv__character` SET `hidden` = COALESCE(`hidden`, CURRENT_TIMESTAMP(6)), `updated`=CURRENT_TIMESTAMP(6) WHERE `character_id` = :character_id',
                [':character_id' => $this->id],
            );
            return $result;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $this->debug);
            return false;
        }
    }
    
    /**
     * Extracted function to update server and name of the character
     * @param array $queries
     *
     * @return void
     */
    private function insertServerAndName(array &$queries): void
    {
        #Insert server, if it has not been inserted yet. If the server is registered at all.
        if (Query::query('SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server;', [':server' => $this->lodestone['server']], return: 'check')) {
            $queries[] = [
                'INSERT IGNORE INTO `ffxiv__character_servers`(`character_id`, `server_id`) VALUES (:character_id, (SELECT `server_id` FROM `ffxiv__server` WHERE `server`=:server));',
                [
                    ':character_id' => $this->id,
                    ':server' => $this->lodestone['server'],
                ],
            ];
        }
        #Insert a name if it has not been inserted yet
        $queries[] = [
            'INSERT IGNORE INTO `ffxiv__character_names`(`character_id`, `name`) VALUES (:character_id, :name);',
            [
                ':character_id' => $this->id,
                ':name' => $this->lodestone['name'],
            ],
        ];
    }
    
    /**
     * Function to update the entity
     *
     * @param bool $hard Whether to do hard removal
     *
     * @return bool
     */
    protected function delete(bool $hard = false): bool
    {
        if ($hard) {
            $check = $this->getFromLodestone();
            if ($check !== ['404' => true]) {
                return false;
            }
            $queries = [
                #Remove from Free Company
                'DELETE FROM `ffxiv__freecompany_character` WHERE `character_id`=:character_id;',
                #Remove from PvP Team
                'DELETE FROM `ffxiv__pvpteam_character` WHERE `character_id`=:character_id;',
                #Remove from Linkshells
                'DELETE FROM `ffxiv__linkshell_character` WHERE `character_id`=:character_id;',
                #Remove from character lists
                'DELETE FROM `ffxiv__character_friends` WHERE `character_id`=:character_id OR `friend`=:character_id;',
                #Remove from following list
                'DELETE FROM `ffxiv__character_following` WHERE `character_id`=:character_id OR `following`=:character_id;',
                #Remove achievements
                'DELETE FROM `ffxiv__character_achievement` WHERE `character_id`=:character_id;',
                #Remove past incarnations
                'DELETE FROM `ffxiv__character_clans` WHERE `character_id`=:character_id;',
                #Remove jobs
                'DELETE FROM `ffxiv__character_jobs` WHERE `character_id`=:character_id;',
                #Remove name history
                'DELETE FROM `ffxiv__character_names` WHERE `character_id`=:character_id;',
                #Remove server history
                'DELETE FROM `ffxiv__character_servers` WHERE `character_id`=:character_id;',
                #Remove linked character
                'DELETE FROM `uc__user_to_ff_character` WHERE `character_id` = :character_id;',
                #Nullify the avatars
                'UPDATE `uc__avatars` SET `character_id`=NULL WHERE `character_id` = :character_id;',
                #Delete character
                'DELETE FROM `ffxiv__character` WHERE `character_id` = :character_id;',
            ];
        } else {
            $queries = [
                #Remove from Free Company
                'UPDATE `ffxiv__freecompany_character` SET `current`=0 WHERE `character_id`=:character_id;',
                #Remove from PvP Team
                'UPDATE `ffxiv__pvpteam_character` SET `current`=0 WHERE `character_id`=:character_id;',
                #Remove from Linkshells
                'UPDATE `ffxiv__linkshell_character` SET `current`=0 WHERE `character_id`=:character_id;',
                #Remove from character lists
                'UPDATE `ffxiv__character_friends` SET `current`=0 WHERE `character_id`=:character_id OR `friend`=:character_id;',
                #Remove from following list
                'UPDATE `ffxiv__character_following` SET `current`=0 WHERE `character_id`=:character_id OR `following`=:character_id;',
                #Update character
                'UPDATE `ffxiv__character` SET `deleted` = COALESCE(`deleted`, CURRENT_TIMESTAMP(6)), `updated`=CURRENT_TIMESTAMP(6) WHERE `character_id` = :character_id;',
            ];
        }
        try {
            return Query::query($queries, [':character_id' => $this->id]);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $this->debug);
            return false;
        }
    }
    
    /**
     * Link user to character
     *
     * @return array
     * @internal
     */
    public function linkUser(): array
    {
        try {
            #Check if a character exists and is linked already
            $character = Query::query('SELECT `character_id`, `user_id` FROM `uc__user_to_ff_character` WHERE `character_id`=:id;', [':id' => $this->id], return: 'row');
            if ($character !== [] && $character['user_id']) {
                return ['http_error' => 409, 'reason' => 'Character already linked'];
            }
            #Register or update the character
            $this->update();
            if (!empty($this->lodestone['id'])) {
                #Something went wrong with getting data
                if (!empty($this->lodestone['404'])) {
                    return ['http_error' => 400, 'reason' => 'No character found with id `'.$this->id.'`'];
                }
                return ['http_error' => 500, 'reason' => 'Failed to get fresh data for character with id `'.$this->id.'`'];
            }
            #Check if biography is set
            if (empty($this->lodestone['bio'])) {
                return ['http_error' => 424, 'reason' => 'No biography found for character with id `'.$this->id.'`'];
            }
            #Check if the biography contains the respected text
            $token = \preg_replace('/(.*)(fftracker:([a-z\d]{64}))(.*)/uis', '$3', $this->lodestone['bio']);
            if (empty($token)) {
                return ['http_error' => 424, 'reason' => 'No tracker token found for character with id `'.$this->id.'`'];
            }
            #Check if the ID of the current user is the same as the user who has this token
            if (!Query::query('SELECT `user_id` FROM `uc__users` WHERE `user_id`=:user_id AND `ff_token`=:token;', [':user_id' => $_SESSION['user_id'], ':token' => $token], return: 'check')) {
                return ['http_error' => 403, 'reason' => 'Wrong token or user provided'];
            }
            #Link character to user
            $result = Query::query([
                'INSERT IGNORE INTO `uc__user_to_ff_character` (`user_id`, `character_id`) VALUES (:user_id, :character_id);', [':user_id' => $_SESSION['user_id'], ':character_id' => $this->id],
                'INSERT IGNORE INTO `uc__user_to_group` (`user_id`, `group_id`) VALUES (:user_id, :group_id);', [':user_id' => $_SESSION['user_id'], ':group_id' => [Config::GROUP_IDS['Linked to FF'], 'int']],
            ]);
            Security::log(LogType::UserDetailsChanged->value, 'Attempted to link FFXIV character', ['id' => $this->id, 'result' => $result]);
            #Download avatar
            new User($_SESSION['user_id'])->addAvatar(false, 'https://img2.finalfantasyxiv.com/f/'.$this->avatar_id.'c0.jpg', $this->id);
            return ['response' => $result];
        } catch (\Throwable $exception) {
            return ['http_error' => 500, 'reason' => $exception->getMessage()];
        }
    }
}