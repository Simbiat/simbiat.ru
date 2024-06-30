<?php
declare(strict_types=1);
namespace Simbiat\Website\About\Pages;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Tech extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/tech', 'name' => 'Technology']
    ];
    #Sub service name
    protected string $subServiceName = 'tech';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'About Simbiat Software\'s Technology';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'About Simbiat Software\'s Technology';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'About Simbiat Software\'s Technology';
}
