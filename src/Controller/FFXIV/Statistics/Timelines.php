<?php

declare(strict_types=1);

namespace App\Controller\FFXIV\Statistics;

final class Timelines extends General
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics/timelines', 'name' => 'Timelines'],
    ];

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'Timelines';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'Timelines';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'FFXIV Statistics: Timelines Category';

    /**
     * @var string List of JSON files, that we need to try to ingest
     */
    #[\Override]
    protected string $json_to_ingest = 'timelines';
}
