<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker\Statistics;

class Bugs extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/bugs', 'name' => 'Bugs']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Bugs';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Bugs';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Bugs Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'bugs';
}
