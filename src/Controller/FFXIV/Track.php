<?php

declare(strict_types=1);

namespace App\Controller\FFXIV;

use App\Controller\Abstracts\StaticPage;

final class Track extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/track', 'name' => 'Track entity'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'track';

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Track entity';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Track entity';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Register entity for tracking on FFXIV Tracker';

    // List of permissions, from which at least 1 is required to have access to the page
    #[\Override]
    protected array $required_permission = ['view_ff'];

    // This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        return [];
    }
}
