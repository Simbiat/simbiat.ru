<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Needs extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/needs', 'name' => 'The Needs'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'needs';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Needs';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Needs';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Spreading the word';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/logo/rectangle_color.svg',
        '/assets/images/supops/logo/rectangle_mono.svg',
        '/assets/images/supops/logo/square_color.svg',
        '/assets/images/supops/logo/square_mono.svg',
    ];
}
