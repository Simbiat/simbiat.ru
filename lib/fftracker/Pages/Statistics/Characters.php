<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class Characters extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/characters', 'name' => 'Characters']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Characters';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Characters';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Characters Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'characters';
}
