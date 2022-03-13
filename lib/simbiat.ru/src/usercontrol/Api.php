<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

class Api extends \Simbiat\Abstracts\Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['login' => 'Login to the platform', 'register' => 'Register on the platform', 'remind' => 'Reset the password'];

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
            #Cache security
            $security = new Security();
            #Check CSRF
            if (!$security->antiCSRF(exit: false)) {
                return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
            }
            switch ($_POST['signinup']['type']) {
                #Login
                case 'login':
                    break;
                #New user
                case 'register':
                    return (new Signinup)->register();
                #Reminder
                case 'remind':
                    break;
                default:
                    return ['http_error' => 400, 'reason' => 'Unsupported action type provided'];
            }
            return ['response' => true];
        } else {
            return ['http_error' => 400, 'reason' => 'No data provided'];
        }
    }
}
