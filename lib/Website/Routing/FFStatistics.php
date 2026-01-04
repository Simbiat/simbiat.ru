<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\FFXIV\Pages\Statistics\Achievements;
use Simbiat\FFXIV\Pages\Statistics\Bugs;
use Simbiat\FFXIV\Pages\Statistics\Characters;
use Simbiat\FFXIV\Pages\Statistics\Groups;
use Simbiat\FFXIV\Pages\Statistics\Other;
use Simbiat\FFXIV\Pages\Statistics\Raw;
use Simbiat\FFXIV\Pages\Statistics\Timelines;
use Simbiat\Website\Abstracts\Router;

class FFStatistics extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['raw', 'achievements', 'bugs', 'characters', 'groups', 'other', 'timelines'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'FFXIV Statistics']
    ];
    protected string $title = 'Final Fantasy XIV Statistics';
    protected string $h1 = 'Final Fantasy XIV Statistics';
    protected string $og_desc = 'Final Fantasy XIV Statistics';
    protected string $og_image = '/ogimages/fftracker.webp';
    protected string $service_name = 'fftracker';
    protected string $redirect_main = '/fftracker/statistics/characters';
    
    #This is the actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'raw' => new Raw()->get([]),
            'achievements' => new Achievements()->get([]),
            'bugs' => new Bugs()->get([]),
            'characters' => new Characters()->get([]),
            'groups' => new Groups()->get([]),
            'other' => new Other()->get([]),
            'timelines' => new Timelines()->get([]),
        };
    }
}
