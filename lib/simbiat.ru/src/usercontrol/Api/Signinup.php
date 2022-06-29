<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\usercontrol\Common;

class Signinup extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['login' => 'Login to the platform',
                                'register' => 'Register on the platform',
                                'remind' => 'Reset the password',
                                'logout' => 'Logout from the system',
    ];
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;

    use Common;

    protected function genData(array $path): array
    {
        if (!empty($_POST['signinup'])) {
            #Processing is based on type, so if it's empty - something is wrong
            if (empty($_POST['signinup']['type'])) {
                return ['http_error' => 400, 'reason' => 'No action type provided'];
            }
            if ($path[0] !== $_POST['signinup']['type']) {
                return ['http_error' => 400, 'reason' => 'Action type does not match the verb'];
            }
            return match ($_POST['signinup']['type']) {
                'login' => (new \Simbiat\usercontrol\Signinup)->login(),
                'register' => (new \Simbiat\usercontrol\Signinup)->register(),
                'remind' => (new \Simbiat\usercontrol\Signinup)->remind(),
                'logout' => (new \Simbiat\usercontrol\Signinup)->logout(),
                default => ['http_error' => 400, 'reason' => 'Unsupported action type provided'],
            };
        } else {
            return ['http_error' => 400, 'reason' => 'No data provided'];
        }
    }
}
