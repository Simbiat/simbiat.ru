<?php
declare(strict_types=1);
namespace Simbiat\bictracker;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['keying', 'search', 'bic', 'open', 'closed'];
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
        return match(strtolower($path[0])) {
            'search' => (new Pages\Search)->get(array_slice($path, 1)),
            'keying' => (new Pages\Keying)->get(array_slice($path, 1)),
            'bic' => (new Pages\Bic)->get(array_slice($path, 1)),
            'open', 'closed' => (new Pages\Listing)->get($path),
        };
    }
}
