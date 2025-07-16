<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Page;

/**
 * Class for pages that can be counted (that is, they have multiple items)
 */
class Countables extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cache_age = 1440;
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/sitemap/', 'name' => 'Sitemap: ']
    ];
    #Sub service name
    protected string $subservice_name = 'sitemap';
    #Page title. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $title = 'Sitemap: ';
    #Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'Sitemap: ';
    #Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $og_desc = 'Sitemap: ';
    #Max elements per sitemap page
    protected int $max_elements = 50000;
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        if ($this->max_elements > 50000 || $this->max_elements < 10) {
            $this->max_elements = 50000;
        }
        $this->h2_push = [];
        #Remove potential file extension at the end of a path
        if (!empty($path[1])) {
            $path[1] = preg_replace('/\.xml$/ui', '', $path[1]);
        }
        #Get page
        if (empty($path[1]) || !is_numeric($path[1]) || $path[1] < 1) {
            $path[1] = 1;
        } else {
            $path[1] = (int)$path[1];
        }
        #Update the link of breadcrumb
        $this->breadcrumb[0]['href'] .= $path[0].'/';
        #Set the starting position for the query
        $start = ($path[1] - 1) * $this->max_elements;
        #Set values based on the route
        switch ($path[0]) {
            case 'bics':
                $this->breadcrumb[0]['name'] = 'Russian Banks';
                $query = 'SELECT CONCAT(\'bictracker/bics/\', `BIC`) AS `loc`, `Updated` AS `lastmod`, `NameP` AS `name` FROM `bic__list` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'ffxiv_characters':
                $this->breadcrumb[0]['name'] = 'FFXIV Characters';
                $query = 'SELECT CONCAT(\'fftracker/characters/\', `character_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__character` WHERE `hidden` IS NULL LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'ffxiv_freecompanies':
                $this->breadcrumb[0]['name'] = 'FFXIV Free Companies';
                $query = 'SELECT CONCAT(\'fftracker/freecompanies/\', `fc_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__freecompany` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'ffxiv_linkshells':
                $this->breadcrumb[0]['name'] = 'FFXIV Linkshells';
                $query = 'SELECT CONCAT(\'fftracker/\', IF(`crossworld`=1, \'crossworld_\', \'\'), \'linkshells/\', `ls_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__linkshell` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'ffxiv_pvpteams':
                $this->breadcrumb[0]['name'] = 'FFXIV PvP Teams';
                $query = 'SELECT CONCAT(\'fftracker/pvpteams/\', `pvp_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__pvpteam` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'ffxiv_achievements':
                $this->breadcrumb[0]['name'] = 'FFXIV Achievements';
                $query = 'SELECT CONCAT(\'fftracker/achievements/\', `achievement_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `ffxiv__achievement` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'threads':
                $this->breadcrumb[0]['name'] = 'Forums Threads';
                $query = 'SELECT CONCAT(\'talks/threads/\', `thread_id`) AS `loc`, `updated` AS `lastmod`, `name` FROM `talks__threads` WHERE `private`=0 AND `talks__threads`.`created`<=CURRENT_TIMESTAMP() ORDER BY `name` LIMIT '.$start.', '.$this->max_elements;
                break;
            case 'users':
                $this->breadcrumb[0]['name'] = 'Users';
                $query = 'SELECT CONCAT(\'talks/users/\', `user_id`) AS `loc`, `updated` AS `lastmod`, `username` as `name` FROM `uc__users` WHERE `user_id` NOT IN (SELECT `user_id` FROM `uc__users` WHERE `system`=1) ORDER BY `name` LIMIT '.$start.', '.$this->max_elements;
                break;
        }
        #Update name of breadcrumb
        $this->breadcrumb[0]['name'] .= ', Page '.$path[1];
        #Update title and description
        $this->og_desc = 'Sitemap: '.$this->breadcrumb[0]['name'];
        $this->h1 = $this->og_desc;
        $this->title = $this->og_desc;
        #Get actual links
        if (!empty($query)) {
            try {
                $links = Query::query($query, return: 'all');
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
