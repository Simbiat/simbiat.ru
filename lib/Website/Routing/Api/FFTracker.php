<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing\Api;

use Simbiat\Website\Api\FFTracker\Achievement;
use Simbiat\Website\Api\FFTracker\Character;
use Simbiat\Website\Api\FFTracker\CrossworldLinkshell;
use Simbiat\Website\Api\FFTracker\FreeCompany;
use Simbiat\Website\Api\FFTracker\Linkshell;
use Simbiat\Website\Api\FFTracker\MergeCrest;
use Simbiat\Website\Api\FFTracker\PvPTeam;
use function array_slice;

class FFTracker extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $sub_routes = [
        'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'achievements', 'merge_crest'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routes_description = [
        'Node representing Final Fantasy XIV character',
        'Node representing Final Fantasy XIV Free Company',
        'Node representing Final Fantasy XIV PvP Team',
        'Node representing Final Fantasy XIV Linkshell',
        'Node representing Final Fantasy XIV Crossworld Linkshell',
        'Node representing Final Fantasy XIV Achievement',
        'Node to merge crest components into a single image file'
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $top_level = false;
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = false;
    
    protected function genData(array $path): array
    {
        return match($path[0]) {
            'characters' => (new Character())->getData(array_slice($path, 1)),
            'freecompanies' => (new FreeCompany())->getData(array_slice($path, 1)),
            'linkshells' => (new Linkshell())->getData(array_slice($path, 1)),
            'crossworld_linkshells' => (new CrossworldLinkshell())->getData(array_slice($path, 1)),
            'pvpteams' => (new PvPTeam())->getData(array_slice($path, 1)),
            'achievements' => (new Achievement())->getData(array_slice($path, 1)),
            'merge_crest' => (new MergeCrest())->getData(array_slice($path, 1)),
        };
    }
}