<?php

declare(strict_types=1);

namespace App\Controller\UserControl;

use App\Controller\Abstracts\Page;
use App\Entity\User;

final class Avatars extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/uc/avatars', 'name' => 'Avatars'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'avatars';

    // Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Avatars';

    // Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Your avatars';

    // Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Page to edit your avatars';

    // Cache strategy: aggressive, private, live, month, week, day, hour
    #[\Override]
    protected string $cache_strategy = 'private';

    // Flag indicating that authentication is required
    #[\Override]
    protected bool $authentication_needed = true;

    // This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        return ['avatars' => new User($_SESSION['user_id'])->getAvatars()];
    }
}
