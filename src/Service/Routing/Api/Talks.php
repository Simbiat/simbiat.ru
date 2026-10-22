<?php

declare(strict_types=1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Api;
use App\Controller\Api\Talks\Posts;
use App\Controller\Api\Talks\Sections;
use App\Controller\Api\Talks\Threads;

final class Talks extends Api
{
    /**
     * @var array<int, string> Supported edges
     */
    #[\Override]
    protected array $sub_routes = [
        'sections',
        'threads',
        'posts',
    ];

    /**
     * @var array<int, string> Description of the nodes (need to be in the same order)
     */
    #[\Override]
    protected array $routes_description = [
        'Endpoint to manage sections',
        'Endpoint to manage threads',
        'Endpoint to manage posts',
    ];

    protected function genData(array $path): array
    {
        return match ($path[0]) {
            'sections' => new Sections()->route(\array_slice($path, 1)),
            'threads' => new Threads()->route(\array_slice($path, 1)),
            'posts' => new Posts()->route(\array_slice($path, 1)),
        };
    }
}
