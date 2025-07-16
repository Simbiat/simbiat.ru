<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\About;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Tech extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/tech', 'name' => 'Technology']
    ];
    #Sub service name
    protected string $subservice_name = 'tech';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'About Simbiat Software\'s Technology';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'About Simbiat Software\'s Technology';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'About Simbiat Software\'s Technology';
}
