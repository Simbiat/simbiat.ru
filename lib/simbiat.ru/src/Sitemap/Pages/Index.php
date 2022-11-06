<?php
declare(strict_types=1);
namespace Simbiat\Sitemap\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Index extends Page
{
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 1440;
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/sitemap/html/index/', 'name'=>'Index']
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

    protected function generate(array $path): array
    {
        if ($this->maxElements > 50000 || $this->maxElements < 10) {
            $this->maxElements = 50000;
        }
        if ($path[0] === 'txt' || $path[0] === 'xml') {
            $this->h2push = [];
        }
        #Sitemap for general links (non-countable)
        $links = [
            ['loc'=>'general/', 'name'=>'General links'],
        ];
        #Get countable links
        try {
            $counts = HomePage::$dbController->selectAll('
                SELECT \'bics\' AS `link`, \'Russian Bank Codes\' AS `name`, COUNT(*) AS `count` FROM `bic__list`
                UNION ALL
                SELECT \'characters\' AS `link`, \'FFXIV Characters\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__character`
                UNION ALL
                SELECT \'freecompanies\' AS `link`, \'FFXIV Free Companies\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__freecompany`
                UNION ALL
                SELECT \'linkshells\' AS `link`, \'FFXIV Linkshells\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__linkshell`
                UNION ALL
                SELECT \'pvpteams\' AS `link`, \'FFXIV PvP Teams\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__pvpteam`
                UNION ALL
                SELECT \'achievements\' AS `link`, \'FFXIV Achievements\' AS `name`, COUNT(*) AS `count` FROM `ffxiv__achievement`
                UNION ALL
                SELECT \'threads\' AS `link`, \'Forum Threads\' AS `name`, COUNT(*) AS `count` FROM `talks__threads` WHERE `private`=0
                UNION ALL
                SELECT \'users\' AS `link`, \'Users\' AS `name`, COUNT(*) AS `count` FROM `uc__users` WHERE `userid`!=1
            ');
        } catch (\Throwable) {
            $counts = [];
        }
        #Generate links
        foreach ($counts as $linkType) {
            if ($linkType['count'] <= $this->maxElements) {
                $links[] = ['loc'=>$linkType['link'].'/', 'name'=>$linkType['name']];
            } else {
                $pages = intval(ceil($linkType['count']/$this->maxElements));
                for ($page = 1; $page <= $pages; $page++) {
                    $links[] = ['loc'=>$linkType['link'].'/'.$page.'/', 'name'=>$linkType['name'].', Page '.$page];
                }
            }
        }
        return [
            'index' => true,
            'sitemap_links' => $links,
        ];
    }
}
