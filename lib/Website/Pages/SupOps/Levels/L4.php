<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\Levels;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class L4 extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/l4', 'name' => 'Level 4']
    ];
    #Sub service name
    protected string $subservice_name = 'l4';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Level 4';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Level 4';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Level 4 specifics of The Flow';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l4.png',
        '/assets/images/supops/memes/priorities.avif',
        '/assets/images/supops/navigation/map.svg',
    ];
}