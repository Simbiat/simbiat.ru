<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Router;
use Simbiat\fftracker\Pages\Statistics\Raw;
use Simbiat\fftracker\Pages\Statistics\Achievements;
use Simbiat\fftracker\Pages\Statistics\Bugs;
use Simbiat\fftracker\Pages\Statistics\Characters;
use Simbiat\fftracker\Pages\Statistics\Cities;
use Simbiat\fftracker\Pages\Statistics\FreeCompanies;
use Simbiat\fftracker\Pages\Statistics\GrandCompanies;
use Simbiat\fftracker\Pages\Statistics\Other;
use Simbiat\fftracker\Pages\Statistics\Servers;
use Simbiat\fftracker\Pages\Statistics\Timelines;

class Statistics extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['raw', 'achievements', 'bugs', 'characters', 'cities', 'freecompanies', 'grandcompanies', 'other', 'servers', 'timelines'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/fftracker/statistics', 'name'=>'FFXIV Statistics']
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
            'cities' => (new Cities)->get([]),
            'freecompanies' => (new FreeCompanies)->get([]),
            'grandcompanies' => (new GrandCompanies)->get([]),
            'other' => (new Other)->get([]),
            'servers' => (new Servers)->get([]),
            'timelines' => (new Timelines)->get([]),
        };
    }
}
