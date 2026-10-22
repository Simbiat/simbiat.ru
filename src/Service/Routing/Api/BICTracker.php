<?php

declare(strict_types=1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Api;
use App\Controller\Api\BIC\Bic;
use App\Controller\Api\BIC\DBUpdate;
use App\Controller\Api\BIC\Keying;

final class BICTracker extends Api
{
    /**
     * @var array<int, string> Supported edges
     */
    #[\Override]
    protected array $sub_routes = [
        'bics', 'keying', 'dbupdate',
    ];

    /**
     * @var array<int, string> Description of the nodes (need to be in the same order)
     */
    #[\Override]
    protected array $routes_description = [
        'Node representing details of Russian organizations based on Bank Identification Code',
        'Node for checking Russian account keying against a Russian Bank Identification Code',
        'Node to force BIC database update',
    ];

    protected function genData(array $path): array
    {
        return match ($path[0]) {
            'bics' => new Bic()->getData(\array_slice($path, 1)),
            'keying' => new Keying()->getData(\array_slice($path, 1)),
            'dbupdate' => new DBUpdate()->getData(\array_slice($path, 1)),
        };
    }
}
