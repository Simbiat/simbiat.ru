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

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'fftracker';

    /**
     * @var string Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'FFXIV Linkage';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'FFXIV Linkage';

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Page to link FFXIV characters';

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
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['link_ff'];

    /**
     * This is actual page generation based on further details of the $path
     *
     * @param array<int, string> $path
     *
     * @return array<string, mixed>
     */
    protected function generate(array $path): array
    {
        return new User($_SESSION['user_id'])->getFF();
    }
}
