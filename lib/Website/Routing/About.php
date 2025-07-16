<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\About\Me;
use Simbiat\Website\Pages\About\Privacy;
use Simbiat\Website\Pages\About\Security;
use Simbiat\Website\Pages\About\Tech;
use Simbiat\Website\Pages\About\ToS;
use Simbiat\Website\Redirects\About\Contacts;
use Simbiat\Website\Redirects\About\Resume;
use Simbiat\Website\Redirects\About\Website;
use function array_slice;

class About extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['tech', 'tos', 'privacy', 'security', 'website', 'me', 'resume', 'contacts'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/', 'name' => 'About']
    ];
    protected string $title = 'About Simbiat Software';
    protected string $h1 = 'About Simbiat Software';
    protected string $ogdesc = 'About Simbiat Software';
    protected string $service_name = 'about';
    
    /**
     * This is the actual page generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'tech' => new Tech()->get(array_slice($path, 1)),
            'tos' => new ToS()->get(array_slice($path, 1)),
            'privacy' => new Privacy()->get(array_slice($path, 1)),
            'security' => new Security()->get(array_slice($path, 1)),
            'website' => new Website()->get(array_slice($path, 1)),
            'me' => new Me()->get(array_slice($path, 1)),
            'resume' => new Resume()->get(array_slice($path, 1)),
            'contacts' => new Contacts()->get(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.implode('`, `', $this->sub_routes).'`.'],
        };
    }
}
