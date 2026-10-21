<?php

declare(strict_types=1);

namespace App\Controller\SupOps\FACTS;

use App\Controller\Abstracts\StaticPage;

final class FACTS extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/facts', 'name' => 'The FACTS'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'facts';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: The FACTS';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: The FACTS';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps is built on FACTS';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/memes/everybody_lies.avif',
        '/assets/images/supops/memes/megazord.avif',
        '/assets/images/supops/navigation/feedback.svg',
    ];
}
