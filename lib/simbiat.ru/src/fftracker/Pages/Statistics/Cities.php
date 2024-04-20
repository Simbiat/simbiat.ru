<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class Cities extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/cities', 'name' => 'Cities']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Cities';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Cities';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Cities Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'cities';
}
