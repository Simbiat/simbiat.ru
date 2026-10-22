<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\Talks\Post;
use App\Controller\Talks\Section;
use App\Controller\Talks\Thread;
use App\Controller\Talks\User;

final class Talks extends Router
{
    /**
     * @var array<int, string> List supported "paths". Basic ones only, some extra validation may be required further
     */
    #[\Override]
    protected array $sub_routes = ['sections', 'threads', 'posts', 'users', 'edit'];

    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/talks/', 'name' => 'Talks'],
    ];

    #[\Override]
    protected string $title = 'Talks';

    #[\Override]
    protected string $h1 = 'Talks';

    #[\Override]
    protected string $og_desc = 'Talks';

    #[\Override]
    protected string $service_name = 'talks';

    #[\Override]
    protected string $redirect_main = '/talks/sections/';

    /**
     * This is the actual page generation based on further details of the $path
     *
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'sections' => new Section()->get(\array_slice($path, 1)),
            'threads' => new Thread()->get(\array_slice($path, 1)),
            'posts' => new Post()->get(\array_slice($path, 1)),
            'users' => new User()->get(\array_slice($path, 1)),
        };
    }
}
