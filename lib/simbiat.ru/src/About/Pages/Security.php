<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Security extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/security', 'name' => 'Security Policy']
    ];
    #Sub service name
    protected string $subServiceName = 'security';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Security Policy';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Security Policy';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Security Policy';
    #Flag to indicate this is a static page
    protected bool $static = true;
}
