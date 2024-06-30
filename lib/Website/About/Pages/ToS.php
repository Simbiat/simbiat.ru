<?php
declare(strict_types=1);
namespace Simbiat\Website\About\Pages;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class ToS extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/tos', 'name' => 'Terms of Service']
    ];
    #Sub service name
    protected string $subServiceName = 'tos';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Terms of Service';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Terms of Service';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Terms of Service';
}
