<?php

declare(strict_types=1);

namespace App\Controller\SupOps\Levels;

use App\Controller\Abstracts\StaticPage;

final class L0 extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/l0', 'name' => 'Level 0'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'l0';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Level 0';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Level 0';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Level 0 specifics of The Flow';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/allow_it.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}
