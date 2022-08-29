<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Website extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/website', 'name' => 'Website']
    ];
    #Sub service name
    protected string $subServiceName = 'website';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'About Simbiat Software website';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'About Simbiat Software website';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'About Simbiat Software website';
}
