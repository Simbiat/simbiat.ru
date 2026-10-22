<?php

declare(strict_types=1);

namespace App\Controller\About;

use App\Controller\Abstracts\StaticPage;

final class Tech extends StaticPage
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/about/tech', 'name' => 'Technology'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'tech';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = "About Simbiat Software's Technology";

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = "About Simbiat Software's Technology";

    /**
     * @var string Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = "About Simbiat Software's Technology";
}
