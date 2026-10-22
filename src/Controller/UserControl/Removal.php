<?php

declare(strict_types=1);

namespace App\Controller\UserControl;

use App\Controller\Abstracts\Page;

final class Removal extends Page
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/uc/removal', 'name' => 'Removal'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'removal';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Removal';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Profile removal';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'Page to remove your profile';

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
        // This page is essentially static with 2 buttons.
        // It's not marked as static, because it will still require a DB to get session data, without which it would be useless
        return [];
    }
}
