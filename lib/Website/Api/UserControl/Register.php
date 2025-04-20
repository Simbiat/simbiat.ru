<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use GeoIp2\Database\Reader;
use Simbiat\Database\Select;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Config;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;
use Simbiat\Website\usercontrol\User;

/**
 * Handle user registration
 */
class Register extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;
    
    /**
     *
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        #Validating data
        if (empty($_POST['signinup']['username'])) {
            return ['http_error' => 400, 'reason' => 'No username provided'];
        }
        $user = new User();
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        $email = (new Email($_POST['signinup']['email']));
        if (empty($_POST['signinup']['password'])) {
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        if (mb_strlen($_POST['signinup']['password'], 'UTF-8') < 8) {
            return ['http_error' => 400, 'reason' => 'Password is shorter than 8 symbols'];
        }
        #Get timezone
        $timezone = $_POST['signinup']['timezone'] ?? 'UTC';
        if (!\in_array($timezone, timezone_identifiers_list(), true)) {
            $timezone = 'UTC';
        }
        #Check if banned or in use
        if (
            $email->isBad() ||
            $user->bannedName($_POST['signinup']['username']) ||
            $user->usedName($_POST['signinup']['username'])
        ) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (empty(Config::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Check if registration is enabled
        if (!Select::selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'registration\'')) {
            return ['http_error' => 503, 'reason' => 'Registration is currently disabled'];
        }
        #Generate password and activation strings
        $password = Security::passHash($_POST['signinup']['password']);
        $activation = Security::genToken();
        $ff_token = Security::genToken();
        #Try to read country and city for IP
        try {
            $geoIp = new Reader(Config::$geoip.'GeoLite2-City.mmdb')->city($_SESSION['IP']);
        } catch (\Throwable) {
            #Do nothing, not critical
        }
        try {
            $queries = [
                #Insert to the main database
                [
                    'INSERT INTO `uc__users`(`username`, `password`, `ff_token`, `timezone`, `country`, `city`) VALUES (:username, :password, :ff_token, :timezone, :country, :city)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':password' => $password,
                        ':ff_token' => $ff_token,
                        ':timezone' => $timezone,
                        ':country' => $geoIp->country->name ?? '',
                        ':city' => $geoIp->city->name ?? '',
                        ':ip' => $_SESSION['IP'] ?? '',
                    ],
                ],
                #Insert into mails database
                [
                    'INSERT INTO `uc__emails` (`userid`, `email`, `subscribed`, `activation`) VALUES ((SELECT `userid` FROM `uc__users` WHERE `username`=:username), :mail, 1, :activation)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':mail' => $_POST['signinup']['email'],
                        ':activation' => Security::passHash($activation),
                    ]
                ],
                #Insert into group table
                [
                    'INSERT INTO `uc__user_to_group` (`userid`, `groupid`) VALUES ((SELECT `userid` FROM `uc__users` WHERE `username`=:username), :groupid)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':groupid' => [Config::groupsIDs['Unverified'], 'int'],
                    ]
                ],
            ];
            Config::$dbController::query($queries);
            $email->confirm($_POST['signinup']['username'], $activation);
            return $user->login(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Registration failed'];
        }
    }
}
