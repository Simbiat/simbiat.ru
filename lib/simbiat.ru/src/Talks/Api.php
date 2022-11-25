<?php
declare(strict_types=1);
namespace Simbiat\Talks;

use Simbiat\Talks\Api\Posts;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'posts',
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Endpoint to manage posts',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'posts' => (new Posts())->route(array_slice($path, 1)),
        };
    }
}
