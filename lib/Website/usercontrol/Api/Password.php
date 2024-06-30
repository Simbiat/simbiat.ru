<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol\Api;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\HomePage;
use Simbiat\Website\usercontrol\User;

class Password extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['PATCH' => ''];
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
        if (!empty($_POST['pass_userid']) && preg_match('/\d+/u', $_POST['pass_userid']) === 1) {
            $id = $_POST['pass_userid'];
        } else {
            if ($_SESSION['userid'] === 1) {
                return ['http_error' => 403, 'reason' => 'Not logged in'];
            } else {
                $id = $_SESSION['userid'];
            }
        }
        if (empty($_POST['current_password']) && empty($_POST['pass_reset'])) {
            return ['http_error' => 400, 'reason' => 'No current password or reset token provided'];
        }
        if (empty($_POST['new_password'])) {
            return ['http_error' => 400, 'reason' => 'New password not provided'];
        }
        if (HomePage::$dbup === false) {
            return ['http_error' => 503, 'reason' => 'Database is not available'];
        }
        $user = (new User($id));
        if (empty($_POST['pass_reset'])) {
            #Get password
            try {
                $password = HomePage::$dbController->selectValue('SELECT `password` FROM `uc__users` WHERE `userid`=:userid',
                    [':userid' => $id]
                );
            } catch (\Throwable) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            if (empty($password)) {
                return ['http_error' => 500, 'reason' => 'Failed to get credentials from database'];
            }
            #Validate current password
            if ($user->passValid($_POST['current_password'], $password) === false) {
                return ['http_error' => 403, 'reason' => 'Bad password'];
            }
        } else {
            #Get activation code
            try {
                $pwReset = HomePage::$dbController->selectValue('SELECT `pw_reset` FROM `uc__users` WHERE `userid`=:userid',
                    [':userid' => $id]
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
        @session_regenerate_id(true);
        #Change password
        if ($user->passChange($_POST['new_password'])) {
            return ['response' => true];
        } else {
            return ['http_error' => 500, 'reason' => 'Failed to update password'];
        }
    }
}
