<?php
declare(strict_types=1);
namespace Simbiat\About;

class Router extends \Simbiat\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['me', 'website', 'tech', 'contacts', 'tos', 'privacy', 'security', 'resume'];
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
            'me' => (new Pages\Me)->get(array_slice($path, 1)),
            'website' => (new Pages\Website)->get(array_slice($path, 1)),
            'tech' => (new Pages\Tech)->get(array_slice($path, 1)),
            'contacts' => (new Pages\Contacts)->get(array_slice($path, 1)),
            'tos' => (new Pages\ToS)->get(array_slice($path, 1)),
            'privacy' => (new Pages\Privacy)->get(array_slice($path, 1)),
            'security' => (new Pages\Security)->get(array_slice($path, 1)),
            'resume' => (new Pages\Resume)->get(array_slice($path, 1)),
        };
    }
}
