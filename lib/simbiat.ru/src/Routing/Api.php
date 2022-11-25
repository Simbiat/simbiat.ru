<?php
declare(strict_types=1);
namespace Simbiat\Routing;

use Simbiat\Abstracts;
use Simbiat\bictracker;
use Simbiat\fftracker;
use Simbiat\Upload;
use Simbiat\usercontrol;

class Api extends Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'fftracker', 'bictracker', 'uc', 'upload', 'talks',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Endpoints related to Final Fantasy XIV Tracker',
        'Endpoints related to BIC Tracker',
        'Endpoints for user registration, login, password reset and other actions for user editing',
        'Endpoint for file upload',
        'Endpoint for managing forums',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = true;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'fftracker' => (new fftracker\Api)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Api)->route(array_slice($path, 1)),
            'uc' => (new usercontrol\Api)->route(array_slice($path, 1)),
            'talks' => (new \Simbiat\Talks\Api)->route(array_slice($path, 1)),
            #Upload does not require any further paths
            'upload' => (new Upload)->route([]),
        };
    }
}
