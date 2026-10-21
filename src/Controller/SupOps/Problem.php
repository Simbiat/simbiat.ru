<?php

declare(strict_types=1);

namespace App\Controller\SupOps;

use App\Controller\Abstracts\StaticPage;

final class Problem extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/problem', 'name' => 'The Problem'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'problem';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The Problem';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The Problem';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Why does tech support fail?';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/overworked.avif',
        '/assets/images/supops/memes/trust_me.avif',
        '/assets/images/supops/navigation/idea.svg',
    ];
}
