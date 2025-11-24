<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class Needs extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/needs', 'name' => 'The Needs']
    ];
    #Sub service name
    protected string $subservice_name = 'needs';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: The Needs';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: The Needs';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Spreading the word';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/logo/rectangle_color.svg',
        '/assets/images/supops/logo/rectangle_mono.svg',
        '/assets/images/supops/logo/square_color.svg',
        '/assets/images/supops/logo/square_mono.svg',
    ];
}
