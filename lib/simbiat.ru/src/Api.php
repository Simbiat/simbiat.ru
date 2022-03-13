<?php
declare(strict_types=1);
namespace Simbiat;

class Api extends Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'fftracker', 'bictracker', 'uc',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'API endpoints related to Final Fantasy XIV Tracker',
        'API endpoints related to BIC Tracker',
        'API endpoint for user registration, login and password reset',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = true;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'fftracker' => (new fftracker\Api)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Api)->route(array_slice($path, 1)),
            'uc' => (new usercontrol\Api)->route(array_slice($path, 1)),
        };
    }
}
