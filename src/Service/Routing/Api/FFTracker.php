<?php

declare(strict_types=1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Api;
use App\Controller\Api\FFXIV\Achievement;
use App\Controller\Api\FFXIV\Character;
use App\Controller\Api\FFXIV\CrossworldLinkshell;
use App\Controller\Api\FFXIV\FreeCompany;
use App\Controller\Api\FFXIV\Linkshell;
use App\Controller\Api\FFXIV\MergeCrest;
use App\Controller\Api\FFXIV\PvPTeam;

final class FFTracker extends Api
{
    /**
     * @var array<int, string> Supported edges
     */
    #[\Override]
    protected array $sub_routes = [
        'characters', 'freecompanies', 'pvpteams', 'linkshells', 'crossworld_linkshells', 'achievements', 'merge_crest',
    ];

    /**
     * @var array<int, string> Description of the nodes (need to be in the same order)
     */
    #[\Override]
    protected array $routes_description = [
        'Node representing Final Fantasy XIV character',
        'Node representing Final Fantasy XIV Free Company',
        'Node representing Final Fantasy XIV PvP Team',
        'Node representing Final Fantasy XIV Linkshell',
        'Node representing Final Fantasy XIV Crossworld Linkshell',
        'Node representing Final Fantasy XIV Achievement',
        'Node to merge crest components into a single image file',
    ];

    protected function genData(array $path): array
    {
        return match ($path[0]) {
            'characters' => new Character()->getData(\array_slice($path, 1)),
            'freecompanies' => new FreeCompany()->getData(\array_slice($path, 1)),
            'linkshells' => new Linkshell()->getData(\array_slice($path, 1)),
            'crossworld_linkshells' => new CrossworldLinkshell()->getData(\array_slice($path, 1)),
            'pvpteams' => new PvPTeam()->getData(\array_slice($path, 1)),
            'achievements' => new Achievement()->getData(\array_slice($path, 1)),
            'merge_crest' => new MergeCrest()->getData(\array_slice($path, 1)),
        };
    }
}
