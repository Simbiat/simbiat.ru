<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

#Class that deals with user registration, login, logout, etc.
use Simbiat\Config\Common;
use Simbiat\HomePage;
use Simbiat\Security;

class Signinup
{
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
        #Get timezone
        $timezone = $_POST['signinup']['timezone'] ?? 'UTC';
        if (!in_array($timezone, timezone_identifiers_list())) {
            $timezone = 'UTC';
        }
        #Check if banned or in use
        $checkers = new Checkers;
        if ($checkers->bannedIP() ||
            $checkers->bannedMail($_POST['signinup']['email']) ||
            $checkers->bannedName($_POST['signinup']['username']) ||
            $checkers->usedMail($_POST['signinup']['email']) ||
            $checkers->usedName($_POST['signinup']['username'])
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
            (new Emails)->activationMail($_POST['signinup']['email'], $_POST['signinup']['username'], $activation);
            return $this->login(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Registration failed'];
        }
    }

    public function login(bool $afterRegister = false): array
    {
        #Validating data
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        if (empty($_POST['signinup']['password'])) {
            return ['http_error' => 400, 'reason' => 'No password provided'];
        }
        #Check if banned
        $checkers = new Checkers;
        if ($checkers->bannedIP() ||
            $checkers->bannedMail($_POST['signinup']['email']) ||
            $checkers->bannedName($_POST['signinup']['email'])
        ) {
            return ['http_error' => 403, 'reason' => 'Prohibited credentials provided'];
        }
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get password of the user, while also checking if it exists
        try {
            $credentials = HomePage::$dbController->selectRow('SELECT `uc__users`.`userid`, `username`, `password`, `strikes` FROM `uc__user_to_email` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__user_to_email`.`userid` WHERE `uc__user_to_email`.`email`=:mail',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Check if password is set (means that user does exist)
        if (empty($credentials['password'])) {
            return ['http_error' => 403, 'reason' => 'No user found'];
        }
        #Check for strikes
        if ($credentials['strikes'] >= 5) {
            return ['http_error' => 403, 'reason' => 'Too many failed login attempts. Try password reset.'];
        }
        #Check the password
        if (Security::passValid($credentials['userid'], $_POST['signinup']['password'], $credentials['password']) === false) {
            return ['http_error' => 403, 'reason' => 'Bad password'];
        }
        #Add username and userid to session
        $_SESSION['username'] = $credentials['username'];
        $_SESSION['userid'] = $credentials['userid'];
        #Set cookie if we have "rememberme" checked
        if (!empty($_POST['signinup']['rememberme'])) {
            $this->rememberMe();
        }
        session_regenerate_id(true);
        if ($afterRegister) {
            return ['status' => 201, 'response' => true];
        } else {
            return ['response' => true];
        }
    }

    public function cookieLogin(): array
    {
        $cookieName = str_replace(['.', ' '], '_', 'rememberme_'.Common::$http_host);
        #Check if cookie exists
        if (empty($_COOKIE[$cookieName])) {
            return [];
        }
        #Validate cookie
        try {
            #Decode data
            $data = json_decode($_COOKIE[$cookieName], true);
            if (empty($data['id']) || empty($data['pass'])) {
                #No expected data found
                return [];
            }
            #Cache Security object
            $data['id'] = Security::decrypt($data['id']);
            #Check DB
            if (HomePage::$dbController !== null) {
                #Get user data
                $savedData = HomePage::$dbController->selectRow('SELECT `uc__cookies`.`userid`, `validator`, `username` FROM `uc__cookies` LEFT JOIN `uc__users` ON `uc__cookies`.`userid`=`uc__users`.`userid` WHERE `cookieid`=:id',
                    [':id' => $data['id']]
                );
                if (empty($savedData) || empty($savedData['validator'])) {
                    #No cookie found or no password present
                    return [];
                }
                #Validate cookie password
                if (hash('sha3-512', $data['pass']) !== $savedData['validator']) {
                    #Wrong password
                    return [];
                }
                #Reset strikes if any
                Security::resetStrikes($savedData['userid']);
                #Update cookie
                $this->rememberMe($data['id'], $savedData['userid']);
                return ['userid' => $savedData['userid'], 'username' => $savedData['username']];
            } else {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }
    }

    public function logout(): array
    {
        #Remove rememberme cookie
        #From browser
        setcookie('rememberme_'.Common::$http_host, '', ['expires' => 1, 'path' => '/', 'domain' => Common::$http_host, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
        #From DB
        try {
            if (HomePage::$dbController !== null && !empty($_SESSION['userid'])) {
                HomePage::$dbController->query('DELETE FROM `uc__cookies` WHERE `userid`=:id', [':id' => [$_SESSION['userid'], 'int']]);
            }
        } catch (\Throwable) {
            #Do nothing
        }
        #Clean session (affects $_SESSION only)
        session_unset();
        #Destroy session (destroys it storage)
        return ['response' => session_destroy()];
    }

    #Setting cookie for remembering user
    private function rememberMe(string $id = '', null|string|int $userid = null): void
    {
        try {
            #Generate cookie ID
            if (empty($id)) {
                $id = bin2hex(random_bytes(64));
            }
            #Generate cookie password
            $pass = bin2hex(random_bytes(128));
            #Write cookie data to DB
            if (HomePage::$dbController === null) {
                #If we can't write to DB for some reason - do not share any data with client
                return;
            }
            if (HomePage::$dbController !== null && (!empty($_SESSION['userid']) || !empty($userid))) {
                HomePage::$dbController->query('INSERT INTO `uc__cookies` (`cookieid`, `validator`, `userid`) VALUES (:cookie, :pass, :id) ON DUPLICATE KEY UPDATE `validator`=:pass, `time`=CURRENT_TIMESTAMP();',
                    [
                        ':cookie' => $id,
                        ':pass' => hash('sha3-512', $pass),
                        ':id' => $userid ?? [$_SESSION['userid'], 'int'],
                    ]
                );
            } else {
                return;
            }
            #Set options
            $options = ['expires' => time()+60*60*24*30, 'path' => '/', 'domain' => Common::$http_host, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict'];
            #Set cookie value
            $value = json_encode(['id' => Security::encrypt($id), 'pass'=> $pass],JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
            setcookie('rememberme_'.Common::$http_host, $value, $options);
        } catch (\Throwable) {
            #Do nothing, since not critical
        }
    }

    public function remind(): array
    {
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email/name provided'];
        }
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get password of the user, while also checking if it exists
        try {
            $credentials = HomePage::$dbController->selectRow('SELECT `uc__users`.`userid`, `uc__users`.`username`, `uc__user_to_email`.`email` FROM `uc__user_to_email` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__user_to_email`.`userid` WHERE (`uc__users`.`username`=:mail OR `uc__user_to_email`.`email`=:mail) AND `uc__user_to_email`.`activation` IS NULL ORDER BY `subscribed` DESC LIMIT 1',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Process only if user was found
        if (!empty($credentials)) {
            try {
                $token = Security::genCSRF();
                #Write the reset token to DB
                HomePage::$dbController->query('UPDATE `uc__users` SET `pw_reset`=:token WHERE `userid`=:userid', [':userid' => $credentials['userid'], ':token' => Security::passHash($token)]);
                (new Emails)->sendMail($credentials['email'], 'Password Reset', ['token' => $token, 'userid' => $credentials['userid']], $credentials['username']);
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Registration failed'];
            }
        }
        return ['response' => true];
    }
}
