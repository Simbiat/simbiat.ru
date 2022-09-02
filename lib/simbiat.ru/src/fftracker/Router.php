<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['search', 'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'crossworldlinkshells', 'achievements', 'statistics', 'crests', 'track',];
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
        if (!empty($path[1])) {
            return match ($path[0]) {
                'characters' => (new Pages\Character)->get(array_slice($path, 1)),
                'freecompanies' => (new Pages\FreeCompany)->get(array_slice($path, 1)),
                'pvpteams' => (new Pages\PvPTeam)->get(array_slice($path, 1)),
                'linkshells' => (new Pages\Linkshell)->get(array_slice($path, 1)),
                'crossworld_linkshells', 'crossworldlinkshells' => (new Pages\CrossworldLinkshell)->get(array_slice($path, 1)),
                'achievements' => (new Pages\Achievement)->get(array_slice($path, 1)),
                'statistics' => (new Pages\Statistics)->get(array_slice($path, 1)),
                'crests' => (new Pages\Crests)->get(array_slice($path, 1)),
                default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
            };
        } else {
            return match ($path[0]) {
                'search' => (new Pages\Search)->get(array_slice($path, 1)),
                'track' => (new Pages\Track)->get(array_slice($path, 1)),
                'crests' => (new Pages\Crests)->get(array_slice($path, 1)),
                'statistics' => (new Pages\Statistics)->get(array_slice($path, 1)),
                'characters', 'freecompanies', 'pvpteams', 'linkshells', 'achievements' => (new Pages\Listing)->get($path),
                default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
            };
        }
    }
}
