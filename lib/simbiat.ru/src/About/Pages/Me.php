<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Me extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/me', 'name' => 'Me']
    ];
    #Sub service name
    protected string $subServiceName = 'me';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'About me';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'About me';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'About me';
    #Flag to indicate this is a static page
    protected bool $static = true;
}
