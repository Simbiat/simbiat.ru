<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Scale extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/scale', 'name' => 'The Scale'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'scale';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Scale';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Scale';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Both big and small scale';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/shortcut.webp',
        '/assets/images/supops/navigation/performance.svg',
    ];
}
