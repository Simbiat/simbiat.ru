<?php

declare(strict_types=1);

namespace App\Controller\FFXIV\Statistics;

final class Other extends General
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics/other', 'name' => 'Other'],
    ];

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Other';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Other';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'FFXIV Statistics: Other Category';

    // List of JSOn files, that we need to try to ingest
    #[\Override]
    protected string $json_to_ingest = 'other';
}
