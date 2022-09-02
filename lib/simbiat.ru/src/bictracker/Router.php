<?php
declare(strict_types=1);
namespace Simbiat\bictracker;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['keying', 'search', 'bics', 'openbics', 'closedbics'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/bictracker/', 'name'=>'БИК Трекер']
    ];
    protected string $title = 'БИК Трекер';
    protected string $h1 = 'БИК Трекер';
    protected string $ogdesc = 'Трекер БИК предоставляемых Центральным Банком Российской Федерации';
    protected string $serviceName = 'bictracker';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        if (!empty($path[1])) {
            return match ($path[0]) {
                'bics' => (new Pages\Bic)->get(array_slice($path, 1)),
                'keying' => (new Pages\Keying)->get(array_slice($path, 1)),
                default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
            };
        } else {
            return match($path[0]) {
                'search' => (new Pages\Search)->get(array_slice($path, 1)),
                'keying' => (new Pages\Keying)->get(array_slice($path, 1)),
                'openbics', 'closedbics' => (new Pages\Listing)->get($path),
                default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
            };
        }
    }
}
