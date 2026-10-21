<?php

declare(strict_types=1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

final class ToS extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/about/tos', 'name' => 'Terms of Service'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'tos';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Terms of Service';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Terms of Service';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Terms of Service';
}
