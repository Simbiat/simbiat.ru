<?php
#Functions used to get data from Lodestone
declare(strict_types=1);
namespace Simbiat\FFTModules;

use Simbiat\Database\Controller;
use Simbiat\Lodestone;

trait Grabber
{
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
                    $data = $this->CharacterGrab($id);
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'freecompany':
                #Check if numeric and reset type, if it's not
                if (is_numeric($id) === true) {
                    $data = $this->CompanyGrab($id);
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'linkshell':
                #Check if numeric and reset type, if it's not
                if (is_numeric($id) === true) {
                    $data = $this->LinkshellGrab($id);
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'crossworldlinkshell':
                #Check if valid format
                if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                    $data = $this->CrossLinkGrab($id);
                } else {
                    $data = $this->LodestoneGrab($id);
                }
                break;
            case 'pvpteam':
                #Check if valid format
                if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                    $data = $this->PVPGrab($id);
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
                        $data = $this->AchievementGrab($charId, $id);
                    }
                } else {
                    $data = 'Wrong ID for achievement';
                }
                break;
            case '':
                if (is_numeric($id)) {
                    #Try getting character
                    $data = $this->CharacterGrab($id);
                    if (!is_array($data) || $data['404'] === true) {
                        #Try getting Free Company
                        $data = $this->CompanyGrab($id);
                        if (!is_array($data) || $data['404'] === true) {
                            #Try getting Linkshell
                            $data = $this->LinkshellGrab($id);
                            if (!is_array($data) || $data['404'] === true) {
                                $data = 'Failed to find entity with ID '.$id;
                            }
                        }
                    }
                } else {
                    if (preg_match('/[a-zA-Z0-9]{40}/mi', $id)) {
                        #Try getting PvP Team
                        $data = $this->PVPGrab($id);
                        if (!is_array($data) || $data['404'] === true) {
                            #Try getting Crossworld Linkshell
                            $data = $this->CrossLinkGrab($id);
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

    private function CharacterGrab(string $id): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->setLanguage($this->language)->setUseragent($this->useragent)->getCharacter($id)->getCharacterJobs($id)->getCharacterAchievements($id, false, 0, false, false, true)->getResult();
        if (empty($data['characters'][$id]['server'])) {
            if (@$data['characters'][$id] == 404) {
                $data['entitytype'] = 'character';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Character '.$id;
                } else {
                    return 'Failed to get all necessary data for Character '.$id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['characters'][$id];
        $data['characterid'] = $id;
        $data['entitytype'] = 'character';
        $data['404'] = false;
        return $data;
    }

    private function CompanyGrab(string $id): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->setLanguage($this->language)->setUseragent($this->useragent)->getFreeCompany($id)->getFreeCompanyMembers($id, 0)->getResult();
        if (empty($data['freecompanies'][$id]['server']) || (!empty($data['freecompanies'][$id]['members']) && count($data['freecompanies'][$id]['members']) < intval($data['freecompanies'][$id]['members_count'])) || (empty($data['freecompanies'][$id]['members']) && intval($data['freecompanies'][$id]['members_count']) > 0)) {
            if (@$data['freecompanies'][$id] == 404) {
                $data['entitytype'] = 'freecompany';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Free Company '.$id;
                } else {
                    return 'Failed to get all necessary data for Free Company '.$id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['freecompanies'][$id];
        $data['freecompanyid'] = $id;
        $data['entitytype'] = 'freecompany';
        $data['404'] = false;
        return $data;
    }

    private function LinkshellGrab(string $id): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->setLanguage($this->language)->setUseragent($this->useragent)->getLinkshellMembers($id, 0)->getResult();
        if (empty($data['linkshells'][$id]['server']) || (!empty($data['linkshells'][$id]['members']) && count($data['linkshells'][$id]['members']) < intval($data['linkshells'][$id]['memberscount'])) || (empty($data['linkshells'][$id]['members']) && intval($data['linkshells'][$id]['memberscount']) > 0)) {
            if (@$data['linkshells'][$id]['members'] == 404) {
                $data['entitytype'] = 'linkshell';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Linkshell '.$id;
                } else {
                    return 'Failed to get all necessary data for Linkshell '.$id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['linkshells'][$id];
        $data['linkshellid'] = $id;
        $data['entitytype'] = 'linkshell';
        $data['404'] = false;
        return $data;
    }

    private function CrossLinkGrab(string $id): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->setLanguage($this->language)->setUseragent($this->useragent)->getLinkshellMembers($id, 0)->getResult();
        if (empty($data['linkshells'][$id]['dataCenter']) || (!empty($data['linkshells'][$id]['members']) && count($data['linkshells'][$id]['members']) < $data['linkshells'][$id]['memberscount']) || (empty($data['linkshells'][$id]['members']) && intval($data['linkshells'][$id]['memberscount']) > 0)) {
            if (@$data['linkshells'][$id]['members'] == 404) {
                $data['entitytype'] = 'crossworldlinkshell';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for Crossworld Linkshell '.$id;
                } else {
                    return 'Failed to get all necessary data for Crossworld Linkshell '.$id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['linkshells'][$id];
        $data['linkshellid'] = $id;
        $data['entitytype'] = 'crossworldlinkshell';
        $data['404'] = false;
        return $data;
    }

    private function PVPGrab(string $id): string|array
    {
        $Lodestone = (new Lodestone);
        $data = $Lodestone->getPvPTeam($id)->getResult();
        if (empty($data['pvpteams'][$id]['dataCenter']) || empty($data['pvpteams'][$id]['members'])) {
            if (@$data['pvpteams'][$id]['members'] == 404) {
                $data['entitytype'] = 'pvpteam';
                $data['404'] = true;
                return $data;
            } else {
                if (empty($Lodestone->getLastError())) {
                    return 'Failed to get any data for PvP Team '.$id;
                } else {
                    return 'Failed to get all necessary data for PvP Team '.$id.' ('.$Lodestone->getLastError()['url'].'): '.$Lodestone->getLastError()['error'];
                }
            }
        }
        $data = $data['pvpteams'][$id];
        $data['pvpteamid'] = $id;
        $data['entitytype'] = 'pvpteam';
        $data['404'] = false;
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function AchievementGrab(string $character, string $achievement): string|array
    {
        #Grab data
        $Lodestone = (new Lodestone)->setUseragent($this->useragent)->setLanguage($this->language);
        $data = $Lodestone->getCharacterAchievements($character, intval($achievement))->getResult();
        if (empty($data['characters'][$character]['achievements'][$achievement])) {
            $error = $Lodestone->getLastError();
            #Attempt to get other characters
            if ($error['error'] === 'Requests are (temporary) blocked, 403') {
                $dbController = (new Controller);
                #Get characters
                $altChars = $dbController->selectColumn(
                    'SELECT `characterid` FROM `ffxiv__character_achievement` WHERE `achievementid`=:ach AND `characterid` !=:char ORDER BY `time` DESC;',
                    [
                        ':ach' => $achievement,
                        ':char' => $character,
                    ]
                );
                #Iterrate list
                foreach ($altChars as $char) {
                    $data = $Lodestone->getCharacterAchievements($char, intval($achievement))->getResult();
                    if (!empty($data['characters'][$char]['achievements'][$achievement])) {
                        #Update character ID
                        $character = $char;
                        goto dataFound;
                    }
                }
            }
            return $error['error'].' ('.$error['url'].')';
        }
        dataFound:
        #Try to get achievement ID as seen in Lodestone database (play guide)
        $data = $Lodestone->searchDatabase('achievement', 0, 0, $data['characters'][$character]['achievements'][$achievement]['name'])->getResult();
        #Remove counts elements from achievement database
        unset($data['database']['achievement']['pageCurrent'], $data['database']['achievement']['pageTotal'], $data['database']['achievement']['total']);
        #Flip the array of achievements (if any) to ease searching for the right element
        $data['database']['achievement'] = array_flip(array_combine(array_keys($data['database']['achievement']), array_column($data['database']['achievement'], 'name')));
        #Set dbid
        if (empty($data['database']['achievement'][$data['characters'][$character]['achievements'][$achievement]['name']])) {
            $data['characters'][$character]['achievements'][$achievement]['dbid'] = NULL;
        } else {
            $data['characters'][$character]['achievements'][$achievement]['dbid'] = $data['database']['achievement'][$data['characters'][$character]['achievements'][$achievement]['name']];
        }
        $data = $data['characters'][$character]['achievements'][$achievement];
        #Prepare bindings for actual update
        $bindings = [];
        $bindings[':achievementid'] = $achievement;
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
        if (empty($data['dbid'])) {
            $bindings[':dbid'] = [NULL, 'null'];
        } else {
            $bindings[':dbid'] = $data['dbid'];
        }
        $bindings['entitytype'] = 'achievement';
        return $bindings;
    }
}
