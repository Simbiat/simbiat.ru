<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\FFXIV\Pages\Achievement;
use Simbiat\FFXIV\Pages\Character;
use Simbiat\FFXIV\Pages\Crests;
use Simbiat\FFXIV\Pages\CrossworldLinkshell;
use Simbiat\FFXIV\Pages\FreeCompany;
use Simbiat\FFXIV\Pages\Linkshell;
use Simbiat\FFXIV\Pages\Listing;
use Simbiat\FFXIV\Pages\PvPTeam;
use Simbiat\FFXIV\Pages\Search;
use Simbiat\FFXIV\Pages\Track;
use Simbiat\Website\Abstracts\Router;
use function array_slice;

class FFTracker extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['search', 'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'crossworldlinkshells', 'achievements', 'statistics', 'crests', 'track', 'points',
        #legacy singular nodes
        'character', 'freecompany', 'pvpteam', 'linkshell', 'crossworld_linkshell', 'crossworldlinkshell', 'achievement'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/', 'name' => 'FFXIV Tracker']
    ];
    protected string $title = 'Final Fantasy XIV Tracker';
    protected string $h1 = 'Final Fantasy XIV Tracker';
    protected string $og_desc = 'Tracker for Final Fantasy XIV entities and respective statistics';
    protected string $og_image = '/ogimages/fftracker.webp';
    protected string $service_name = 'fftracker';
    
    #This is the actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'search' => new Search()->get(array_slice($path, 1)),
            'track' => new Track()->get(array_slice($path, 1)),
            'crests' => new Crests()->get(array_slice($path, 1)),
            'statistics' => new FFStatistics()->route(array_slice($path, 1)),
            'characters' => (!empty($path[1]) ? new Character()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'freecompanies' => (!empty($path[1]) ? new FreeCompany()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'pvpteams' => (!empty($path[1]) ? new PvPTeam()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'linkshells' => (!empty($path[1]) ? new Linkshell()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'crossworld_linkshells' => (!empty($path[1]) ? new CrossworldLinkshell()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'achievements' => (!empty($path[1]) ? new Achievement()->get(array_slice($path, 1)) : new Listing()->get($path)),
            'points' => new Listing()->get($path),
            #Redirects
            'character' => new \Simbiat\Website\Pages\Redirects\FFTracker\Character()->get(array_slice($path, 1)),
            'freecompany' => new \Simbiat\Website\Pages\Redirects\FFTracker\FreeCompany()->get(array_slice($path, 1)),
            'pvpteam' => new \Simbiat\Website\Pages\Redirects\FFTracker\PvPTeam()->get(array_slice($path, 1)),
            'linkshell' => new \Simbiat\Website\Pages\Redirects\FFTracker\Linkshell()->get(array_slice($path, 1)),
            'crossworld_linkshell', 'crossworldlinkshell', 'crossworldlinkshells' => new \Simbiat\Website\Pages\Redirects\FFTracker\CrossworldLinkshell()->get(array_slice($path, 1)),
            'achievement' => new \Simbiat\Website\Pages\Redirects\FFTracker\Achievement()->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
    }
}
