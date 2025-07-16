<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\About;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class ToS extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/tos', 'name' => 'Terms of Service']
    ];
    #Sub service name
    protected string $subservice_name = 'tos';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Terms of Service';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Terms of Service';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Terms of Service';
}
