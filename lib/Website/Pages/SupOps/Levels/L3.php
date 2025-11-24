<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\Levels;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class L3 extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/l3', 'name' => 'Level 3']
    ];
    #Sub service name
    protected string $subservice_name = 'l3';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Level 3';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Level 3';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Level 3 specifics of The Flow';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l3.png',
        '/assets/images/supops/memes/knowledge_is_power.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}