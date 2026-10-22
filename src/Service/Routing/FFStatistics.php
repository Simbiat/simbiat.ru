<?php

declare(strict_types=1);

namespace App\Service\Routing;

use App\Controller\FFXIV\Statistics\Achievements;
use App\Controller\FFXIV\Statistics\Bugs;
use App\Controller\FFXIV\Statistics\Characters;
use App\Controller\FFXIV\Statistics\Groups;
use App\Controller\FFXIV\Statistics\Other;
use App\Controller\FFXIV\Statistics\Raw;
use App\Controller\FFXIV\Statistics\Timelines;

final class FFStatistics extends Router
{
    /**
     * @var array<int, string> List supported "paths". Basic ones only, some extra validation may be required further
     */
    #[\Override]
    protected array $sub_routes = ['raw', 'achievements', 'bugs', 'characters', 'groups', 'other', 'timelines'];

    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'FFXIV Statistics'],
    ];

    #[\Override]
    protected string $title = 'Final Fantasy XIV Statistics';

    #[\Override]
    protected string $h1 = 'Final Fantasy XIV Statistics';

    #[\Override]
    protected string $og_desc = 'Final Fantasy XIV Statistics';

    #[\Override]
    protected string $og_image = '/ogimages/fftracker.webp';

    #[\Override]
    protected string $service_name = 'fftracker';

    #[\Override]
    protected string $redirect_main = '/fftracker/statistics/characters';

    /**
     * This is the actual page generation based on further details of the $path
     *
     * @param array $path
     *
     * @return array
     */
    protected function pageGen(array $path): array
    {
        return match ($path[0]) {
            'raw' => new Raw()->get([]),
            'achievements' => new Achievements()->get([]),
            'bugs' => new Bugs()->get([]),
            'characters' => new Characters()->get([]),
            'groups' => new Groups()->get([]),
            'other' => new Other()->get([]),
            'timelines' => new Timelines()->get([]),
        };
    }
}
