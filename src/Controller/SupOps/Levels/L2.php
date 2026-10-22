<?php

declare(strict_types=1);

namespace App\Controller\SupOps\Levels;

use App\Controller\Abstracts\StaticPage;

final class L2 extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/l2', 'name' => 'Level 2'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'l2';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'SupOps: Level 2';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'SupOps: Level 2';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'SupOps: Level 2 specifics of The Flow';

    /**
     * @var array<int, string> List of images to H2 push
     */
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/charts/l2.png',
        '/assets/images/supops/memes/not_just_a_hat_rack.avif',
        '/assets/images/supops/navigation/escalator.svg',
    ];
}
