<?php
declare(strict_types=1);
namespace Simbiat\About;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['tech', 'tos', 'privacy', 'security'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/about/', 'name'=>'About']
    ];
    protected string $title = 'About Simbiat Software';
    protected string $h1 = 'About Simbiat Software';
    protected string $ogdesc = 'About Simbiat Software';
    protected string $serviceName = 'about';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'tech' => (new Pages\Tech)->get(array_slice($path, 1)),
            'tos' => (new Pages\ToS)->get(array_slice($path, 1)),
            'privacy' => (new Pages\Privacy)->get(array_slice($path, 1)),
            'security' => (new Pages\Security)->get(array_slice($path, 1)),
        };
    }
}
