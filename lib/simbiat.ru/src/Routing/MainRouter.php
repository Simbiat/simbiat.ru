<?php
declare(strict_types=1);
namespace Simbiat\Routing;

use Simbiat\About;
use Simbiat\Abstracts;
use Simbiat\bictracker;
use Simbiat\Tests\Router;
use Simbiat\Feeds;
use Simbiat\fftracker;
use Simbiat\HomePage;
use Simbiat\Sitemap;
use Simbiat\usercontrol;

class MainRouter extends Abstracts\Router
{
    #Supported
    protected array $subRoutes = [
        #Empty string implies homepage
        '',
        'api',
        'tests',
        #Forum/articles
        #'forum', 'blog', 'thread',
        #Pages routing
        'about', 'fftracker', 'bictracker', 'uc', 'tests',
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
            #'forum', 'blog' => (new Show)->forum($path),
            #'thread' => (new Show)->thread($path),
            #Pages routing
            'about' => (new About\Router)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Router)->route(array_slice($path, 1)),
            'fftracker' => (new fftracker\Router)->route(array_slice($path, 1)),
            'uc' => (new usercontrol\Router)->route(array_slice($path, 1)),
            'tests' => (new Router)->route(array_slice($path, 1)),
            #Feeds
            'sitemap' => (new Sitemap\Router)->route(array_slice($path, 1)),
            'rss', 'atom' => (new Feeds)->uriParse($path),
            #Errors
            'error', 'errors', 'httperror', 'httperrors' => $this->error(array_slice($path, 1)),
            '' => $this->landing(),
            default => $this->error(['404']),
        };
    }

    #Temporary function for landing page
    private function landing(): array
    {
        $outputArray = ['h1' => 'Home', 'serviceName' => 'landing',];
        $outputArray['posts'] = HomePage::$dbController->selectAll('SELECT `postid`, `talks__posts`.`created`, `talks__posts`.`text`, `talks__threads`.`name` FROM `talks__posts` LEFT JOIN `talks__threads` on `talks__posts`.`threadid` = `talks__threads`.`threadid` ORDER BY `talks__posts`.`created` DESC LIMIT 10');
        return $outputArray;
    }

    #Function to route help route error pages on frontend
    private function error(array $uri): array {
        if (empty($uri[0]) || preg_match('/\d{3}/', $uri[0]) !== 1) {
            $outputArray['http_error'] = 404;
        } else {
            $outputArray['http_error'] = intval($uri[0]);
        }
        $outputArray['error_page'] = true;
        return $outputArray;
    }
}
