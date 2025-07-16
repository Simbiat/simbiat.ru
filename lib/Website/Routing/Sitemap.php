<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Sitemap\Pages\Countables;
use Simbiat\Website\Sitemap\Pages\General;
use Simbiat\Website\Sitemap\Pages\Index;

class Sitemap extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['index', 'fftracker',
        'general', 'bics', 'threads', 'users',
        'ffxiv_characters', 'ffxiv_freecompanies', 'ffxiv_linkshells', 'ffxiv_pvpteams', 'ffxiv_achievements',
    ];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/sitemap/', 'name' => 'Sitemap']
    ];
    protected string $title = 'Sitemap';
    protected string $h1 = 'Sitemap';
    protected string $ogdesc = 'Sitemap';
    protected string $service_name = 'sitemap';
    #If no path[0] is provided, but we want to show specific page, instead of a stub - redirect to page with this address
    protected string $redirect_main = '/sitemap/index';
    
    #This is the actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        #Send 406 if format is not acceptable
        Headers::notAccept(['application/xml']);
        #Send content type header
        @header('Content-Type: application/xml; charset=utf-8');
        #Ensure the path is set, even though it's empty
        if (empty($path[0])) {
            $path[0] = 'index';
        }
        $result = match($path[0]) {
            'general' => new General()->get($path),
            'bics', 'ffxiv_characters', 'ffxiv_freecompanies', 'ffxiv_linkshells', 'ffxiv_pvpteams', 'ffxiv_achievements', 'threads', 'users' => new Countables()->get($path),
            'fftracker' => new \Simbiat\Website\Sitemap\Pages\FFTracker()->get($path),
            'index' => new Index()->get($path),
        };
        $result['format'] = 'xml';
        $result['template_override'] = 'common/pages/sitemap.twig';
        return $result;
    }
}
