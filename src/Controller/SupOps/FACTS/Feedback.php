<?php

declare(strict_types=1);

namespace App\Controller\SupOps\FACTS;

use App\Controller\Abstracts\StaticPage;

final class Feedback extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/supops/feedback', 'name' => 'Feedback'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'feedback';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'SupOps: Feedback';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'SupOps: Feedback';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'SupOps: Every user voice is a data point; listen, inform, improve';

    // List of images to H2 push
    #[\Override]
    protected array $h2_push_extra = [
        '/assets/images/supops/facts/feedback.svg',
        '/assets/images/supops/navigation/automation.svg',
    ];
}
