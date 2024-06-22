<?php
declare(strict_types=1);
namespace Simbiat\Sitemap\Pages;

class FFTracker extends Index
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/sitemap/xml/fftracker.xml', 'name' => 'Index']
    ];
    #Flag indicating main index file (index.xml)
    protected bool $mainIndex = false;
    #Query for countables
    protected string $query = '
                    SELECT \'ffxiv_characters\' AS `link`, \'FFXIV Characters\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__character`
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
