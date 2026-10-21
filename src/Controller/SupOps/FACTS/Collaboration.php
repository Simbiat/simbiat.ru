<?php

declare(strict_types=1);

namespace App\Controller\SupOps\FACTS;

use App\Controller\Abstracts\StaticPage;

final class Collaboration extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/collaboration', 'name' => 'Collaboration'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'collaboration';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Collaboration';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Collaboration';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Solutions are co‑created';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/facts/collaboration.svg',
        '/assets/images/supops/navigation/transparency.svg',
    ];
}
