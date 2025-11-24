<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Interoperability extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/interoperability', 'name' => 'The Interoperability']
    ];
    #Sub service name
    protected string $subservice_name = 'interoperability';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Interoperability';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Interoperability';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Every tool in the kit matters';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/integration.avif',
        '/assets/images/supops/navigation/puzzle.svg',
    ];
}
