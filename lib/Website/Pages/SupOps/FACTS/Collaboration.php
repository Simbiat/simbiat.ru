<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\FACTS;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Collaboration extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/collaboration', 'name' => 'Collaboration']
    ];
    #Sub service name
    protected string $subservice_name = 'collaboration';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Collaboration';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Collaboration';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Solutions are co‑created';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/facts/collaboration.svg',
        '/assets/images/supops/navigation/transparency.svg',
    ];
}
