<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages\Statistics;

class FreeCompanies extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/freecompanies', 'name' => 'Free Companies']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Free Companies';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Free Companies';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Free Companies Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'freecompanies';
}
