<?php
declare(strict_types=1);
namespace Simbiat;

class Api extends Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'fftracker', 'bictracker'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'API endpoints related to Final Fantasy XIV Tracker',
        'API endpoints related to BIC Tracker'
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = true;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'fftracker' => (new fftracker\Api)->route(array_slice($path, 1)),
            'bictracker' => (new bictracker\Api)->route(array_slice($path, 1)),
            default => ['http_error' => 400, 'reason' => 'Unsupported endpoint', 'endpoints' => array_combine($this->subRoutes, $this->routesDesc)],
        };
    }
}
