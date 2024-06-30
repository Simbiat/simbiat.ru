<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Pages\Statistics;

use Simbiat\Website\fftracker\Pages\Statistics\General;

class Timelines extends General
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics/timelines', 'name' => 'Timelines']
    ];
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Timelines';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Timelines';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Statistics: Timelines Category';
    #List of JSOn files, that we need to try to ingest
    protected string $jsonToIngest = 'timelines';
}
