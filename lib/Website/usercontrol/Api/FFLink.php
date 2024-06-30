<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol\Api;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\fftracker\Entities\Character;

class FFLink extends Api
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
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;

    protected function genData(array $path): array
    {
        return (new Character($_POST['characterid'] ?? ''))->linkUser();
    }
}
