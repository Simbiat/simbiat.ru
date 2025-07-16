<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\About;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Security extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/security', 'name' => 'Security Policy']
    ];
    #Sub service name
    protected string $subservice_name = 'security';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Security Policy';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Security Policy';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Security Policy';
}
