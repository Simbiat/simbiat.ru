<?php
declare(strict_types = 1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Api;

class BICTracker extends Api
{
    #Supported edges
    protected array $sub_routes = [
        'bics', 'keying', 'dbupdate'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routes_description = [
        'Node representing details of Russian organizations based on Bank Identification Code',
        'Node for checking Russian account keying against a Russian Bank Identification Code',
        'Node to force BIC database update',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $top_level = false;
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = false;

    protected function genData(array $path): array
    {
        return match($path[0]){
            'bics' => (new \App\Controller\Api\BIC\Bic)->getData(\array_slice($path, 1)),
            'keying' => (new \App\Controller\Api\BIC\Keying)->getData(\array_slice($path, 1)),
            'dbupdate' => (new \App\Controller\Api\BIC\DBUpdate)->getData(\array_slice($path, 1)),
        };
    }
}
