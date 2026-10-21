<?php

declare(strict_types=1);

namespace App\Controller\SupOps\Levels;

use App\Controller\Abstracts\StaticPage;

final class L4 extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/l4', 'name' => 'Level 4'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'l4';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Level 4';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Level 4';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Level 4 specifics of The Flow';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l4.png',
        '/assets/images/supops/memes/priorities.avif',
        '/assets/images/supops/navigation/map.svg',
    ];
}
