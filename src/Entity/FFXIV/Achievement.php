<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity\FFXIV;

use App\Service\Config;
use App\Service\Errors;
use App\Service\Images;
use App\Service\Sanitization;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;

/**
 * Class representing a FFXIV achievement
 */
class Achievement extends AbstractEntity
{
    #Custom properties
    protected const string ENTITY_TYPE = 'achievement';
    public int $updated;
    public int $registered;
    public ?string $category = null;
    public ?string $subcategory = null;
    public ?string $icon = null;
    public ?string $how_to = null;
    public ?string $db_id = null;
    public array $rewards = [];
    public array $characters = [];
    
    /**
     * Function to get initial data from DB
     *
     * @param bool $non_private Whether selection of characters is only for non-private characters
     *
     * @return array
     */
    protected function getFromDB(bool $non_private = false): array
    {
        #Get general information
        $data = Query::query('SELECT * FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`achievement_id` = :id', [':id' => $this->id], return: 'row');
        #Return empty if nothing was found
        if ($data === []) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = Query::query('SELECT
                                                        \'character\' AS `type`,
                                                        c.`character_id` AS `id`,
                                                        c.`name`,
                                                        c.`avatar`      AS `icon`,
                                                        (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`c`.`character_id`) AS `user_id`
                                                    FROM `ffxiv__character` AS c
                                                    JOIN (
                                                            SELECT `character_id`
                                                            FROM `ffxiv__character_achievement`
                                                            WHERE `achievement_id` = :id
                                                            ORDER BY `time` DESC
                                                            LIMIT 50
                                                         ) AS ca
                                                      ON c.`character_id` = ca.`character_id`
                                                    WHERE c.`hidden_achievements` IS NULL
                                                    ORDER BY c.`name`;',
            [':id' => $this->id], return: 'all');
        return $data;
    }
    
    /**
     * Get data from Lodestone
     *
     * @param bool $allow_sleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     * @internal
     * @return string|array
     * @throws \Exception
     */
    public function getFromLodestone(bool $allow_sleep = false): string|array
    {
        $achievement = $this->getFromDB(true);
        if (empty($achievement['name'])) {
            return ['404' => true, 'reason' => 'Achievement with ID `'.$this->id.'` is not found on Tracker'];
        }
        #Cache Lodestone
        $lodestone = new Lodestone();
        #If we do not have db_id already - try to get one
        if (empty($achievement['db_id'])) {
            $achievement['db_id'] = $this->getDBID($achievement['name']);
        }
        #Somewhat simpler and faster processing if we have db_id already
        if (!empty($achievement['db_id'])) {
            try {
                $data = $lodestone->getAchievementFromDB($achievement['db_id'])->getResult();
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
            #Most likely temporary unavailability of the Lodestone page
            if (empty($data['database']['achievement'][$achievement['db_id']])) {
                return ['404' => true];
            }
            $data = $data['database']['achievement'][$achievement['db_id']];
            unset($data['time']);
            $data['db_id'] = $achievement['db_id'];
            $data['id'] = $this->id;
            return $data;
        }
        if (empty($achievement['characters'])) {
            return ['404' => true];
        }
        #Iterrate list
        $achievement['characters'] = [['id' => 1]];
        foreach ($achievement['characters'] as $char) {
            try {
                $data = $lodestone->getCharacterAchievements($char['id'], (int)$this->id)->getResult();
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
                return $exception->getMessage();
            }
            if (\array_key_exists('private', $data['characters'][$char['id']]['achievements']) && $data['characters'][$char['id']]['achievements']['private']) {
                #Mark character as having private achievements
                try {
                    Query::query('UPDATE `ffxiv__character` SET `hidden_achievements`=CURRENT_TIMESTAMP(6) WHERE `character_id`=:id;', [':id' => $char['id']]);
                } catch (\Throwable $exception) {
                    Errors::error_log($exception);
                }
                continue;
            }
            if (!empty($data['characters'][$char['id']]['achievements'][$this->id]) && \is_array($data['characters'][$char['id']]['achievements'][$this->id])) {
                #Try to get achievement ID as seen in Lodestone database (play guide)
                $data['characters'][$char['id']]['achievements'][$this->id]['db_id'] = $this->getDBID($data['characters'][$char['id']]['achievements'][$this->id]['name']);
                #Remove time
                unset($data['characters'][$char['id']]['achievements'][$this->id]['time']);
                $data = $data['characters'][$char['id']]['achievements'][$this->id];
                $data['id'] = $this->id;
                return $data;
            }
        }
        return ['404' => true];
    }
    
    /**
     * Helper function to get db_id from Lodestone based on the achievement name
     * @param string $search_for
     *
     * @return string|null
     */
    private function getDBID(string $search_for): string|null
    {
        try {
            $db_search_result = new Lodestone()->searchDatabase('achievement', 0, 0, $search_for)->getResult();
        } catch (\Throwable) {
            return null;
        }
        #Remove counts elements from achievement database
        unset($db_search_result['database']['achievement']['page_current'], $db_search_result['database']['achievement']['page_total'], $db_search_result['database']['achievement']['total']);
        if (\count($db_search_result) === 0) {
            return null;
        }
        #Flip the array of achievements (if any) to ease searching for the right element
        $db_search_result['database']['achievement'] = \array_flip(\array_combine(\array_keys($db_search_result['database']['achievement']), \array_column($db_search_result['database']['achievement'], 'name')));
        if (!empty($db_search_result['database']['achievement'][$search_for])) {
            return $db_search_result['database']['achievement'][$search_for];
        }
        return null;
    }
    
    /**
     * Function to do processing of DB data
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->name = $from_db['name'];
        $this->updated = \strtotime($from_db['updated']);
        $this->registered = \strtotime($from_db['registered']);
        $this->category = $from_db['category'];
        $this->subcategory = $from_db['subcategory'];
        $this->icon = $from_db['icon'];
        $this->how_to = Sanitization::sanitizeHTML($from_db['how_to']);
        $this->db_id = $from_db['db_id'];
        $this->rewards = [
            'points' => (int)$from_db['points'],
            'title' => $from_db['title'],
            'item' => [
                'name' => $from_db['item'],
                'icon' => $from_db['item_icon'],
                'id' => $from_db['item_id'],
            ],
        ];
        $this->characters = [
            'total' => (int)$from_db['earned_by'],
            'last' => $from_db['characters'],
        ];
    }
    
    /**
     * Function to update the entity in DB
     * @return bool
     */
    protected function updateDB(): bool
    {
        
        #Prepare bindings for actual update
        $bindings = [];
        $bindings[':achievement_id'] = $this->id;
        $bindings[':name'] = $this->lodestone['name'];
        $bindings[':icon'] = self::removeLodestoneDomain($this->lodestone['icon']);
        #Download icon
        $webp = Images::download($this->lodestone['icon'], Config::$icons.$bindings[':icon']);
        if ($webp) {
            $bindings[':icon'] = \str_replace('.png', '.webp', $bindings[':icon']);
        }
        $bindings[':points'] = $this->lodestone['points'];
        $bindings[':category'] = $this->lodestone['category'];
        $bindings[':subcategory'] = $this->lodestone['subcategory'];
        if (empty($this->lodestone['how_to'])) {
            $bindings[':how_to'] = [NULL, 'null'];
        } else {
            $bindings[':how_to'] = Sanitization::sanitizeHTML($this->lodestone['how_to']);
        }
        if (empty($this->lodestone['title'])) {
            $bindings[':title'] = [NULL, 'null'];
        } else {
            $bindings[':title'] = $this->lodestone['title'];
        }
        if (empty($this->lodestone['item']['name'])) {
            $bindings[':item'] = [NULL, 'null'];
        } else {
            $bindings[':item'] = $this->lodestone['item']['name'];
        }
        if (empty($this->lodestone['item']['icon'])) {
            $bindings[':item_icon'] = [NULL, 'null'];
        } else {
            $bindings[':item_icon'] = self::removeLodestoneDomain($this->lodestone['item']['icon']);
            #Download icon
            $webp = Images::download($this->lodestone['item']['icon'], Config::$icons.$bindings[':item_icon']);
            if ($webp) {
                $bindings[':item_icon'] = \str_replace('.png', '.webp', $bindings[':item_icon']);
            }
        }
        if (empty($this->lodestone['item']['id'])) {
            $bindings[':item_id'] = [NULL, 'null'];
        } else {
            $bindings[':item_id'] = $this->lodestone['item']['id'];
        }
        if (empty($this->lodestone['db_id'])) {
            $bindings[':db_id'] = [NULL, 'null'];
        } else {
            $bindings[':db_id'] = $this->lodestone['db_id'];
        }
        try {
            return Query::query('INSERT INTO `ffxiv__achievement` SET `achievement_id`=:achievement_id, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `how_to`=:how_to, `title`=:title, `item`=:item, `item_icon`=:item_icon, `item_id`=:item_id, `db_id`=:db_id ON DUPLICATE KEY UPDATE `achievement_id`=:achievement_id, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `how_to`=:how_to, `title`=:title, `item`=:item, `item_icon`=:item_icon, `item_id`=:item_id, `db_id`=:db_id, `updated`=CURRENT_TIMESTAMP(6)', $bindings);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'achievement_id: '.$this->id);
            return false;
        }
    }
}