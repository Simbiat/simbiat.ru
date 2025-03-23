<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Abstracts\Router;
use Simbiat\Website\Pages\FFTracker\Statistics\Achievements;
use Simbiat\Website\Pages\FFTracker\Statistics\Bugs;
use Simbiat\Website\Pages\FFTracker\Statistics\Characters;
use Simbiat\Website\Pages\FFTracker\Statistics\Groups;
use Simbiat\Website\Pages\FFTracker\Statistics\Other;
use Simbiat\Website\Pages\FFTracker\Statistics\Raw;
use Simbiat\Website\Pages\FFTracker\Statistics\Timelines;

class Statistics extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['raw', 'achievements', 'bugs', 'characters', 'groups', 'other', 'timelines'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'FFXIV Statistics']
    ];
    protected string $title = 'Final Fantasy XIV Statistics';
    protected string $h1 = 'Final Fantasy XIV Statistics';
    protected string $ogdesc = 'Final Fantasy XIV Statistics';
    protected string $ogimage = '/ogimages/fftracker.png';
    protected string $serviceName = 'fftracker';
    protected string $redirectMain = '/fftracker/statistics/characters';
    
    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'raw' => (new Raw)->get([]),
            'achievements' => (new Achievements)->get([]),
            'bugs' => (new Bugs)->get([]),
            'characters' => (new Characters)->get([]),
            'groups' => (new Groups)->get([]),
            'other' => (new Other)->get([]),
            'timelines' => (new Timelines)->get([]),
        };
    }
}
