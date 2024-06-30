<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker;

class Api extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'achievements', 'merge_crest'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Node representing Final Fantasy XIV character',
        'Node representing Final Fantasy XIV Free Company',
        'Node representing Final Fantasy XIV PvP Team',
        'Node representing Final Fantasy XIV Linkshell',
        'Node representing Final Fantasy XIV Crossworld Linkshell',
        'Node representing Final Fantasy XIV Achievement',
        'Node to merge crest components into a single image file'
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = false;

    protected function genData(array $path): array
    {
        return match($path[0]){
            'characters' => (new \Simbiat\Website\fftracker\Api\Character)->getData(array_slice($path, 1)),
            'freecompanies' => (new \Simbiat\Website\fftracker\Api\FreeCompany)->getData(array_slice($path, 1)),
            'linkshells' => (new \Simbiat\Website\fftracker\Api\Linkshell)->getData(array_slice($path, 1)),
            'crossworld_linkshells' => (new \Simbiat\Website\fftracker\Api\CrossworldLinkshell)->getData(array_slice($path, 1)),
            'pvpteams' => (new \Simbiat\Website\fftracker\Api\PvPTeam)->getData(array_slice($path, 1)),
            'achievements' => (new \Simbiat\Website\fftracker\Api\Achievement)->getData(array_slice($path, 1)),
            'merge_crest' => (new \Simbiat\Website\fftracker\Api\MergeCrest)->getData(array_slice($path, 1)),
        };
    }
}
