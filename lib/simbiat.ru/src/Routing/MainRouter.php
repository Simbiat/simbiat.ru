<?php
declare(strict_types=1);
namespace Simbiat\Routing;

use Simbiat\About;
use Simbiat\About\Pages\Homepage;
use Simbiat\Abstracts;
use Simbiat\bictracker;
use Simbiat\Config\Talks;
use Simbiat\Tests\Router;
use Simbiat\Feeds;
use Simbiat\fftracker;
use Simbiat\Sitemap;
use Simbiat\usercontrol;
use Simbiat\usercontrol\User;

class MainRouter extends Abstracts\Router
{
    #Supported
    protected array $subRoutes = [
        #Empty string implies homepage
        '',
        'api',
        'tests',
        #Forums, blogs, etc.
        'talks',
        #Pages routing
        'about', 'fftracker', 'bictracker', 'uc', 'tests',
        #Simple pages
        'simplepages',
        #Games
        'games',
        #Feeds
        'sitemap', 'rss', 'atom',
        #Errors
        'error', 'errors', 'httperror', 'httperrors'
    ];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/', 'name'=>'Home page'],
    ];

    /**
     * @throws \Exception
     */
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'api' => array_merge(['template_override' => 'api.twig'], (new Api)->route(array_slice($path, 1))),
            #Forum/Articles
            'talks' => (new \Simbiat\Talks\Router())->route(array_slice($path, 1)),
            #Pages routing
            'about' => (new About\Router)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Router)->route(array_slice($path, 1)),
            'fftracker' => (new fftracker\Router)->route(array_slice($path, 1)),
            'uc' => (new usercontrol\Router)->route(array_slice($path, 1)),
            'tests' => (new Router)->route(array_slice($path, 1)),
            #Simple pages
            'simplepages' => (new \Simbiat\SimplePages\Router)->route(array_slice($path, 1)),
            #Games
            'games' => (new \Simbiat\Games\Router)->route(array_slice($path, 1)),
            #Feeds
            'sitemap' => (new Sitemap\Router)->route(array_slice($path, 1)),
            'rss', 'atom' => (new Feeds)->uriParse($path),
            #Errors
            'error', 'errors', 'httperror', 'httperrors' => $this->error(array_slice($path, 1)),
            '' => $this->homepage(),
            default => $this->error(['404']),
        };
    }

    #Temporary function for landing page
    private function homepage(): array
    {
        return (new Homepage)->get([]);
    }

    #Function to help route error pages on frontend
    private function error(array $uri): array {
        if (empty($uri[0]) || !in_array(intval($uri[0]), [300, 301, 302, 303, 305, 307, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 500, 501, 502, 503, 504, 505])) {
            $outputArray['http_error'] = 404;
        } else {
            $outputArray['http_error'] = intval($uri[0]);
        }
        $outputArray['suggested_link'] = '/'.dirname($_SERVER['REQUEST_URI'] ?? '');
        $outputArray['error_page'] = true;
        return $outputArray;
    }
}
