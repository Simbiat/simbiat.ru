<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

class FFTracker extends Index
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/sitemap/xml/fftracker.xml', 'name' => 'Index']
    ];
    #Flag indicating the main index file (index.xml)
    protected bool $main_index = false;
    #Query for countables
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
