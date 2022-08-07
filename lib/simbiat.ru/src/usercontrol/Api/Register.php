<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\HomePage;
use Simbiat\Security;
use Simbiat\usercontrol\Checkers;
use Simbiat\usercontrol\Emails;
use Simbiat\usercontrol\User;

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
    protected bool $CSRF = true;

    protected function genData(array $path): array
    {
        #Validating data
        if (empty($_POST['signinup']['username'])) {
            return ['http_error' => 400, 'reason' => 'No username provided'];
        }
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
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
        if (Checkers::bannedIP() ||
            Checkers::bannedMail($_POST['signinup']['email']) ||
            Checkers::bannedName($_POST['signinup']['username']) ||
            Checkers::usedMail($_POST['signinup']['email']) ||
            Checkers::usedName($_POST['signinup']['username'])
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
        $activation = Security::genCSRF();
        $ff_token = Security::genCSRF();
        try {
            $queries = [
                #Insert to main database
                [
                    'INSERT INTO `uc__users`(`username`, `password`, `ff_token`, `timezone`, `country`, `city`) VALUES (:username, :password, :ff_token, :timezone, (SELECT `country` FROM `seo__ips` WHERE `ip`=:ip), (SELECT `city` FROM `seo__ips` WHERE `ip`=:ip))',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':password' => $password,
                        ':ff_token' => $ff_token,
                        ':timezone' => $timezone,
                        ':ip' => $_SESSION['IP'] ?? '',
                    ],
                ],
                #Insert into mails database
                [
                    'INSERT INTO `uc__user_to_email` (`userid`, `email`, `subscribed`, `activation`) VALUES ((SELECT `userid` FROM `uc__users` WHERE `username`=:username), :mail, 1, :activation)',
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
            Emails::activationMail($_POST['signinup']['email'], $_POST['signinup']['username'], $activation);
            return (new User)->login(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Registration failed'];
        }
    }
}
