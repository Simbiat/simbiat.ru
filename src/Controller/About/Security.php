<?php

declare(strict_types=1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

final class Security extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/about/security', 'name' => 'Security Policy'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'security';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Security Policy';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Security Policy';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Security Policy';
}
