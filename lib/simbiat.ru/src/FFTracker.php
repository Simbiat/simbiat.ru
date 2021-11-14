<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Database\Controller;
use Simbiat\fftracker\Entities\Achievement;
use Simbiat\fftracker\Entities\Character;
use Simbiat\fftracker\Entities\CrossworldLinkshell;
use Simbiat\fftracker\Entities\FreeCompany;
use Simbiat\fftracker\Entities\Linkshell;
use Simbiat\fftracker\Entities\PvPTeam;

class FFTracker
{
    #Allowed languages
    private ?HTMLCache $HTMLCache = NULL;

    public function __construct(string $language = 'na', int $maxAge = 90, int $maxLines = 50, string $userAgent = '', string $cacheDir = '')
    {
        #Checking if HTML Cache is used
        if (method_exists('\Simbiat\HTMLCache','delete')) {
            if (empty($cacheDir)) {
                $this->HTMLCache = (new HTMLCache());
            } else {
                $this->HTMLCache = (new HTMLCache($cacheDir));
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function Update(string $id, string $type = '', string $charId = ''): string|bool
    {
        #If type is set, check if entity exists and get its updated time
        if (!empty($type) && in_array($type, ['achievement', 'character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam'])) {
            $updated = (new Database\Controller)->selectValue('SELECT `updated` FROM `ffxiv__'.($type === 'crossworldlinkshell' ? 'linkshell' : $type).'` WHERE `'.($type === 'crossworldlinkshell' ? 'linkshell' : $type).'id` = :id', [':id'=>$id]);
            #Check if it has not been updated recently (10 minutes, to protect potential abuse)
            if ($updated !== NULL && (time() - strtotime($updated)) < 600) {
                #Return entity type
                return $type;
            }
        }
        #Grab data first
        $data = $this->LodestoneGrab($id, $type, $charId);
        if (is_array($data)) {
            if (isset($data['404']) && $data['404'] === true) {
                #Means that entity was removed from Lodestone
                #Mark as deleted in tracker
                $result = match($data['entitytype']) {
                    'character' => (new Character)->setId($id)->delete(),
                    'freecompany' => (new FreeCompany)->setId($id)->delete(),
                    'pvpteam' => (new PvPTeam)->setId($id)->delete(),
                    'linkshell' => (new Linkshell)->setId($id)->delete(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($id)->delete(),
                };
                if ($result === true) {
                    #Clean cache
                    if ($this->HTMLCache !== NULL) {
                        $this->HTMLCache->delete('fftracker/'.$data['entitytype'].'/'.$id);
                    }
                }
                return $result;
            } else {
                #Data was retrieved, update entity
                $result = $this->EntityUpdate($data);
                if ($result === true) {
                    #Clean cache
                    if ($this->HTMLCache !== NULL) {
                        $this->HTMLCache->delete('fftracker/'.$data['entitytype'].'/'.$id);
                    }
                    return $data['entitytype'];
                } else {
                    return $result;
                }
            }
        } else {
            #This means, that an error was returned
            return strval($data);
        }
    }

    #Attempt to grab data
    /**
     * @throws \Exception
     */
    private function LodestoneGrab(string $id, string $type = '', string $charId = ''): string|array
    {
        switch ($type) {
            case 'character':
                #Check if numeric and reset type, if it's not
                if (is_numeric($id) === true) {
                    $data = (new Character)->setId($id)->getFromLodestone();
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'freecompany':
                #Check if numeric and reset type, if it's not
                if (is_numeric($id) === true) {
                    $data = (new FreeCompany)->setId($id)->getFromLodestone();
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'linkshell':
                #Check if numeric and reset type, if it's not
                if (is_numeric($id) === true) {
                    $data = (new Linkshell)->setId($id)->getFromLodestone();
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'crossworldlinkshell':
                #Check if valid format
                if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                    $data = (new CrossworldLinkshell)->setId($id)->getFromLodestone();
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'pvpteam':
                #Check if valid format
                if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                    $data = (new PvPTeam)->setId($id)->getFromLodestone();
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'achievement':
                #Check if valid format
                if (is_numeric($id) === true) {
                    #Check if character is provided
                    if (empty($charId)) {
                        $data = 'No character ID provided for achievement';
                    } else {
                        $data = (new Achievement)->setId($id)->getFromLodestone();
                    }
                } else {
                    $data = 'Wrong ID for achievement';
                }
                break;
            case '':
                if (is_numeric($id)) {
                    #Try getting character
                    $data = (new Character)->setId($id)->getFromLodestone();
                    if (!is_array($data) || $data['404'] === true) {
                        #Try getting Free Company
                        $data = (new FreeCompany)->setId($id)->getFromLodestone();
                        if (!is_array($data) || $data['404'] === true) {
                            #Try getting Linkshell
                            $data = (new Linkshell)->setId($id)->getFromLodestone();
                            if (!is_array($data) || $data['404'] === true) {
                                $data = 'Failed to find entity with ID '.$id;
                            }
                        }
                    }
                } else {
                    if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                        #Try getting PvP Team
                        $data = (new PvPTeam)->setId($id)->getFromLodestone();
                        if (!is_array($data) || $data['404'] === true) {
                            #Try getting Crossworld Linkshell
                            $data = (new CrossworldLinkshell)->setId($id)->getFromLodestone();
                            if (!is_array($data) || $data['404'] === true) {
                                $data = 'Failed to find entity with ID '.$id;
                            }
                        }
                    } else {
                        $data = 'Wrong ID '.$id;
                    }
                }
                break;
            default:
                $data = 'Unsupported type '.$type;
        }
        return $data;
    }

    #Update data
    private function EntityUpdate(array $data): string|bool
    {
        return match(@$data['entitytype']) {
            'character' => $this->CharacterUpdate($data),
            'freecompany' => $this->CompanyUpdate($data),
            'linkshell' => $this->LinkshellUpdate($data),
            'crossworldlinkshell' => $this->LinkshellUpdate($data, true),
            'pvpteam' => $this->PVPUpdate($data),
            'achievement' => $this->AchievementUpdate($data),
            default => false,
        };
    }

    #Generalized function to get entity data
    /**
     * @throws \Exception
     */
    public function TrackerGrab(string $type, string $id): array
    {
        return match($type) {
            'character' => $this->GetCharacter($id),
            'achievement' => $this->GetAchievement($id),
            'freecompany' => $this->GetCompany($id),
            'pvpteam' => $this->GetPVP($id),
            'linkshell', 'crossworld_linkshell', 'crossworldlinkshell' => $this->GetLinkshell($id),
            default => [],
        };
    }

    #Function to show X random entities

    /**
     * @throws \Exception
     */
    public function GetRandomEntities(int $number): array
    {
        return (new Controller)->selectAll('
                (SELECT `characterid` AS `id`, \'character\' as `type`, `name`, `avatar` AS `icon`, 0 AS `crossworld` FROM `ffxiv__character` WHERE `characterid` IN (SELECT `characterid` FROM `ffxiv__character` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `freecompanyid` AS `id`, \'freecompany\' as `type`, `name`, `crest` AS `icon`, 0 AS `crossworld` FROM `ffxiv__freecompany` WHERE `freecompanyid` IN (SELECT `freecompanyid` FROM `ffxiv__freecompany` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `linkshellid` AS `id`, IF(`crossworld`=1, \'crossworld_linkshell\', \'linkshell\') as `type`, `name`, NULL AS `icon`, `crossworld` FROM `ffxiv__linkshell` WHERE `linkshellid` IN (SELECT `linkshellid` FROM `ffxiv__linkshell` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `pvpteamid` AS `id`, \'pvpteam\' as `type`, `name`, `crest` AS `icon`, 1 AS `crossworld` FROM `ffxiv__pvpteam`WHERE `pvpteamid` IN (SELECT `pvpteamid` FROM `ffxiv__pvpteam` WHERE `deleted` IS NULL ORDER BY RAND()) LIMIT '.$number.')
                UNION ALL
                (SELECT `achievementid` AS `id`, \'achievement\' as `type`, `name`, `icon`, 1 AS `crossworld` FROM `ffxiv__achievement` WHERE `achievementid` IN (SELECT `achievementid` FROM `ffxiv__achievement` ORDER BY RAND()) LIMIT '.$number.')
                ORDER BY RAND() LIMIT '.$number.'
        ');
    }
}
