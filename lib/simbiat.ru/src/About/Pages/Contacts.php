<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Contacts extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/contacts', 'name' => 'Contacts']
    ];
    #Sub service name
    protected string $subServiceName = 'contacts';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Contacts';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Contacts';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Contacts';
    #Flag to indicate this is a static page
    protected bool $static = true;
}
