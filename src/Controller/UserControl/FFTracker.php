<?php

declare(strict_types=1);

namespace App\Controller\UserControl;

use App\Controller\Abstracts\Page;
use App\Entity\User;

final class FFTracker extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/uc/fftracker', 'name' => 'FFXIV'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'fftracker';

    // Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'FFXIV Linkage';

    // Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'FFXIV Linkage';

    // Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Page to link FFXIV characters';

    // Cache strategy: aggressive, private, live, month, week, day, hour
    #[\Override]
    protected string $cache_strategy = 'private';

    // Flag indicating that authentication is required
    #[\Override]
    protected bool $authentication_needed = true;

    // List of permissions, from which at least 1 is required to have access to the page
    #[\Override]
    protected array $required_permission = ['link_ff'];

    // This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        return new User($_SESSION['user_id'])->getFF();
    }
}
