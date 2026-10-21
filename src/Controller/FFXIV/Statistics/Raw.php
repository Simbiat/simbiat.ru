<?php

declare(strict_types=1);

namespace App\Controller\FFXIV\Statistics;

final class Raw extends General
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics/raw', 'name' => 'Raw data'],
    ];

    // Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Raw character data';

    // Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Raw character data';

    // Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'FFXIV Statistics: Raw character data';

    // List of JSOn files, that we need to try to ingest
    #[\Override]
    protected string $json_to_ingest = 'raw';
}
