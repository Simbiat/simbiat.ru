<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\DeviceDetector;

final class SimplePages extends Router
{
    /**
     * Current breadcrumb for navigation.
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/simplepages/', 'name' => 'Simple Pages'],
    ];

    #[\Override]
    protected string $h1 = 'Simple Pages';

    #[\Override]
    protected string $og_desc = 'Various simple pages hosted by Simbiat Software';

    #[\Override]
    protected string $service_name = 'simplepages';

    /**
     * @var array<int, string> List supported "paths". Basic ones only, some extra validation may be required further
     */
    #[\Override]
    protected array $sub_routes = ['devicedetector'];

    #[\Override]
    protected string $title = 'Simple Pages';

    /**
     * This is the actual page generation based on further details of the $path.
     *
     * @param array $path
     *
     * @return array
     */
    #[\Override]
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'devicedetector' => new DeviceDetector()->get(\array_slice($path, 1)),
        };
    }
}
