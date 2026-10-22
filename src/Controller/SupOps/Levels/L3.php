<?php

declare(strict_types=1);

namespace App\Controller\SupOps\Levels;

use App\Controller\Abstracts\StaticPage;

final class L3 extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/l3', 'name' => 'Level 3'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'l3';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'SupOps: Level 3';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'SupOps: Level 3';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'SupOps: Level 3 specifics of The Flow';

    /**
     * @var array<int, string> List of images to H2 push
     */
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l3.png',
        '/assets/images/supops/memes/knowledge_is_power.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}
