<?php
declare(strict_types=1);
namespace Simbiat\Sitemap;

use Simbiat\HomePage;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['xml', 'html', 'txt'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/sitemap/html/', 'name'=>'Sitemap']
    ];
    protected string $title = 'Sitemap';
    protected string $h1 = 'Sitemap';
    protected string $ogdesc = 'Sitemap';
    protected string $serviceName = 'sitemap';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        #Check if format was provided in URL
        if (in_array($path[0], ['xml', 'txt', 'html'])) {
            $format = $path[0];
            #Slice the path
            $path = array_slice($path, 1);
        }
        if (empty($format)) {
            $format = 'html';
        }
        #Send 406 if format is not acceptable
        match ($format) {
            'html' => HomePage::$headers->notAccept(['text/html']),
            'txt' => HomePage::$headers->notAccept(['text/plain']),
            'xml' => HomePage::$headers->notAccept(['application/xml']),
        };
        #Send content type header if we have XML or text
        if ($format === 'txt') {
            @header('Content-Type: text/plain; charset=utf-8');
        } elseif ($format === 'xml') {
            @header('Content-Type: application/xml; charset=utf-8');
        }
        #Ensure path is set, even though it's empty
        if (empty($path[0])) {
            $path[0] = '';
        }
        $result = match($path[0]) {
            'general' => (new Pages\General)->get($path),
            'bics', 'characters', 'freecompanies', 'linkshells', 'pvpteams', 'achievements' => (new Pages\Countables)->get($path),
            default => (new Pages\Index)->get($path),
        };
        $result['format'] = $format;
        if ($format === 'txt' || $format === 'xml') {
            $result['template_override'] = 'common/pages/sitemap.twig';
        }
        return $result;
    }
}
