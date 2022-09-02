<?php
declare(strict_types=1);
namespace Simbiat\Sitemap\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Countables extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/sitemap/html/', 'name'=>'Sitemap: ']
    ];
    #Sub service name
    protected string $subServiceName = 'sitemap';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Sitemap: ';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Sitemap: ';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Sitemap: ';

    protected function generate(array $path): array
    {
        #Get page
        if (empty($path[1]) || !is_numeric($path[1]) || $path[1] < 1) {
            $path[1] = 1;
        } else {
            $path[1] = intval($path[1]);
        }
        #Update link of breadcrumb
        $this->breadCrumb[0]['href'] .= $path[0].'/';
        #Set starting position for query
        $start = ($path[1]-1)*50000;
        #Set values based on route
        switch($path[0]) {
            case 'bics':
                $this->breadCrumb[0]['name'] = 'Russian Banks';
                $query = 'SELECT CONCAT(\'bictracker/bics/\', `BIC`, \'/\') AS `loc`, `Updated` AS `lastmod`, `NameP` AS `name` FROM `bic__list` ORDER BY `NameP` LIMIT '.$start.', 50000';
                break;
            case 'characters':
                $this->breadCrumb[0]['name'] = 'FFXIV Characters';
                $query = 'SELECT CONCAT(\'fftracker/characters/\', `characterid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__character` FORCE INDEX (`name_order`) ORDER BY `name` LIMIT '.$start.', 50000';
                break;
            case 'freecompanies':
                $this->breadCrumb[0]['name'] = 'FFXIV Free Companies';
                $query = 'SELECT CONCAT(\'fftracker/freecompanies/\', `freecompanyid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__freecompany` FORCE INDEX (`name_order`) ORDER BY `name` LIMIT '.$start.', 50000';
                break;
            case 'linkshells':
                $this->breadCrumb[0]['name'] = 'FFXIV Linkshells';
                $query = 'SELECT CONCAT(\'fftracker/\', IF(`crossworld`=1, \'crossworld_\', \'\'), \'linkshells/\', `linkshellid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell` FORCE INDEX (`name_order`) ORDER BY `name` LIMIT '.$start.', 50000';
                break;
            case 'pvpteams':
                $this->breadCrumb[0]['name'] = 'FFXIV PvP Teams';
                $query = 'SELECT CONCAT(\'fftracker/pvpteams/\', `pvpteamid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__pvpteam` FORCE INDEX (`name_order`) ORDER BY `name` LIMIT '.$start.', 50000';
                break;
            case 'achievements':
                $this->breadCrumb[0]['name'] = 'FFXIV Achievements';
                $query = 'SELECT CONCAT(\'fftracker/achievements/\', `achievementid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__achievement` FORCE INDEX (`name_order`) ORDER BY `name` LIMIT '.$start.', 50000';
                break;
        }
        #Update name of breadcrumb
        $this->breadCrumb[0]['name'] .= ', Page '.$path[1];
        #Update title and description
        $this->title = $this->h1 = $this->ogdesc = 'Sitemap: '.$this->breadCrumb[0]['name'];
        #Get actual links
        if (!empty($query)) {
            try {
                $links = HomePage::$dbController->selectAll($query);
            } catch (\Throwable) {
                $links = [];
            }
        } else {
            $links = [];
        }
        return [
            'sitemap_links' => $links,
        ];
    }
}
