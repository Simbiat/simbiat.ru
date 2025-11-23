<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Feeds;
use Simbiat\Website\Pages\About\Homepage;

use Simbiat\Website\Redirects\BICTracker\Legacy;

use function array_slice;

/**
 * Route web requests to other routers
 */
class MainRouter extends Router
{
    #Supported
    protected array $sub_routes = [
        #Empty string implies homepage
        '',
        'api',
        'tests',
        #Forums, blogs, etc.
        'talks',
        #Pages routing
        'about', 'fftracker', 'bictracker', 'uc', 'bic',
        #Simple pages
        'simplepages',
        #SupOps
        'supops',
        #Games
        'games',
        #Feeds
        'sitemap', 'rss', 'atom',
        #Errors
        'error', 'errors', 'httperror', 'httperrors'
    ];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/', 'name' => 'Home page'],
    ];
    
    /**
     * @throws \Exception
     */
    protected function pageGen(array $path): array
    {
        if ($path[0] === 'sitemap') {
            #We want to handle sitemap links equally regardless of trailing .xml extension
            $path[\array_key_last($path)] = \str_replace('.xml', '', $path[\array_key_last($path)]);
        }
        return match ($path[0]) {
            'api' => \array_merge(['template_override' => 'common/pages/api.twig'], new Api()->route(array_slice($path, 1))),
            #Forum/Articles
            'talks' => new Talks()->route(array_slice($path, 1)),
            #Pages routing
            'about' => new About()->route(array_slice($path, 1)),
            'bictracker' => new BICTracker()->route(array_slice($path, 1)),
            'bic' => new Legacy()->get(array_slice($path, 1)),
            'fftracker' => new FFTracker()->route(array_slice($path, 1)),
            'uc' => new UserControl()->route(array_slice($path, 1)),
            'tests' => new Tests()->route(array_slice($path, 1)),
            #Simple pages
            'simplepages' => new SimplePages()->route(array_slice($path, 1)),
            #SupOps
            'supops' => new SupOps()->route(array_slice($path, 1)),
            #Games
            'games' => new Games()->route(array_slice($path, 1)),
            #Feeds
            'sitemap' => new Sitemap()->route(array_slice($path, 1)),
            'rss', 'atom' => new Feeds()->uriParse($path),
            #Errors
            'error', 'errors', 'httperror', 'httperrors' => $this->error(array_slice($path, 1)),
            '' => new Homepage()->get([]),
            default => $this->error(['404']),
        };
    }
    
    /**
     * Function to help route error pages on frontend
     * @param array $uri
     *
     * @return array
     */
    private function error(array $uri): array
    {
        if (empty($uri[0]) || !\in_array((int)$uri[0], [300, 301, 302, 303, 305, 307, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451, 500, 501, 502, 503, 504, 505], true)) {
            $output_array['http_error'] = 404;
        } else {
            $output_array['http_error'] = (int)$uri[0];
        }
        $output_array['suggested_link'] = '/'.\dirname($_SERVER['REQUEST_URI'] ?? '');
        $output_array['error_page'] = true;
        return $output_array;
    }
}