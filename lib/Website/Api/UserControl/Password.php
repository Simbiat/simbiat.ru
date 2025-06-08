<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Config;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\User;

/**
 * Function to change password through API
 */
class Password extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['PATCH' => ''];
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;
    
    /**
     * @param array $path
     *
     * @return array|true[]
     */
    protected function genData(array $path): array
    {
        if (!empty($_POST['pass_user_id']) && preg_match('/\d+/u', $_POST['pass_user_id']) === 1) {
            $id = $_POST['pass_user_id'];
        } else {
            if ($_SESSION['user_id'] === 1) {
                return ['http_error' => 403, 'reason' => 'Not logged in'];
            }
            $id = $_SESSION['user_id'];
        }
        if (empty($_POST['current_password']) && empty($_POST['pass_reset'])) {
            return ['http_error' => 400, 'reason' => 'No current password or reset token provided'];
        }
        if (empty($_POST['new_password'])) {
            return ['http_error' => 400, 'reason' => 'New password not provided'];
        }
        if (!Config::$dbup) {
            return ['http_error' => 503, 'reason' => 'Database is not available'];
        }
        $user = (new User($id));
        if (empty($_POST['pass_reset'])) {
            #Get password
            try {
                $password = Query::query('SELECT `password` FROM `uc__users` WHERE `user_id`=:user_id',
                    [':user_id' => $id], return: 'value'
                );
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            if (empty($password)) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            #Validate current password
            if (!$user->passValid($_POST['current_password'], $password)) {
                return ['http_error' => 403, 'reason' => 'Bad password'];
            }
        } else {
            #Get activation code
            try {
                $pwReset = Query::query('SELECT `password_reset` FROM `uc__users` WHERE `user_id`=:user_id',
                    [':user_id' => $id], return: 'value'
                );
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            if (empty($pwReset)) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            #Validate token
            if (!password_verify($_POST['pass_reset'], $pwReset)) {
                return ['http_error' => 403, 'reason' => 'Bad password reset token'];
            }
        }
        Security::session_regenerate_id(true);
        #Change password
        if ($user->passChange($_POST['new_password'])) {
            return ['response' => true];
        }
        return ['http_error' => 500, 'reason' => 'Failed to update password'];
    }
}
