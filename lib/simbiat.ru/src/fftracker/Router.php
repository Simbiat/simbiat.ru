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
    protected string $ogimage = '/ogimages/fftracker.png';
    protected string $serviceName = 'fftracker';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'search' => (new Pages\Search)->get(array_slice($path, 1)),
            'track' => (new Pages\Track)->get(array_slice($path, 1)),
            'crests' => (new Pages\Crests)->get(array_slice($path, 1)),
            'statistics' => (new Pages\Statistics)->get(array_slice($path, 1)),
            'characters' => (!empty($path[1]) ? (new Pages\Character)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            'freecompanies' => (!empty($path[1]) ? (new Pages\FreeCompany)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            'pvpteams' => (!empty($path[1]) ? (new Pages\PvPTeam)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            'linkshells' => (!empty($path[1]) ? (new Pages\Linkshell)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            'crossworld_linkshells', 'crossworldlinkshells' => (!empty($path[1]) ? (new Pages\CrossworldLinkshell)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            'achievements' => (!empty($path[1]) ? (new Pages\Achievement)->get(array_slice($path, 1)) : (new Pages\Listing)->get($path)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
