<?php

declare(strict_types=1);

namespace App\Controller\SupOps\FACTS;

use App\Controller\Abstracts\StaticPage;

final class Sustainability extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/sustainability', 'name' => 'Sustainability'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'sustainability';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Sustainability';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Sustainability';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Keep on keeping on';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/facts/sustainability.svg',
        '/assets/images/supops/navigation/flowchart.svg',
    ];
}
