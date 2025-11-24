<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Metrics extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/metrics', 'name' => 'The Metrics']
    ];
    #Sub service name
    protected string $subservice_name = 'metrics';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Metrics';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Metrics';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Measuring success';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/tech/lumo.svg',
        '/assets/images/supops/navigation/scales.svg',
    ];
}
