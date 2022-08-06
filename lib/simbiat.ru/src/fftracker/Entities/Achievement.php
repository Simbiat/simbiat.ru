<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Config\FFTracker;
use Simbiat\Cron;
use Simbiat\Curl;
use Simbiat\fftracker\Entity;
use Simbiat\fftracker\Traits;
use Simbiat\Lodestone;

class Achievement extends Entity
{
    use Traits;

    #Custom properties
    protected const entityType = 'achievement';
    public int $updated;
    public int $registered;
    public ?string $category = null;
    public ?string $subcategory = null;
    public ?string $icon = null;
    public ?string $howto = null;
    public ?string $dbid = null;
    public array $rewards = [];
    public array $characters = [];

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        #Get general information
        $data = $this->dbController->selectRow('SELECT *, (SELECT COUNT(*) FROM `ffxiv__character_achievement` WHERE `ffxiv__character_achievement`.`achievementid` = `ffxiv__achievement`.`achievementid`) as `count` FROM `ffxiv__achievement` WHERE `ffxiv__achievement`.`achievementid` = :id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data)) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = $this->dbController->selectAll('SELECT * FROM (SELECT \'character\' AS `type`, `ffxiv__character`.`characterid` AS `id`, `ffxiv__character`.`name`, `ffxiv__character`.`avatar` AS `icon` FROM `ffxiv__character_achievement` LEFT JOIN `ffxiv__character` ON `ffxiv__character`.`characterid` = `ffxiv__character_achievement`.`characterid` WHERE `ffxiv__character_achievement`.`achievementid` = :id ORDER BY `ffxiv__character_achievement`.`time` DESC LIMIT 50) t ORDER BY `name`', [':id'=>$this->id]);
        #Register for an update if old enough or category or howto or dbid are empty. Also check that this is not a bot.
        if (empty($_SESSION['UA']['bot'])) {
            if ((empty($data['category']) || empty($data['subcategory']) || empty($data['howto']) || empty($data['dbid']) || (time() - strtotime($data['updated'])) >= 31536000) && !empty($data['characters'])) {
                (new Cron)->add('ffUpdateEntity', [$this->id, 'achievement'], priority: 2, message: 'Updating achievement with ID ' . $this->id);
            }
        }
        return $data;
    }

    /**
     * @throws \Exception
     */
    public function getFromLodestone(): string|array
    {
        #Cache objects
        $Lodestone = (new Lodestone);
        #Get characters
        $altChars = $this->dbController->selectColumn(
            'SELECT `characterid` FROM `ffxiv__character_achievement` WHERE `achievementid`=:ach ORDER BY `time` DESC;',
            [
                ':ach' => $this->id,
            ]
        );
        if (empty($altChars)) {
            return ['404' => true];
        }
        #Iterrate list
        foreach ($altChars as $char) {
            $data = $Lodestone->getCharacterAchievements($char, intval($this->id))->getResult();
            if (!empty($data['characters'][$char]['achievements'][$this->id])) {
                #Update character ID
                #Try to get achievement ID as seen in Lodestone database (play guide)
                $data = $Lodestone->searchDatabase('achievement', 0, 0, $data['characters'][$char]['achievements'][$this->id]['name'])->getResult();
                #Remove counts elements from achievement database
                unset($data['database']['achievement']['pageCurrent'], $data['database']['achievement']['pageTotal'], $data['database']['achievement']['total']);
                if (!empty($data['database']['achievement'])) {
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

    #Function to do processing
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
            'points' => intval($fromDB['points']),
            'title' => $fromDB['title'],
            'item' => [
                'name' => $fromDB['item'],
                'icon' => $fromDB['itemicon'],
                'id' => $fromDB['itemid'],
            ],
        ];
        $this->characters = [
            'total' => intval($fromDB['count']),
            'last' => $fromDB['characters'],
        ];
    }

    #Function to update the entity
    protected function updateDB(): string|bool
    {
        try {
            #Prepare bindings for actual update
            $bindings = [];
            $bindings[':achievementid'] = $this->id;
            $bindings[':name'] = $this->lodestone['name'];
            $bindings[':icon'] = str_replace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', '', $this->lodestone['icon']);
            #Download icon
            (new Curl())->imageDownload($this->lodestone['icon'], FFTracker::$icons.$bindings[':icon']);
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
                $bindings[':itemicon'] = str_replace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', '', $this->lodestone['item']['icon']);
                #Download icon
                (new Curl())->imageDownload($this->lodestone['item']['icon'], FFTracker::$icons.$bindings[':itemicon']);
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
            return $this->dbController->query('INSERT INTO `ffxiv__achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid ON DUPLICATE KEY UPDATE `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid, `updated`=UTC_TIMESTAMP()', $bindings);
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    protected function delete(): bool
    {
        #Achievements are not supposed to be deleted
        return true;
    }
}
