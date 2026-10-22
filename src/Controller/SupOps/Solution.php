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

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'solution';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'SupOps: The Solution';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'SupOps: The Solution';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'SupOps: What can improve tech support?';

    /**
     * @var array<int, string> List of images to H2 push
     */
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/money_flow.avif',
        '/assets/images/supops/memes/packing.avif',
        '/assets/images/supops/navigation/definitions.svg',
    ];
}
