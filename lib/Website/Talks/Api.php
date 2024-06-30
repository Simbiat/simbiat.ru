<?php
declare(strict_types=1);
namespace Simbiat\Website\Talks;

use Simbiat\Website\Talks\Api\Posts;
use Simbiat\Website\Talks\Api\Sections;
use Simbiat\Website\Talks\Api\Threads;

class Api extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'sections',
        'threads',
        'posts',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Endpoint to manage sections',
        'Endpoint to manage threads',
        'Endpoint to manage posts',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'sections' => (new Sections())->route(array_slice($path, 1)),
            'threads' => (new Threads())->route(array_slice($path, 1)),
            'posts' => (new Posts())->route(array_slice($path, 1)),
        };
    }
}
