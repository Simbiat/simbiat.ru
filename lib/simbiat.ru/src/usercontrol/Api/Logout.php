<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\Config\Common;
use Simbiat\HomePage;
use Simbiat\Security;

class Logout extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;

    protected function genData(array $path): array
    {
        Security::log('Logout', 'Logout');
        #Remove rememberme cookie
        #From browser
        setcookie('rememberme_'.Common::$http_host, '', ['expires' => gmdate('D, d-M-Y H:i:s', time()).' GMT', 'path' => '/', 'domain' => Common::$http_host, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
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
}
