<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\BICTracker\Bic;
use Simbiat\Website\Pages\BICTracker\Keying;
use Simbiat\Website\Pages\BICTracker\Listing;
use Simbiat\Website\Pages\BICTracker\Search;
use Simbiat\Website\Redirects\BICTracker\ToBics;
use function array_slice;

class BICTracker extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['keying', 'search', 'bics', 'openbics', 'closedbics', 'bic'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/', 'name' => 'БИК Трекер']
    ];
    protected string $title = 'БИК Трекер';
    protected string $h1 = 'БИК Трекер';
    protected string $og_desc = 'Трекер БИК предоставляемых Центральным Банком Российской Федерации';
    protected string $og_image = '/ogimages/bictracker.png';
    protected string $service_name = 'bictracker';
    
    #This is the actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'bics' => new Bic()->get(array_slice($path, 1)),
            'bic' => new ToBics()->get(array_slice($path, 1)),
            'search' => new Search()->get(array_slice($path, 1)),
            'keying' => new Keying()->get(array_slice($path, 1)),
            'openbics', 'closedbics' => new Listing()->get($path),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
        #}
    }
}
