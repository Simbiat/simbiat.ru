<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Api\Upload;

use function array_slice;

class Api extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $sub_routes = [
        'fftracker', 'bictracker', 'uc', 'upload', 'talks',
    ];
    #Description of the nodes (need to be in the same order)
    protected array $routes_description = [
        'Endpoints related to Final Fantasy XIV Tracker',
        'Endpoints related to BIC Tracker',
        'Endpoints for user registration, login, password reset and other actions for user editing',
        'Endpoint for file upload',
        'Endpoint for managing forums',
    ];
    #Flag to indicate that this is a top level node (false by default)
    protected bool $top_level = true;
    
    /**
     * This is an actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        return match($path[0]) {
            'fftracker' => new Api\FFTracker()->route(array_slice($path, 1)),
            'bictracker' => new Api\BICTracker()->route(array_slice($path, 1)),
            'uc' => new Api\UserControl()->route(array_slice($path, 1)),
            'talks' => new Api\Talks()->route(array_slice($path, 1)),
            #Upload does not require any further paths
            'upload' => new Upload()->route([]),
        };
    }
}
