<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\SupOps\Levels;

use Simbiat\Website\Abstracts\Pages\StaticPage;

class L0 extends StaticPage
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/supops/l0', 'name' => 'Level 0']
    ];
    #Sub service name
    protected string $subservice_name = 'l0';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'SupOps: Level 0';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'SupOps: Level 0';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'SupOps: Level 0 specifics of The Flow';
}