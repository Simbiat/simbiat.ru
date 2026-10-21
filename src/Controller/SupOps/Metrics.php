<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Metrics extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/metrics', 'name' => 'The Metrics'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'metrics';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Metrics';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Metrics';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Measuring success';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/tech/lumo.svg',
        '/assets/images/supops/navigation/scales.svg',
    ];
}
