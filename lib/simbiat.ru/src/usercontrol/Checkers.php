<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\HomePage;

class Checkers
{
    #Attach common settings
    use Common;

    public function __construct()
    {
        #Cache DB controller, if not done already
        if (self::$dbController === null) {
            self::$dbController = HomePage::$dbController;
        }
    }

    #Function to check whether IP is banned
    public function bannedIP(): bool
    {
        #Get IP
        $ip = $this->getIP();
        if ($ip === null) {
            #We failed to get any proper IP, something is definitely wrong, protect ourselves
            return true;
        }
        #Check against DB table
        try {
            return self::$dbController->check('SELECT `ip` FROM `ban__ips` WHERE `ip`=:ip', [':ip' => $ip]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether name is banned
    public function bannedName(string $name): bool
    {
        #Check against DB table
        try {
            return self::$dbController->check('SELECT `name` FROM `ban__names` WHERE `name`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether email is banned
    public function bannedMail(string $mail): bool
    {
        #Validate that string is a mail
        if (filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
            #Not an email, something is wrong, protect ourselves
            return true;
        }
        #Check against DB table
        try {
            return self::$dbController->check('SELECT `mail` FROM `ban__mails` WHERE `mail`=:mail', [':mail' => $mail]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check if mail is already used
    public function usedMail(string $mail): bool
    {
        #Check against DB table
        try {
            return self::$dbController->check('SELECT `email` FROM `uc__user_to_email` WHERE `email`=:mail', [':mail' => $mail]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check if username is already used
    public function usedName(string $name): bool
    {
        #Check against DB table
        try {
            return self::$dbController->check('SELECT `username` FROM `uc__users` WHERE `username`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }
}
