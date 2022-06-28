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
    protected bool $authenticationNeeded = true;

    use Common;

    protected function genData(array $path): array
    {
        if (empty($_SESSION['userid'])) {
            return ['http_error' => 403, 'reason' => 'Not logged in'];
        }
        if (empty($_POST['current_password'])) {
            return ['http_error' => 400, 'reason' => 'Current password not provided'];
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
        #Get password
        try {
            $password = self::$dbController->selectValue('SELECT `password` FROM `uc__users` WHERE `userid`=:userid',
                [':userid' => $_SESSION['userid']]
            );
        } catch (\Throwable) {
            return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
        }
        if (empty($password)) {
            return ['http_error' => 503, 'reason' => 'Failed to get credentials from database'];
        }
        #Cache Security object
        $security = new Security();
        #Validate current password
        if ($security->passValid($_SESSION['userid'], $_POST['current_password'], $password) === false) {
            return ['http_error' => 403, 'reason' => 'Bad password'];
        }
        #Change password
        if ($security->passChange($_SESSION['userid'], $_POST['new_password'])) {
            return ['response' => true];
        } else {
            return ['http_error' => 503, 'reason' => 'Failed to update password'];
        }
    }
}
