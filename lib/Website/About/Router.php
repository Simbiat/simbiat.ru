<?php
declare(strict_types = 1);

namespace Simbiat\Website\About;

use function array_slice;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['tech', 'tos', 'privacy', 'security', 'website', 'me', 'resume', 'contacts'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/', 'name' => 'About']
    ];
    protected string $title = 'About Simbiat Software';
    protected string $h1 = 'About Simbiat Software';
    protected string $ogdesc = 'About Simbiat Software';
    protected string $serviceName = 'about';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'tech' => (new \Simbiat\Website\About\Pages\Tech)->get(array_slice($path, 1)),
            'tos' => (new \Simbiat\Website\About\Pages\ToS)->get(array_slice($path, 1)),
            'privacy' => (new \Simbiat\Website\About\Pages\Privacy)->get(array_slice($path, 1)),
            'security' => (new \Simbiat\Website\About\Pages\Security)->get(array_slice($path, 1)),
            'website' => (new \Simbiat\Website\About\Redirects\Website)->get(array_slice($path, 1)),
            'me' => (new \Simbiat\Website\About\Redirects\Me)->get(array_slice($path, 1)),
            'resume' => (new \Simbiat\Website\About\Redirects\Resume)->get(array_slice($path, 1)),
            'contacts' => (new \Simbiat\Website\About\Redirects\Contacts)->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
