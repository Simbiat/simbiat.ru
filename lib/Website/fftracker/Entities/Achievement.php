<?php
declare(strict_types = 1);

namespace Simbiat\Website\fftracker\Entities;

use Simbiat\Website\Config;
use Simbiat\Cron\TaskInstance;
use Simbiat\Website\Errors;
use Simbiat\Website\fftracker\Entity;
use Simbiat\Website\Images;
use Simbiat\Lodestone;

/**
 * Class representing a FFXIV achievement
 */
class Achievement extends Entity
{
    #Custom properties
    protected const string entityType = 'achievement';
    public int $updated;
    public int $registered;
    public ?string $category = null;
    public ?string $subcategory = null;
    public ?string $icon = null;
    public ?string $howto = null;
    public ?string $dbid = null;
    public array $rewards = [];
    public array $characters = [];
    
    /**
     * Function to get initial data from DB
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = Config::$dbController->selectRow('SELECT * FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`achievementid` = :id', [':id' => $this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = Config::$dbController->selectAll('SELECT * FROM (SELECT \'character\' AS `type`, `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar` AS `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`characterid` = `ffxiv__character_achievement`.`characterid` WHERE `ffxiv__character_achievement`.`achievementid` = :id ORDER BY `ffxiv__character_achievement`.`time` DESC LIMIT 50) t ORDER BY `name`', [':id' => $this->id]);
        #Register for an update if old enough or category or howto or dbid are empty. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot']) && !empty($data['characters']) && (empty($data['category']) || empty($data['subcategory']) || empty($data['howto']) || empty($data['dbid']) || (time() - strtotime($data['updated'])) >= 31536000)) {
            (new TaskInstance())->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$this->id, 'achievement'], 'message' => 'Updating achievement with ID '.$this->id, 'priority' => 2])->add();
        }
        return $data;
    }
    
    /**
     * Get data from Lodestone
     *
     * @param bool $allowSleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     *
     * @return string|array
     * @throws \Exception
     */
    public function getFromLodestone(bool $allowSleep = false): string|array
    {
        #Get the data that we have
        $achievement = $this->getFromDB();
        #Cache Lodestone
        $Lodestone = new Lodestone();
        #If we do not have dbid already - try to get one
        if (empty($achievement['dbid'])) {
            $achievement['dbid'] = $this->getDBID($achievement['name']);
        }
        #Somewhat simpler and faster processing if we have dbid already
        if (!empty($achievement['dbid'])) {
            $data = $Lodestone->getAchievementFromDB($achievement['dbid'])->getResult();
            $data = $data['database']['achievement'][$achievement['dbid']];
            unset($data['time']);
            $data['dbid'] = $achievement['dbid'];
            $data['id'] = $this->id;
            return $data;
        }
        if (empty($achievement['characters'])) {
            return ['404' => true];
        }
        #Iterrate list
        foreach ($achievement['characters'] as $char) {
            $data = $Lodestone->getCharacterAchievements($char['id'], (int)$this->id)->getResult();
            #Take a pause if we were throttled, and pause is allowed
            if (!empty($Lodestone->getLastError()['error']) && preg_match('/Lodestone has throttled the request, 429/', $Lodestone->getLastError()['error']) === 1) {
                if ($allowSleep) {
                    sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (!empty($data['characters'][$char['id']]['achievements'][$this->id]) && \is_array($data['characters'][$char['id']]['achievements'][$this->id])) {
                #Try to get achievement ID as seen in Lodestone database (play guide)
                $data['characters'][$char['id']]['achievements'][$this->id]['dbid'] = $this->getDBID($data['characters'][$char['id']]['achievements'][$this->id]['name']);
                #Remove time
                unset($data['characters'][$char['id']]['achievements'][$this->id]['time']);
                $data = $data['characters'][$char['id']]['achievements'][$this->id];
                $data['id'] = $this->id;
                return $data;
            }
        }
        return [];
    }
    
    /**
     * Helper function to get dbid from Lodestone based on achievement name
     * @param string $searchFor
     *
     * @return string|null
     */
    private function getDBID(string $searchFor): string|null
    {
        $Lodestone = new Lodestone();
        $dbSearchResult = $Lodestone->searchDatabase('achievement', 0, 0, $searchFor)->getResult();
        #Remove counts elements from achievement database
        unset($dbSearchResult['database']['achievement']['pageCurrent'], $dbSearchResult['database']['achievement']['pageTotal'], $dbSearchResult['database']['achievement']['total']);
        #Flip the array of achievements (if any) to ease searching for the right element
        $dbSearchResult['database']['achievement'] = array_flip(array_combine(array_keys($dbSearchResult['database']['achievement']), array_column($dbSearchResult['database']['achievement'], 'name')));
        if (!empty($dbSearchResult['database']['achievement'][$searchFor])) {
            return $dbSearchResult['database']['achievement'][$searchFor];
        }
        return null;
    }
    
    /**
     * Function to do processing of DB data
     * @param array $fromDB
     *
     * @return void
     */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->updated = strtotime($fromDB['updated']);
        $this->registered = strtotime($fromDB['registered']);
        $this->category = $fromDB['category'];
        $this->subcategory = $fromDB['subcategory'];
        $this->icon = $fromDB['icon'];
        $this->howto = $fromDB['howto'];
        $this->dbid = $fromDB['dbid'];
        $this->rewards = [
            'points' => (int)$fromDB['points'],
            'title' => $fromDB['title'],
            'item' => [
                'name' => $fromDB['item'],
                'icon' => $fromDB['itemicon'],
                'id' => $fromDB['itemid'],
            ],
        ];
        $this->characters = [
            'total' => (int)$fromDB['earnedby'],
            'last' => $fromDB['characters'],
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
        $bindings[':achievementid'] = $this->id;
        $bindings[':name'] = $this->lodestone['name'];
        $bindings[':icon'] = self::removeLodestoneDomain($this->lodestone['icon']);
        #Download icon
        $webp = Images::download($this->lodestone['icon'], Config::$icons.$bindings[':icon']);
        if ($webp) {
            $bindings[':icon'] = str_replace('.png', '.webp', $bindings[':icon']);
        }
        $bindings[':points'] = $this->lodestone['points'];
        $bindings[':category'] = $this->lodestone['category'];
        $bindings[':subcategory'] = $this->lodestone['subcategory'];
        if (empty($this->lodestone['howto'])) {
            $bindings[':howto'] = [NULL, 'null'];
        } else {
            $bindings[':howto'] = $this->lodestone['howto'];
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
            $bindings[':itemicon'] = [NULL, 'null'];
        } else {
            $bindings[':itemicon'] = self::removeLodestoneDomain($this->lodestone['item']['icon']);
            #Download icon
            $webp = Images::download($this->lodestone['item']['icon'], Config::$icons.$bindings[':itemicon']);
            if ($webp) {
                $bindings[':itemicon'] = str_replace('.png', '.webp', $bindings[':itemicon']);
            }
        }
        if (empty($this->lodestone['item']['id'])) {
            $bindings[':itemid'] = [NULL, 'null'];
        } else {
            $bindings[':itemid'] = $this->lodestone['item']['id'];
        }
        if (empty($this->lodestone['dbid'])) {
            $bindings[':dbid'] = [NULL, 'null'];
        } else {
            $bindings[':dbid'] = $this->lodestone['dbid'];
        }
        try {
            return Config::$dbController->query('INSERT INTO `ffxiv__achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid ON DUPLICATE KEY UPDATE `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid, `updated`=CURRENT_TIMESTAMP()', $bindings);
        } catch (\Exception $e) {
            Errors::error_log($e, 'achievementid: '.$this->id);
            return false;
        }
    }
}