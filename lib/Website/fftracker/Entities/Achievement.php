<?php
declare(strict_types = 1);

namespace Simbiat\Website\fftracker\Entities;

use Simbiat\Website\Config;
use Simbiat\Cron\TaskInstance;
use Simbiat\Website\Errors;
use Simbiat\Website\fftracker\Entity;
use Simbiat\Website\HomePage;
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
        $data = HomePage::$dbController->selectRow('SELECT * FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`achievementid` = :id', [':id' => $this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = HomePage::$dbController->selectAll('SELECT * FROM (SELECT \'character\' AS `type`, `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar` AS `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`characterid` = `ffxiv__character_achievement`.`characterid` WHERE `ffxiv__character_achievement`.`achievementid` = :id ORDER BY `ffxiv__character_achievement`.`time` DESC LIMIT 50) t ORDER BY `name`', [':id' => $this->id]);
        #Register for an update if old enough or category or howto or dbid are empty. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot']) && !empty($data['characters']) && (empty($data['category']) || empty($data['subcategory']) || empty($data['howto']) || empty($data['dbid']) || (time() - strtotime($data['updated'])) >= 31536000)) {
            (new TaskInstance())->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$this->id, 'achievement'], 'message' => 'Updating achievement with ID '.$this->id, 'priority' => 2])->add();
        }
        return $data;
    }
    
    /**
     * Get data from Lodestone
     * @param bool $allowSleep Whether to wait in case Lodestone throttles the request (that is throttle on our side)
     *
     * @return string|array
     */
    public function getFromLodestone(bool $allowSleep = false): string|array
    {
        #Cache objects
        $Lodestone = new Lodestone();
        #Get characters
        $altChars = HomePage::$dbController->selectColumn(
            'SELECT `characterid` FROM `ffxiv__character_achievement` WHERE `achievementid`=:ach ORDER BY `time` DESC LIMIT 50;',
            [
                ':ach' => $this->id,
            ]
        );
        if (empty($altChars)) {
            return ['404' => true];
        }
        #Iterrate list
        foreach ($altChars as $char) {
            $data = $Lodestone->getCharacterAchievements($char, (int)$this->id)->getResult();
            #Take a pause if we were throttled, and pause is allowed
            if (!empty($Lodestone->getLastError()['error']) && preg_match('/Lodestone has throttled the request, 429/', $Lodestone->getLastError()['error']) === 1) {
                if ($allowSleep) {
                    sleep(60);
                }
                return 'Request throttled by Lodestone';
            }
            if (!empty($data['characters'][$char]['achievements'][$this->id]) && \is_array($data['characters'][$char]['achievements'][$this->id])) {
                #Update character ID
                #Try to get achievement ID as seen in Lodestone database (play guide)
                $data = $Lodestone->searchDatabase('achievement', 0, 0, $data['characters'][$char]['achievements'][$this->id]['name'])->getResult();
                if (!empty($data['database']['achievement'])) {
                    #Remove counts elements from achievement database
                    unset($data['database']['achievement']['pageCurrent'], $data['database']['achievement']['pageTotal'], $data['database']['achievement']['total']);
                    #Flip the array of achievements (if any) to ease searching for the right element
                    $data['database']['achievement'] = array_flip(array_combine(array_keys($data['database']['achievement']), array_column($data['database']['achievement'], 'name')));
                }
                #Set dbid
                if (empty($data['database']['achievement'][$data['characters'][$char]['achievements'][$this->id]['name']])) {
                    $data['characters'][$char]['achievements'][$this->id]['dbid'] = NULL;
                } else {
                    $data['characters'][$char]['achievements'][$this->id]['dbid'] = $data['database']['achievement'][$data['characters'][$char]['achievements'][$this->id]['name']];
                }
                #Remove time
                unset($data['characters'][$char]['achievements'][$this->id]['time']);
                $data = $data['characters'][$char]['achievements'][$this->id];
                $data['id'] = $this->id;
                return $data;
            }
        }
        return [];
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
        #Eggstreme Hunting is a duplicate name for Legacy achievement (ID 500) and for current one (ID 903).
        #But current seasonal achievements do not have viewable page in Lodestone Database for some reason.
        #Yet DBID is found for current achievement due to... Duplicate name. Which results in unique key violation.
        #Since it's supposed to be "invisible" we enforce DBID to be null for it.
        if (empty($this->lodestone['dbid']) || $this->id === '903') {
            $bindings[':dbid'] = [NULL, 'null'];
        } else {
            $bindings[':dbid'] = $this->lodestone['dbid'];
        }
        try {
            return HomePage::$dbController->query('INSERT INTO `ffxiv__achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid ON DUPLICATE KEY UPDATE `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid, `updated`=CURRENT_TIMESTAMP()', $bindings);
        } catch (\Exception $e) {
            Errors::error_log($e, 'achievementid: '.$this->id);
            return false;
        }
    }
}