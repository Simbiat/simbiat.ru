<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\HomePage;
use Simbiat\usercontrol\Common;
use Simbiat\usercontrol\Security;

class Password extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => '', 'PATCH' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;

    use Common;

    protected function genData(array $path): array
    {
        if (!empty($_POST['pass_userid']) && preg_match('/\d+/u', $_POST['pass_userid']) === 1) {
            $id = $_POST['pass_userid'];
        } else {
            if (empty($_SESSION['userid'])) {
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
        #Cache DB controller, if not done already
        if (self::$dbController === NULL) {
            self::$dbController = HomePage::$dbController;
        }
        #Cache Security object
        $security = new Security();
        if (empty($_POST['pass_reset'])) {
            #Get password
            try {
                $password = self::$dbController->selectValue('SELECT `password` FROM `uc__users` WHERE `userid`=:userid',
                    [':userid' => $id]
                );
            } catch (\Throwable) {
                return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
            }
            if (empty($password)) {
                return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
            }
            #Validate current password
            if ($security->passValid($id, $_POST['current_password'], $password) === false) {
                return ['http_error' => 403, 'reason' => 'Bad password'];
            }
        } else {
            #Get activation code
            try {
                $pwReset = self::$dbController->selectValue('SELECT `pw_reset` FROM `uc__users` WHERE `userid`=:userid',
                    [':userid' => $id]
                );
            } catch (\Throwable) {
                return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
            }
            if (empty($pwReset)) {
                return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
            }
            #Validate token
            if (!password_verify($_POST['pass_reset'], $pwReset)) {
                return ['http_error' => 403, 'reason' => 'Bad password reset token'];
            }
        }
        #Change password
        if ($security->passChange($id, $_POST['new_password'])) {
            return ['response' => true];
        } else {
            return ['http_error' => 503, 'reason' => 'Failed to update password'];
        }
    }
}
