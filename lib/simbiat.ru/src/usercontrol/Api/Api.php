<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'signinup', 'emails', 'password', 'username'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'User login and creation/deletion handling',
        'Emails management',
        'Password change',
        'Username change',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'signinup' => (new Signinup)->route(array_slice($path, 1)),
            'emails' => (new Emails)->route(array_slice($path, 1)),
            'password' => (new Password)->route(array_slice($path, 1)),
            'username' => (new Username)->route(array_slice($path, 1)),
        };
    }
}
