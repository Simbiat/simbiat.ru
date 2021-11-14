<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\fftracker\Entity;
use Simbiat\Lodestone;

class Achievement extends Entity
{
    #Custom properties
    protected const entityType = 'achievement';
    protected const idFormat = '/^\d+$/mi';
    private ?string $character = null;

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    protected function getFromDB(): array
    {
        $dbcon = (new Controller);
        #Get general information
        $data = $dbcon->selectRow('SELECT *, (SELECT COUNT(*) FROM `'.self::dbPrefix.'character_achievement` WHERE `'.self::dbPrefix.'character_achievement`.`achievementid` = `'.self::dbPrefix.'achievement`.`achievementid`) as `count` FROM `'.self::dbPrefix.'achievement` WHERE `'.self::dbPrefix.'achievement`.`achievementid` = :id', [':id'=>$this->id]);
        #Return empty, if nothing was found
        if (empty($data) || !is_array($data)) {
            return [];
        }
        #Get last characters with this achievement
        $data['characters'] = $dbcon->selectAll('SELECT * FROM (SELECT \'character\' AS `type`, `'.self::dbPrefix.'character`.`characterid` AS `id`, `'.self::dbPrefix.'character`.`name`, `'.self::dbPrefix.'character`.`avatar` AS `icon` FROM `'.self::dbPrefix.'character_achievement` LEFT JOIN `'.self::dbPrefix.'character` ON `'.self::dbPrefix.'character`.`characterid` = `'.self::dbPrefix.'character_achievement`.`characterid` WHERE `'.self::dbPrefix.'character_achievement`.`achievementid` = :id ORDER BY `'.self::dbPrefix.'character_achievement`.`time` DESC LIMIT 10) t ORDER BY `name`', [':id'=>$this->id]);
        #Register for an update if old enough or category or howto or dbid are empty. Also check that this is not a bot (if \Simbiat\usercontrol is used).
        if ((empty($data['category']) || empty($data['subcategory']) || empty($data['howto']) || empty($data['dbid']) || (time() - strtotime($data['updated'])) >= 31536000) && !empty($data['characters']) && empty($_SESSION['UA']['bot'])) {
            (new Cron)->add('ffentityupdate', [$this->id, 'achievement'], priority: 2, message: 'Updating achievement with ID '.$this->id);
        }
        unset($dbcon);
        return $data;
    }

    /**
     * @throws \Exception
     */
    public function getFromLodestone(): string|array
    {
        #Cache objects
        $Lodestone = (new Lodestone);
        $dbController = (new Controller);
        #Get characters
        $altChars = $dbController->selectColumn(
            'SELECT `characterid` FROM `'.self::dbPrefix.'character_achievement` WHERE `achievementid`=:ach AND `characterid` !=:char ORDER BY `time` DESC;',
            [
                ':ach' => $this->id,
                ':char' => $this->character,
            ]
        );
        #Iterrate list
        foreach ($altChars as $char) {
            $data = $Lodestone->getCharacterAchievements($char, intval($this->id))->getResult();
            if (!empty($data['characters'][$char]['achievements'][$this->id])) {
                #Update character ID
                $this->character = $char;
                #Try to get achievement ID as seen in Lodestone database (play guide)
                $data = $Lodestone->searchDatabase('achievement', 0, 0, $data['characters'][$this->character]['achievements'][$this->id]['name'])->getResult();
                #Remove counts elements from achievement database
                unset($data['database']['achievement']['pageCurrent'], $data['database']['achievement']['pageTotal'], $data['database']['achievement']['total']);
                #Flip the array of achievements (if any) to ease searching for the right element
                $data['database']['achievement'] = array_flip(array_combine(array_keys($data['database']['achievement']), array_column($data['database']['achievement'], 'name')));
                #Set dbid
                if (empty($data['database']['achievement'][$data['characters'][$this->character]['achievements'][$this->id]['name']])) {
                    $data['characters'][$this->character]['achievements'][$this->id]['dbid'] = NULL;
                } else {
                    $data['characters'][$this->character]['achievements'][$this->id]['dbid'] = $data['database']['achievement'][$data['characters'][$this->character]['achievements'][$this->id]['name']];
                }
                $data = $data['characters'][$this->character]['achievements'][$this->id];
                #Prepare bindings for actual update
                $bindings = [];
                $bindings[':achievementid'] = $this->id;
                $bindings[':name'] = $data['name'];
                $bindings[':icon'] = str_replace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', '', $data['icon']);
                $bindings[':points'] = $data['points'];
                $bindings[':category'] = $data['category'];
                $bindings[':subcategory'] = $data['subcategory'];
                if (empty($data['howto'])) {
                    $bindings[':howto'] = [NULL, 'null'];
                } else {
                    $bindings[':howto'] = $data['howto'];
                }
                if (empty($data['title'])) {
                    $bindings[':title'] = [NULL, 'null'];
                } else {
                    $bindings[':title'] = $data['title'];
                }
                if (empty($data['item']['name'])) {
                    $bindings[':item'] = [NULL, 'null'];
                } else {
                    $bindings[':item'] = $data['item']['name'];
                }
                if (empty($data['item']['icon'])) {
                    $bindings[':itemicon'] = [NULL, 'null'];
                } else {
                    $bindings[':itemicon'] = str_replace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', '', $data['item']['icon']);
                }
                if (empty($data['item']['id'])) {
                    $bindings[':itemid'] = [NULL, 'null'];
                } else {
                    $bindings[':itemid'] = $data['item']['id'];
                }
                #Eggstreme Hunting is a duplicate name for Legacy achievement (ID 500) and for current one (ID 903).
                #But current seasonal achievements do not have viewable page in Lodestone Database for some reason.
                #Yet DBID is found for current achievement due to... Duplicate name. Which results in unique key violation.
                #Since it's supposed to be "invisible" we enforce DBID to be null for it.
                if (empty($data['dbid']) || $this->id === '903') {
                    $bindings[':dbid'] = [NULL, 'null'];
                } else {
                    $bindings[':dbid'] = $data['dbid'];
                }
                $bindings['entitytype'] = 'achievement';
                return $bindings;
            }
        }
        return [];
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
            #Unset entitytype
            unset($data['entitytype']);
            return (new Controller)->query('INSERT INTO `'.self::dbPrefix.'achievement` SET `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid ON DUPLICATE KEY UPDATE `achievementid`=:achievementid, `name`=:name, `icon`=:icon, `points`=:points, `category`=:category, `subcategory`=:subcategory, `howto`=:howto, `title`=:title, `item`=:item, `itemicon`=:itemicon, `itemid`=:itemid, `dbid`=:dbid, `updated`=UTC_TIMESTAMP()', $data);
        } catch(\Exception $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        }
    }

    #Function to update the entity
    public function delete(): bool
    {
        #Achievements are not supposed to be deleted
        return true;
    }
}
