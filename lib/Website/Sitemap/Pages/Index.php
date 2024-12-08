<?php
declare(strict_types = 1);

namespace Simbiat\Website\Sitemap\Pages;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

/**
 * Class for the main sitemap index file
 */
class Index extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/sitemap/index.xml', 'name' => 'Index']
    ];
    #Sub service name
    protected string $subServiceName = 'sitemap';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Sitemap Index';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Sitemap Index';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Sitemap Index';
    #Max elements per sitemap page
    protected int $maxElements = 50000;
    #Flag indicating main index file (index.xml)
    protected bool $mainIndex = true;
    #Query for countables
    protected string $query = '
                    SELECT \'threads\' AS `link`, \'Forum Threads\' AS `name`, COUNT(*) AS `count` FROM `talks__threads` WHERE `private`=0 AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()
                    UNION ALL
                    SELECT \'users\' AS `link`, \'Users\' AS `name`, COUNT(*) AS `count` FROM `uc__users` WHERE `userid` NOT IN ('.Config::userIDs['Unknown user'].', '.Config::userIDs['System user'].', '.Config::userIDs['Deleted user'].')
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
        if ($this->maxElements > 50000 || $this->maxElements < 10) {
            $this->maxElements = 50000;
        }
        $this->h2push = [];
        #Sitemap for general links (non-countable)
        if ($this->mainIndex) {
            $links = [
                ['loc' => 'general.xml', 'name' => 'General links'],
            ];
        } else {
            $links = [];
        }
        #Get countable links
        try {
            $counts = Config::$dbController->selectAll($this->query);
        } catch (\Throwable) {
            $counts = [];
        }
        #Generate links
        foreach ($counts as $linkType) {
            if ($linkType['count'] <= $this->maxElements) {
                $links[] = ['loc' => $linkType['link'].'.xml', 'name' => $linkType['name']];
            } else {
                $pages = (int)ceil($linkType['count'] / $this->maxElements);
                for ($page = 1; $page <= $pages; $page++) {
                    $links[] = ['loc' => $linkType['link'].'/'.$page.'.xml', 'name' => $linkType['name'].', Page '.$page];
                }
            }
        }
        return [
            'index' => true,
            'sitemap_links' => $links,
        ];
    }
}
