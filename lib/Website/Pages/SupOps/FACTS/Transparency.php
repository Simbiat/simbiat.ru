<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\FACTS;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Transparency extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/transparency', 'name' => 'Transparency']
    ];
    #Sub service name
    protected string $subservice_name = 'transparency';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Transparency';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Transparency';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Openness builds trust faster than any SLA';
}
