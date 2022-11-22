<?php
declare(strict_types=1);
namespace Simbiat\Sitemap\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Talks;
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
    #Max elements per sitemap page
    protected int $maxElements = 50000;

    protected function generate(array $path): array
    {
        if ($this->maxElements > 50000 || $this->maxElements < 10) {
            $this->maxElements = 50000;
        }
        $format = $path[0];
        #Slice the path
        $path = array_slice($path, 1);
        if ($format === 'txt' || $format === 'xml') {
            $this->h2push = [];
        }
        #Get page
        if (empty($path[1]) || !is_numeric($path[1]) || $path[1] < 1) {
            $path[1] = 1;
        } else {
            $path[1] = intval($path[1]);
        }
        #Update link of breadcrumb
        $this->breadCrumb[0]['href'] .= $path[0].'/';
        #Set starting position for query
        $start = ($path[1]-1)*$this->maxElements;
        #Set values based on route
        switch($path[0]) {
            case 'bics':
                $this->breadCrumb[0]['name'] = 'Russian Banks';
                $query = 'SELECT CONCAT(\'bictracker/bics/\', `BIC`, \'/\') AS `loc`, `Updated` AS `lastmod`, `NameP` AS `name` FROM `bic__list`'.($format === 'html' ? ' ORDER BY `NameP`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'characters':
                $this->breadCrumb[0]['name'] = 'FFXIV Characters';
                $query = 'SELECT CONCAT(\'fftracker/characters/\', `characterid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__character`'.($format === 'html' ? ' FORCE INDEX (`name_order`) ORDER BY `name`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'freecompanies':
                $this->breadCrumb[0]['name'] = 'FFXIV Free Companies';
                $query = 'SELECT CONCAT(\'fftracker/freecompanies/\', `freecompanyid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__freecompany`'.($format === 'html' ? ' FORCE INDEX (`name_order`) ORDER BY `name`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'linkshells':
                $this->breadCrumb[0]['name'] = 'FFXIV Linkshells';
                $query = 'SELECT CONCAT(\'fftracker/\', IF(`crossworld`=1, \'crossworld_\', \'\'), \'linkshells/\', `linkshellid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell`'.($format === 'html' ? ' FORCE INDEX (`name_order`) ORDER BY `name`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'pvpteams':
                $this->breadCrumb[0]['name'] = 'FFXIV PvP Teams';
                $query = 'SELECT CONCAT(\'fftracker/pvpteams/\', `pvpteamid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__pvpteam`'.($format === 'html' ? ' FORCE INDEX (`name_order`) ORDER BY `name`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'achievements':
                $this->breadCrumb[0]['name'] = 'FFXIV Achievements';
                $query = 'SELECT CONCAT(\'fftracker/achievements/\', `achievementid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__achievement`'.($format === 'html' ? ' FORCE INDEX (`name_order`) ORDER BY `name`' : '').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'threads':
                $this->breadCrumb[0]['name'] = 'Forums Threads';
                $query = 'SELECT CONCAT(\'talks/threads/\', `threadid`, \'/\') AS `loc`, `updated` AS `lastmod`, `name` FROM `talks__threads`'.($format === 'html' ? ' FORCE INDEX (`name_sort`) WHERE `private`=0 AND `talks__threads`.`created`<=CURRENT_TIMESTAMP() ORDER BY `name`' : ' WHERE `private`=0').' LIMIT '.$start.', '.$this->maxElements;
                break;
            case 'users':
                $this->breadCrumb[0]['name'] = 'Users';
                $query = 'SELECT CONCAT(\'talks/users/\', `userid`, \'/\') AS `loc`, `updated` AS `lastmod`, `username` FROM `uc__users`'.($format === 'html' ? ' FORCE INDEX (`username_unique`) WHERE `userid` NOT IN ('.Talks::unknownUserID.', '.Talks::systemUserID.', '.Talks::deletedUserID.') ORDER BY `name`' : ' WHERE `userid` NOT IN ('.Talks::unknownUserID.', '.Talks::systemUserID.', '.Talks::deletedUserID.')').' LIMIT '.$start.', '.$this->maxElements;
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
