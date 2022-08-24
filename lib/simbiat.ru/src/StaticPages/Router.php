<?php
declare(strict_types=1);
namespace Simbiat\StaticPages;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['devicedetector'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/staticpages/', 'name'=>'Static Pages']
    ];
    protected string $title = 'Static Pages';
    protected string $h1 = 'Static Pages';
    protected string $ogdesc = 'Various static pages hosted by Simbiat Software';
    protected string $serviceName = 'staticpages';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'devicedetector' => (new Pages\DeviceDetector)->get(array_slice($path, 1)),
        };
    }
}
