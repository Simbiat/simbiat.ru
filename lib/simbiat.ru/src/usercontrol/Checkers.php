<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Helpers;
use Simbiat\HomePage;

class Checkers
{
    #Function to check whether IP is banned
    public static function bannedIP(): bool
    {
        #Get IP
        $ip = Helpers::getIP();
        if ($ip === null) {
            #We failed to get any proper IP, something is definitely wrong, protect ourselves
            return true;
        }
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `ip` FROM `ban__ips` WHERE `ip`=:ip', [':ip' => $ip]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether name is banned
    public static function bannedName(string $name): bool
    {
        #Check format
        if (preg_match('/^[\p{L}\d.!#$%&\'*+\/=?_`{|}~\- ^]{1,64}$/ui', $name) !== 1) {
            return false;
        }
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `name` FROM `ban__names` WHERE `name`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether email is banned
    public static function bannedMail(string $mail): bool
    {
        #Validate that string is a mail
        if (filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
            #Not an email, something is wrong, protect ourselves
            return true;
        }
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `mail` FROM `ban__mails` WHERE `mail`=:mail', [':mail' => $mail]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check if mail is already used
    public static function usedMail(string $mail): bool
    {
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `email` FROM `uc__user_to_email` WHERE `email`=:mail', [':mail' => $mail]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check if username is already used
    public static function usedName(string $name): bool
    {
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `username` FROM `uc__users` WHERE `username`=:name', [':name' => $name]);
        } catch (\Throwable) {
            return false;
        }
    }
}
