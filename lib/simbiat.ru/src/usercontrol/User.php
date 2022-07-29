<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Abstracts\Entity;

class User extends Entity
{
    protected const dbPrefix = 'uc__';

    #Entity's properties
    public string $username;
    #Real name
    public array $name = [
        'firstname' => null,
        'lastname' => null,
        'middlename' => null,
        'fathername' => null,
        'prefix' => null,
        'suffix' => null,
    ];
    #Phone
    public ?string $phone = null;
    #Dates
    public array $dates = [
        'registered' => null,
        'updated' => null,
        'birthday' => null,
    ];
    #Parent details
    public array $parent = [
        'id' => null,
        'name' => null,
    ];
    #FF Token
    public ?string $ff_token = null;
    #Sex
    public ?int $sex = null;
    #About
    public ?string $about = null;
    #Timezone
    public ?string $timezone = null;
    #Country
    public ?string $country = null;
    #City
    public ?string $city = null;
    #Website
    public ?string $website = null;
    #Groups
    public array $groups = [];
    #Whether account is activated
    public bool $activated = false;
    #Emails
    public array $emails = [];
    #Avatars
    public array $avatars = [];
    #Current avatar
    public ?string $currentAvatar = null;

    protected function getFromDB(): array
    {
        $dbData =  $this->dbController->selectRow('SELECT `username`, `phone`, `ff_token`, `registered`, `updated`, `parentid`, (IF(`parentid` IS NULL, NULL, (SELECT `username` FROM `'.self::dbPrefix.'users` WHERE `userid`=:userid))) as `parentname`, `birthday`, `firstname`, `lastname`, `middlename`, `fathername`, `prefix`, `suffix`, `sex`, `about`, `timezone`, `country`, `city`, `website` FROM `'.self::dbPrefix.'users` WHERE `userid`=:userid', ['userid'=>[$this->id, 'int']]);
        if (empty($dbData)) {
            return [];
        }
        #Get user's groups
        $dbData['groups'] = $this->dbController->selectColumn('SELECT `groupid` FROM `'.self::dbPrefix.'user_to_group` WHERE `userid`=:userid', ['userid'=>[$this->id, 'int']]);
        if (in_array(2, $dbData['groups'], true)) {
            $dbData['activated'] = false;
        } else {
            $dbData['activated'] = true;
        }
        $dbData['currentAvatar'] = $this->dbController->selectValue('SELECT `url` FROM `uc__user_to_avatar` WHERE `userid`=:userid AND `current`=1 LIMIT 1', ['userid'=>[$this->id, 'int']]);
        return $dbData;
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        #Populate names
        $this->name['firstname'] = $fromDB['firstname'];
        $this->name['lastname'] = $fromDB['lastname'];
        $this->name['middlename'] = $fromDB['middlename'];
        $this->name['fathername'] = $fromDB['fathername'];
        $this->name['prefix'] = $fromDB['prefix'];
        $this->name['suffix'] = $fromDB['suffix'];
        #Populate dates
        $this->dates['registered'] = $fromDB['registered'];
        $this->dates['updated'] = $fromDB['updated'];
        $this->dates['birthday'] = $fromDB['birthday'];
        #Populate parent details
        $this->parent['id'] = $fromDB['parentid'];
        $this->parent['name'] = $fromDB['parentname'];
        #Cleanup the array
        unset($fromDB['parentid'], $fromDB['parentname'], $fromDB['firstname'], $fromDB['lastname'], $fromDB['middlename'], $fromDB['fathername'], $fromDB['prefix'], $fromDB['suffix'], $fromDB['registered'], $fromDB['updated'], $fromDB['birthday']);
        #Populate the rest properties
        $this->arrayToProperties($fromDB);
    }

    public function getEmails(): array
    {
        try {
            return $this->dbController->selectAll('SELECT `email`, `subscribed`, `activation` FROM `'.self::dbPrefix.'user_to_email` WHERE `userid`=:userid ORDER BY `email`;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }

    public function getAvatars(): array
    {
        try {
            return $this->dbController->selectAll('SELECT `url`, `current` FROM `'.self::dbPrefix.'user_to_avatar` WHERE `userid`=:userid;', [':userid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return [];
        }
    }
}
