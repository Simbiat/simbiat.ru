<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\Games\DDEN;
use App\Controller\Games\Jiangshi;
use App\Controller\Games\RadicalResonance;

final class Games extends Router
{
    // List supported "paths". Basic ones only, some extra validation may be required further
    #[\Override]
    protected array $sub_routes = ['jiangshi', 'dden', 'radicalresonance', 'anti'];

    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/games/', 'name' => 'Games'],
    ];

    #[\Override]
    protected string $title = 'Games';

    #[\Override]
    protected string $h1 = 'Games';

    #[\Override]
    protected string $og_desc = 'Games';

    #[\Override]
    protected string $service_name = 'games';

    // This is the actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'jiangshi' => new Jiangshi()->get(\array_slice($path, 1)),
            'dden' => new DDEN()->get(\array_slice($path, 1)),
            'radicalresonance' => new RadicalResonance()->get(\array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
    }
}
