<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\FACTS;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class FACTS extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/facts', 'name' => 'The FACTS']
    ];
    #Sub service name
    protected string $subservice_name = 'facts';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The FACTS';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The FACTS';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps is built on FACTS';
}
