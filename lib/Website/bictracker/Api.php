<?php
declare(strict_types=1);
namespace Simbiat\Website\bictracker;

class Api extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'bics', 'keying', 'dbupdate'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Node representing details of Russian organizations based on Bank Identification Code',
        'Node for checking Russian account keying against a Russian Bank Identification Code',
        'Node to force BIC database update',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = false;

    protected function genData(array $path): array
    {
        return match($path[0]){
            'bics' => (new \Simbiat\Website\bictracker\Api\Bic)->getData(array_slice($path, 1)),
            'keying' => (new \Simbiat\Website\bictracker\Api\Keying)->getData(array_slice($path, 1)),
            'dbupdate' => (new \Simbiat\Website\bictracker\Api\DBUpdate)->getData(array_slice($path, 1)),
        };
    }
}
