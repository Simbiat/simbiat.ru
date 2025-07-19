<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\DeviceDetector;

class SimplePages extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['devicedetector'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/simplepages/', 'name' => 'Simple Pages']
    ];
    protected string $title = 'Simple Pages';
    protected string $h1 = 'Simple Pages';
    protected string $ogdesc = 'Various simple pages hosted by Simbiat Software';
    protected string $service_name = 'simplepages';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'devicedetector' => (new DeviceDetector())->get(\array_slice($path, 1)),
        };
    }
}
