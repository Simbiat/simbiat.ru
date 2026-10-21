<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Solution extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/solution', 'name' => 'The Solution'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'solution';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Solution';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Solution';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: What can improve tech support?';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/money_flow.avif',
        '/assets/images/supops/memes/packing.avif',
        '/assets/images/supops/navigation/definitions.svg',
    ];
}
