<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol\Api;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\HomePage;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;

class Remind extends Api
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
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;

    protected function genData(array $path): array
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
            $credentials = HomePage::$dbController->selectRow('SELECT `uc__users`.`userid`, `uc__users`.`username`, `uc__emails`.`email` FROM `uc__emails` LEFT JOIN `uc__users` on `uc__users`.`userid`=`uc__emails`.`userid` WHERE (`uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail) AND `uc__emails`.`activation` IS NULL ORDER BY `subscribed` DESC LIMIT 1',
                [':mail' => $_POST['signinup']['email']]
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Process only if user was found
        if (!empty($credentials)) {
            try {
                $token = Security::genToken();
                #Write the reset token to DB
                HomePage::$dbController->query('UPDATE `uc__users` SET `pw_reset`=:token WHERE `userid`=:userid', [':userid' => $credentials['userid'], ':token' => Security::passHash($token)]);
                (new Email($credentials['email']))->send('Password Reset', ['token' => $token, 'userid' => $credentials['userid']], $credentials['username']);
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Registration failed'];
            }
        }
        return ['response' => true];
    }
}
