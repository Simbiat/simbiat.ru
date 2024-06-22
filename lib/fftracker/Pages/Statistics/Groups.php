<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class Groups extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/groups', 'name' => 'Groups']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Groups';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Groups';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Groups Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'groups';
}
