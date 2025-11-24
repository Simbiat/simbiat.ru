<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\Levels;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class L2 extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/l2', 'name' => 'Level 2']
    ];
    #Sub service name
    protected string $subservice_name = 'l2';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Level 2';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Level 2';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Level 2 specifics of The Flow';
    #List of images to H2 push
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l2.png',
        '/assets/images/supops/memes/not_just_a_hat_rack.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}