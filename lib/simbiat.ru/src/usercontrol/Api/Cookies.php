<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\usercontrol\User;

class Cookies extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['DELETE' => 'delete'];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['delete' => 'Delete a cookie'];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;

    protected function genData(array $path): array
    {
        return ['response' => (new User($_SESSION['userid']))->deleteCookie()];
    }
}
