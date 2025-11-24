<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Flow extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/flow', 'name' => 'The Flow']
    ];
    #Sub service name
    protected string $subservice_name = 'flow';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Flow';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Flow';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: High-level process flow';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/overall.png',
        '/assets/images/supops/navigation/journey.svg',
    ];
}
