<?php
declare(strict_types = 1);

namespace Simbiat\Website\About;

use Simbiat\Website\About\Pages\Me;
use Simbiat\Website\About\Pages\Privacy;
use Simbiat\Website\About\Pages\Security;
use Simbiat\Website\About\Pages\Tech;
use Simbiat\Website\About\Pages\ToS;
use Simbiat\Website\About\Redirects\Contacts;
use Simbiat\Website\About\Redirects\Resume;
use Simbiat\Website\About\Redirects\Website;

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
    
    /**
     * This is actual page generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'tech' => (new Tech())->get(array_slice($path, 1)),
            'tos' => (new ToS())->get(array_slice($path, 1)),
            'privacy' => (new Privacy())->get(array_slice($path, 1)),
            'security' => (new Security())->get(array_slice($path, 1)),
            'website' => (new Website())->get(array_slice($path, 1)),
            'me' => (new Me())->get(array_slice($path, 1)),
            'resume' => (new Resume())->get(array_slice($path, 1)),
            'contacts' => (new Contacts())->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->subRoutes).'`.'],
        };
    }
}
