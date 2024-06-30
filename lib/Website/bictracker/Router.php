<?php
declare(strict_types=1);
namespace Simbiat\Website\bictracker;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['keying', 'search', 'bics', 'openbics', 'closedbics', 'bic'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/bictracker/', 'name'=>'БИК Трекер']
    ];
    protected string $title = 'БИК Трекер';
    protected string $h1 = 'БИК Трекер';
    protected string $ogdesc = 'Трекер БИК предоставляемых Центральным Банком Российской Федерации';
    protected string $ogimage = '/ogimages/bictracker.png';
    protected string $serviceName = 'bictracker';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
            return match($path[0]) {
                'bics' => (new \Simbiat\Website\bictracker\Pages\Bic)->get(array_slice($path, 1)),
                'bic' => (new \Simbiat\Website\bictracker\Redirects\ToBics)->get(array_slice($path, 1)),
                'search' => (new \Simbiat\Website\bictracker\Pages\Search)->get(array_slice($path, 1)),
                'keying' => (new \Simbiat\Website\bictracker\Pages\Keying)->get(array_slice($path, 1)),
                'openbics', 'closedbics' => (new \Simbiat\Website\bictracker\Pages\Listing)->get($path),
                default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
            };
        #}
    }
}
