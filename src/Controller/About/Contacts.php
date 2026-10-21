<?php

declare(strict_types=1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

final class Contacts extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/about/contacts', 'name' => 'Contacts'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'contacts';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Contacts';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'How co contact Simbiat Software';
}
