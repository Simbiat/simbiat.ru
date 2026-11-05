<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\BIC\Bic;
use App\Controller\BIC\Keying;
use App\Controller\BIC\Listing;
use App\Controller\BIC\Search;

final class BICTracker extends Router
{
    /**
     * Current breadcrumb for navigation.
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/bictracker/', 'name' => 'БИК Трекер'],
    ];

    #[\Override]
    protected string $h1 = 'БИК Трекер';

    #[\Override]
    protected string $og_desc = 'Трекер БИК предоставляемых Центральным Банком Российской Федерации';

    #[\Override]
    protected string $og_image = '/ogimages/bictracker.webp';

    #[\Override]
    protected string $service_name = 'bictracker';

    /**
     * @var array<int, string> List supported "paths". Basic ones only, some extra validation may be required further
     */
    #[\Override]
    protected array $sub_routes = ['keying', 'search', 'bics', 'openbics', 'closedbics', 'bic'];

    #[\Override]
    protected string $title = 'БИК Трекер';

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
            'bics' => new Bic()->get(\array_slice($path, 1)),
            'search' => new Search()->get(\array_slice($path, 1)),
            'keying' => new Keying()->get(\array_slice($path, 1)),
            'openbics', 'closedbics' => new Listing()->get($path),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
        // }
    }
}
