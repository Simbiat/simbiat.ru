<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;

/**
 * Password reminder
 */
class Remind extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Flag to indicate need to validate CSRF
    protected bool $csrf = true;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = true;
    
    /**
     * This is the actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        if (empty($_POST['signinup']['email'])) {
            return ['http_error' => 400, 'reason' => 'No email/name provided'];
        }
        #Check DB
        if (Query::$dbh === null) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        #Get the password of the user while also checking if it exists
        try {
            $credentials = Query::query('SELECT `uc__users`.`user_id`, `uc__users`.`username`, `uc__emails`.`email` FROM `uc__emails` LEFT JOIN `uc__users` on `uc__users`.`user_id`=`uc__emails`.`user_id` WHERE (`uc__users`.`username`=:mail OR `uc__emails`.`email`=:mail) AND `uc__emails`.`activation` IS NULL ORDER BY `subscribed` DESC LIMIT 1',
                [':mail' => $_POST['signinup']['email']], return: 'row'
            );
        } catch (\Throwable) {
            $credentials = null;
        }
        #Process only if a user was found
        if (!empty($credentials)) {
            try {
                $token = Security::genToken();
                #Write the reset token to DB
                Query::query('UPDATE `uc__users` SET `password_reset`=:token WHERE `user_id`=:user_id', [':user_id' => $credentials['user_id'], ':token' => Security::passHash($token)]);
                new Email($credentials['email'])->send('Password Reset', ['token' => $token, 'user_id' => $credentials['user_id']], $credentials['username']);
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Registration failed'];
            }
        }
        return ['response' => true];
    }
}
