<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'signinup', 'emails',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'User login anc creation/deletion handling',
        'Emails management',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'signinup' => (new Signinup)->route(array_slice($path, 1)),
            'emails' => (new Emails)->route(array_slice($path, 1)),
        };
    }
}
