<?php
declare(strict_types = 1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Talks\Posts;
use App\Controller\Api\Talks\Sections;
use App\Controller\Api\Talks\Threads;

class Talks extends \App\Controller\Api\Api
{
    #Supported edges
    protected array $sub_routes = [
        'sections',
        'threads',
        'posts',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routes_description = [
        'Endpoint to manage sections',
        'Endpoint to manage threads',
        'Endpoint to manage posts',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $top_level = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'sections' => (new Sections())->route(\array_slice($path, 1)),
            'threads' => (new Threads())->route(\array_slice($path, 1)),
            'posts' => (new Posts())->route(\array_slice($path, 1)),
        };
    }
}
