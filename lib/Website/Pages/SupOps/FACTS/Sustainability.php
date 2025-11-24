<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\FACTS;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Sustainability extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/sustainability', 'name' => 'Sustainability']
    ];
    #Sub service name
    protected string $subservice_name = 'sustainability';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Sustainability';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Sustainability';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Keep on keeping on';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/facts/sustainability.svg',
        '/assets/images/supops/navigation/flowchart.svg',
    ];
}
