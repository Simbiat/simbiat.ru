<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\Api\Talks\Contact;
use App\Service\Routing\Api\BICTracker;
use App\Service\Routing\Api\FFTracker;
use App\Service\Routing\Api\Talks;
use App\Service\Routing\Api\UserControl;
use App\Service\Upload;

final class Api extends \App\Controller\Api\Api
{
    // Supported edges
    protected array $sub_routes = [
        'fftracker', 'bictracker', 'uc', 'upload', 'talks', 'contact',
    ];

    // Description of the nodes (need to be in the same order)
    protected array $routes_description = [
        'Endpoints related to Final Fantasy XIV Tracker',
        'Endpoints related to BIC Tracker',
        'Endpoints for user registration, login, password reset and other actions for user editing',
        'Endpoint for file upload',
        'Endpoint for managing forums',
        'Endpoint to submit support requests',
    ];

    // Flag to indicate that this is a top level node (false by default)
    protected bool $top_level = true;

    /**
     * This is an actual API response generation based on further details of the $path
     *
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        return match ($path[0]) {
            'fftracker' => new FFTracker()->route(\array_slice($path, 1)),
            'bictracker' => new BICTracker()->route(\array_slice($path, 1)),
            'uc' => new UserControl()->route(\array_slice($path, 1)),
            'talks' => new Talks()->route(\array_slice($path, 1)),
            // Upload does not require any further paths
            'upload' => new Upload()->route([]),
            // Contact does not require any further paths
            'contact' => new Contact()->route([]),
        };
    }
}
