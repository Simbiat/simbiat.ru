<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Page;

/**
 * Class for the main sitemap index file
 */
class Index extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cache_age = 1440;
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/sitemap/index.xml', 'name' => 'Index']
    ];
    #Sub service name
    protected string $subservice_name = 'sitemap';
    #Page title. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $title = 'Sitemap Index';
    #Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'Sitemap Index';
    #Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $og_desc = 'Sitemap Index';
    #Max elements per sitemap page
    protected int $max_elements = 50000;
    #Flag indicating the main index file (index.xml)
    protected bool $main_index = true;
    #Query for countables
    protected string $query = '
                    SELECT \'threads\' AS `link`, \'Forum Threads\' AS `name`, COUNT(*) AS `count` FROM `talks__threads` WHERE `private`=0 AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()
                    UNION ALL
                    SELECT \'users\' AS `link`, \'Users\' AS `name`, COUNT(*) AS `count` FROM `uc__users` WHERE `system`!=1
                    UNION ALL
                    SELECT \'bics\' AS `link`, \'Russian Bank Codes\' AS `name`, COUNT(*) AS `count` FROM `bic__list`
                ';
    
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
        #Sitemap for general links (non-countable)
        if ($this->main_index) {
            $links = [
                ['loc' => 'general.xml', 'name' => 'General links'],
            ];
        } else {
            $links = [];
        }
        #Get countable links
        try {
            $counts = Query::query($this->query, return: 'all');
        } catch (\Throwable) {
            $counts = [];
        }
        #Generate links
        foreach ($counts as $link_type) {
            if ($link_type['count'] <= $this->max_elements) {
                $links[] = ['loc' => $link_type['link'].'.xml', 'name' => $link_type['name']];
            } else {
                $pages = (int)ceil($link_type['count'] / $this->max_elements);
                for ($page = 1; $page <= $pages; $page++) {
                    $links[] = ['loc' => $link_type['link'].'/'.$page.'.xml', 'name' => $link_type['name'].', Page '.$page];
                }
            }
        }
        return [
            'index' => true,
            'sitemap_links' => $links,
        ];
    }
}
