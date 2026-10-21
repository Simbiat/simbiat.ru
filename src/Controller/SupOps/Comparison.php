<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Comparison extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/comparison', 'name' => 'The Comparison'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'comparison';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Comparison';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Comparison';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Comparison to other practices';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/tech/lumo.svg',
        '/assets/images/supops/navigation/tools.svg',
    ];
}
