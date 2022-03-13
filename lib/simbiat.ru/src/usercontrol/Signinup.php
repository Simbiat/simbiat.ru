<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

#Class that deals with user registration, login, logout, etc.
use Simbiat\HomePage;

class Signinup
{
    use Common;

    public function register(): array
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
        #Check if banned
        $checkers = new Checkers;
        if ($checkers->bannedIP() ||
            $checkers->bannedMail($_POST['signinup']['email']) ||
            $checkers->bannedName($_POST['signinup']['username']) ||
            $checkers->usedMail($_POST['signinup']['email']) ||
            $checkers->usedName($_POST['signinup']['username'])
        ) {
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Establish DB
        if (self::$dbController === NULL) {
            if (empty(HomePage::$dbController)) {
                return ['http_error' => 503, 'reason' => 'Database unavailable'];
            } else {
                self::$dbController = HomePage::$dbController;
            }
        }
        #Generate password adn activation strings
        $security = (new Security);
        $password = $security->passHash($_POST['signinup']['password']);
        $activation = $security->genCSRF();
        try {
            $queries = [
                #Insert to main database
                [
                    'INSERT INTO `uc__users`(`username`, `email`, `password`) VALUES (:username, :mail, :password)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':mail' => $_POST['signinup']['email'],
                        ':password' => $password,
                    ],
                ],
                #Insert into mails database
                [
                    'INSERT INTO `uc__user_to_email` (`userid`, `email`, `activation`) VALUES ((SELECT `userid` FROM `uc__users` WHERE `username`=:username), :mail, :activation)',
                    [
                        ':username' => $_POST['signinup']['username'],
                        ':mail' => $_POST['signinup']['email'],
                        ':activation' => $activation,
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
            self::$dbController->query($queries);
        } catch (\Throwable) {
            return ['http_error' => 503, 'reason' => 'Registration failed'];
        }
        return ['response' => true];
    }
}
