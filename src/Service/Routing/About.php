<?php
declare(strict_types = 1);

namespace App\Service\Routing;

use App\Controller\About\Contacts;
use App\Controller\About\Me;
use App\Controller\About\Privacy;
use App\Controller\About\Security;
use App\Controller\About\Tech;
use App\Controller\About\ToS;
use App\Controller\Redirects\About\Resume;
use App\Controller\Redirects\About\Website;
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
    protected string $og_desc = 'About Simbiat Software';
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
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint `'.$path[0].'`. Supported endpoints: `'.\implode('`, `', $this->sub_routes).'`.'],
        };
    }
}
