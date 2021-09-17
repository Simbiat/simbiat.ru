<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Database\Controller;

class Bans
{
    #Attach common settings
    use Common;

    public function __construct()
    {
        #Cache DB controller, if not done already
        if (self::$dbController === NULL) {
            self::$dbController = new Controller;
        }
    }

    #Function to check whether IP is banned

    /**
     * @throws \Exception
     */
    public function bannedIP(): bool
    {
        #Get IP
        $ip = $this->getIP();
        if ($ip === NULL) {
            #We failed to get any proper IP, something is definitely wrong, protect ourselves
            return true;
        }
        #Check against DB table
        return self::$dbController->check('SELECT `ip` FROM `uc__bans_ips` WHERE `ip`=:ip', [':ip' => $ip]);
    }

    #Function to check whether name is banned

    /**
     * @throws \Exception
     */
    public function bannedName(string $name): bool
    {
        #Check against DB table
        return self::$dbController->check('SELECT `name` FROM `uc__bans_names` WHERE `name`=:name', [':name' => $name]);
    }

    #Function to check whether email is banned

    /**
     * @throws \Exception
     */
    public function bannedMail(string $mail): bool
    {
        #Validate that string is a mail
        if (filter_var($mail, FILTER_VALIDATE_IP) === false) {
            #Not an email, something is wrong, protect ourselves
            return true;
        }
        #Check against DB table
        return self::$dbController->check('SELECT `mail` FROM `uc__bans_mails` WHERE `mail`=:mail', [':mail' => $mail]);
    }
}
