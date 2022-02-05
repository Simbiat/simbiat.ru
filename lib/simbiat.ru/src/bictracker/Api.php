<?php
declare(strict_types=1);
namespace Simbiat\bictracker;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'bic', 'keying', 'dbupdate'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Node representing details of an organization based on Bank Identification Code',
        'Node for checking Russian account keying against a Bank Identification Code',
        'Node to force BIC database update',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = false;

    protected function genData(array $path): array
    {
        return match($path[0]){
            'bic' => (new Api\Bic)->getData(array_slice($path, 1)),
            'keying' => (new Api\Keying)->getData(array_slice($path, 1)),
            'dbupdate' => (new Api\DBUpdate)->getData(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint', 'endpoints' => array_combine($this->subRoutes, $this->routesDesc)],
        };
    }
}
