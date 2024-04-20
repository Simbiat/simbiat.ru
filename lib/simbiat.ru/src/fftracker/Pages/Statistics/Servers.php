<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class Servers extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/servers', 'name' => 'Servers']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Servers';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Servers';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Servers Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'servers';
}
