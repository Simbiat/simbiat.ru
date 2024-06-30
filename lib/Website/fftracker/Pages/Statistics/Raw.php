<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Pages\Statistics;

use Simbiat\Website\fftracker\Pages\Statistics\General;

class Raw extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/raw', 'name' => 'Raw data']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Raw character data';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Raw character data';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Raw character data';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'raw';
}
