<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker;

use function array_slice;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['search', 'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'crossworldlinkshells', 'achievements', 'statistics', 'crests', 'track', 'points',
        #legacy singular nodes
        'character', 'freecompany', 'pvpteam', 'linkshell', 'crossworld_linkshell', 'crossworldlinkshell', 'achievement'];
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
            'search' => (new \Simbiat\Website\fftracker\Pages\Search())->get(array_slice($path, 1)),
            'track' => (new \Simbiat\Website\fftracker\Pages\Track())->get(array_slice($path, 1)),
            'crests' => (new \Simbiat\Website\fftracker\Pages\Crests())->get(array_slice($path, 1)),
            'statistics' => (new \Simbiat\Website\fftracker\Pages\Statistics())->route(array_slice($path, 1)),
            'characters' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\Character())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'freecompanies' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\FreeCompany())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'pvpteams' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\PvPTeam())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'linkshells' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\Linkshell())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'crossworld_linkshells' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\CrossworldLinkshell())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'achievements' => (!empty($path[1]) ? (new \Simbiat\Website\fftracker\Pages\Achievement())->get(array_slice($path, 1)) : (new \Simbiat\Website\fftracker\Pages\Listing())->get($path)),
            'points' => (new \Simbiat\Website\fftracker\Pages\Listing())->get($path),
            #Redirects
            'character' => (new \Simbiat\Website\fftracker\Redirects\Character())->get(array_slice($path, 1)),
            'freecompany' => (new \Simbiat\Website\fftracker\Redirects\FreeCompany())->get(array_slice($path, 1)),
            'pvpteam' => (new \Simbiat\Website\fftracker\Redirects\PvPTeam())->get(array_slice($path, 1)),
            'linkshell' => (new \Simbiat\Website\fftracker\Redirects\Linkshell())->get(array_slice($path, 1)),
            'crossworld_linkshell', 'crossworldlinkshell', 'crossworldlinkshells' => (new \Simbiat\Website\fftracker\Redirects\CrossworldLinkshell())->get(array_slice($path, 1)),
            'achievement' => (new \Simbiat\Website\fftracker\Redirects\Achievement())->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
