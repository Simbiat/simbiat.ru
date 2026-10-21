<?php

declare(strict_types=1);

namespace App\Controller\SupOps\Levels;

use App\Controller\Abstracts\StaticPage;

final class L1 extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/l1', 'name' => 'Level 1'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'l1';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Level 1';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Level 1';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Level 1 specifics of The Flow';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l1.png',
        '/assets/images/supops/memes/detective_mode.avif',
        '/assets/images/supops/memes/bean_waiting.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}
