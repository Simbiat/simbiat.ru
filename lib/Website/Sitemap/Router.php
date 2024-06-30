<?php
declare(strict_types=1);
namespace Simbiat\Website\Sitemap;

use Simbiat\http20\Headers;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['index', 'fftracker',
        'general', 'bics', 'threads', 'users',
        'ffxiv_characters', 'ffxiv_freecompanies', 'ffxiv_linkshells', 'ffxiv_pvpteams', 'ffxiv_achievements',
    ];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/sitemap/', 'name'=>'Sitemap']
    ];
    protected string $title = 'Sitemap';
    protected string $h1 = 'Sitemap';
    protected string $ogdesc = 'Sitemap';
    protected string $serviceName = 'sitemap';
    #If no path[0] is provided, but we want to show specific page, instead of a stub - redirect to page with this address
    protected string $redirectMain = '/sitemap/index';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        #Send 406 if format is not acceptable
        Headers::notAccept(['application/xml']);
        #Send content type header
        @header('Content-Type: application/xml; charset=utf-8');
        #Ensure path is set, even though it's empty
        if (empty($path[0])) {
            $path[0] = 'index';
        }
        $result = match($path[0]) {
            'general' => (new \Simbiat\Website\Sitemap\Pages\General)->get($path),
            'bics', 'ffxiv_characters', 'ffxiv_freecompanies', 'ffxiv_linkshells', 'ffxiv_pvpteams', 'ffxiv_achievements', 'threads', 'users' => (new \Simbiat\Website\Sitemap\Pages\Countables)->get($path),
            'fftracker' => (new \Simbiat\Website\Sitemap\Pages\FFTracker)->get($path),
            'index' => (new \Simbiat\Website\Sitemap\Pages\Index)->get($path),
        };
        $result['format'] = 'xml';
        $result['template_override'] = 'common/pages/sitemap.twig';
        return $result;
    }
}
