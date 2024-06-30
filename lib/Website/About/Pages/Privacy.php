<?php
declare(strict_types=1);
namespace Simbiat\Website\About\Pages;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Privacy extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/privacy', 'name' => 'Privacy Policy']
    ];
    #Sub service name
    protected string $subServiceName = 'privacy';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Privacy Policy';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Privacy Policy';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Privacy Policy';
}
