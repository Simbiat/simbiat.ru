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
        ['href'=>'/sitemap/', 'name'=>'Sitemap: ']
    ];
    #Sub service name
    protected string $subServiceName = 'sitemap';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Sitemap: ';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Sitemap: ';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Sitemap: ';
    #Max elements per sitemap page
    protected int $maxElements = 50000;

    protected function generate(array $path): array
    {
        if ($this->maxElements > 50000 || $this->maxElements < 10) {
            $this->maxElements = 50000;
        }
        $this->h2push = [];
        #Remove potential file extension at the end of path
        if (!empty($path[1])) {
            $path[1] = preg_replace('/\.xml$/ui', '', $path[1]);
        }
        #Get page
        if (empty($path[1]) || !is_numeric($path[1]) || $path[1] < 1) {
            $path[1] = 1;
        } else {
            $path[1] = (int)$path[1];
        }
        #Update link of breadcrumb
        $this->breadCrumb[0]['href'] .= $path[0].'/';
        #Set starting position for query
        $start = ($path[1]-1)*$this->maxElements;
        #Set values based on route
        switch($path[0]) {
            case 'bics':
                $this->breadCrumb[0]['name'] = 'Russian Banks';
                $query = 'SELECT CONCAT(\'bictracker/bics/\', `BIC`) AS `loc`, `Updated` AS `lastmod`, `NameP` AS `name` FROM `bic__list` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'ffxiv_characters':
                $this->breadCrumb[0]['name'] = 'FFXIV Characters';
                $query = 'SELECT CONCAT(\'fftracker/characters/\', `characterid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__character` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'ffxiv_freecompanies':
                $this->breadCrumb[0]['name'] = 'FFXIV Free Companies';
                $query = 'SELECT CONCAT(\'fftracker/freecompanies/\', `freecompanyid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__freecompany` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'ffxiv_linkshells':
                $this->breadCrumb[0]['name'] = 'FFXIV Linkshells';
                $query = 'SELECT CONCAT(\'fftracker/\', IF(`crossworld`=1, \'crossworld_\', \'\'), \'linkshells/\', `linkshellid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'ffxiv_pvpteams':
                $this->breadCrumb[0]['name'] = 'FFXIV PvP Teams';
                $query = 'SELECT CONCAT(\'fftracker/pvpteams/\', `pvpteamid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__pvpteam` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'ffxiv_achievements':
                $this->breadCrumb[0]['name'] = 'FFXIV Achievements';
                $query = 'SELECT CONCAT(\'fftracker/achievements/\', `achievementid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__achievement` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'threads':
                $this->breadCrumb[0]['name'] = 'Forums Threads';
                $query = 'SELECT CONCAT(\'talks/threads/\', `threadid`) AS `loc`, `updated` AS `lastmod`, `name` FROM `talks__threads` WHERE `private`=0 AND `talks__threads`.`created`<=CURRENT_TIMESTAMP() ORDER BY `name` LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'users':
                $this->breadCrumb[0]['name'] = 'Users';
                $query = 'SELECT CONCAT(\'talks/users/\', `userid`) AS `loc`, `updated` AS `lastmod`, `username` as `name` FROM `uc__users` WHERE `userid` NOT IN ('.\Simbiat\Config::userIDs['Unknown user'].', '.\Simbiat\Config::userIDs['System user'].', '.\Simbiat\Config::userIDs['Deleted user'].') ORDER BY `name` LIMIT '.$start.', '.$this->maxElements;
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
