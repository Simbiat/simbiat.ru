<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class Achievements extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/achievements', 'name' => 'Achievements']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Achievements';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Achievements';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Achievements Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'achievements';
}
