<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['search', 'characters', 'freecompanies', 'pvpteams', 'linkshells', 'achievements'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/fftracker/', 'name'=>'FFXIV Tracker']
    ];
    protected string $title = 'Final Fantasy XIV Tracker';
    protected string $h1 = 'Final Fantasy XIV Tracker';
    protected string $ogdesc = 'Tracker for Final Fantasy XIV entities and respective statistics';
    protected string $serviceName = 'fftracker';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match(strtolower($path[0])) {
            'search' => (new Pages\Search)->get(array_slice($path, 1)),
            'character' => (new Pages\Character)->get(array_slice($path, 1)),
            'characters', 'freecompanies', 'pvpteams', 'linkshells', 'achievements' => (new Pages\Listing)->get($path),
        };
    }
}
