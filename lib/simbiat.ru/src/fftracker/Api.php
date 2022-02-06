<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'character', 'freecompany', 'pvpteam', 'linkshell', 'crossworld_linkshell', 'achievement'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Node representing Final Fantasy XIV character',
        'Node representing Final Fantasy XIV Free Company',
        'Node representing Final Fantasy XIV PvP Team',
        'Node representing Final Fantasy XIV Linkshell',
        'Node representing Final Fantasy XIV Crossworld Linkshell',
        'Node representing Final Fantasy XIV Achievement',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = false;

    protected function genData(array $path): array
    {
        return match($path[0]){
            'character' => (new Api\Character)->getData(array_slice($path, 1)),
            'freecompany' => (new Api\FreeCompany)->getData(array_slice($path, 1)),
            'linkshell' => (new Api\Linkshell)->getData(array_slice($path, 1)),
            'crossworld_linkshell' => (new Api\CrossworldLinkshell)->getData(array_slice($path, 1)),
            'pvpteam' => (new Api\PvPTeam)->getData(array_slice($path, 1)),
            'achievement' => (new Api\Achievement)->getData(array_slice($path, 1)),
        };
    }
}
