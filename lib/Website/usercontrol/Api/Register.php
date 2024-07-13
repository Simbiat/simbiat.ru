<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol\Api;

use GeoIp2\Database\Reader;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Config;
use Simbiat\Website\HomePage;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;
use Simbiat\Website\usercontrol\User;

class Register extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;

    protected function genData(array $path): array
    {
        #Validating data
        if (empty($_POST['signinup']['username'])) {
            return ['http_error' => 400, 'reason' => 'No username provided'];
        } else {
            $user = new User();
        }
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        } else {
            $email = (new Email($_POST['signinup']['email']));
        }
        if (empty($_POST['signinup']['password'])) {
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        if (mb_strlen($_POST['signinup']['password'], 'UTF-8') < 8) {
            return ['http_error' => 400, 'reason' => 'Password is shorter than 8 symbols'];
        }
        #Get timezone
        $timezone = $_POST['signinup']['timezone'] ?? 'UTC';
        if (!in_array($timezone, timezone_identifiers_list())) {
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
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Check if registration is enabled
        if (boolval(HomePage::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'registration\'')) === false) {
            return ['http_error' => 503, 'reason' => 'Registration is currently disabled'];
        }
        #Generate password and activation strings
        $password = Security::passHash($_POST['signinup']['password']);
        $activation = Security::genToken();
        $ff_token = Security::genToken();
        #Try to read country and city for IP
        try {
            $geoIp = (new Reader(Config::$geoip.'GeoLite2-City.mmdb'))->city($_SESSION['IP']);
        } catch (\Throwable) {
            #Do nothing, not critical
        }
        try {
            $queries = [
                #Insert to main database
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
                #Insert into groups table
                [
                    'INSERT INTO `uc__user_to_group` (`userid`, `groupid`) VALUES ((SELECT `userid` FROM `uc__users` WHERE `username`=:username), 2)',
                    [
                        ':username' => $_POST['signinup']['username'],
                    ]
                ],
            ];
            HomePage::$dbController->query($queries);
            $email->confirm($_POST['signinup']['username'], $activation);
            return $user->login(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Registration failed'];
        }
    }
}
