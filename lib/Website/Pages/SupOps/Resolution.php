<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Resolution extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/resolution', 'name' => 'The Resolution']
    ];
    #Sub service name
    protected string $subservice_name = 'resolution';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Resolution';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Resolution';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: How to resolve tickets';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/sharing.avif',
        '/assets/images/supops/memes/flex_seal.avif',
        '/assets/images/supops/navigation/expand.svg',
    ];
}
