<?php

declare(strict_types=1);

namespace App\Controller\Sitemap;

use App\Controller\Abstracts\Sitemap;

final class FFTracker extends Sitemap
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/sitemap/xml/fftracker.xml', 'name' => 'Index'],
    ];

    /**
     * @var bool Flag indicating the main index file (index.xml)
     */
    #[\Override]
    protected bool $main_index = false;

    /**
     * @var string Query for countables
     */
    #[\Override]
    protected string $query = '
                    SELECT \'ffxiv_characters\' AS `link`, \'FFXIV Characters\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__character` WHERE `hidden` IS NULL
                    UNION ALL
                    SELECT \'ffxiv_freecompanies\' AS `link`, \'FFXIV Free Companies\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__freecompany`
                    UNION ALL
                    SELECT \'ffxiv_linkshells\' AS `link`, \'FFXIV Linkshells\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__linkshell`
                    UNION ALL
                    SELECT \'ffxiv_pvpteams\' AS `link`, \'FFXIV PvP Teams\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__pvpteam`
                    UNION ALL
                    SELECT \'ffxiv_achievements\' AS `link`, \'FFXIV Achievements\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__achievement`
                ';
}
