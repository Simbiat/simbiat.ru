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

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'avatars';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Avatars';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Your avatars';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Page to edit your avatars';

    /**
     * @var string Cache strategy: aggressive, private, live, month, week, day, hour
     */
    #[\Override]
    protected string $cache_strategy = 'private';

    /**
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = true;

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        return ['avatars' => new User($_SESSION['user_id'])->getAvatars()];
    }
}
