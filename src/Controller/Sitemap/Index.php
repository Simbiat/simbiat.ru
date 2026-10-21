<?php

declare(strict_types=1);

namespace App\Controller\Sitemap;

use App\Controller\Abstracts\Sitemap;
use Simbiat\Database\Query;

/**
 * Class for the main sitemap index file.
 */
final class Index extends Sitemap
{
    // Cache age, in case we prefer the generated page to be cached
    #[\Override]
    protected int $cache_age = 1440;

    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/sitemap/index.xml', 'name' => 'Index'],
    ];

    // Sub service name
    #[\Override]
    protected string $subservice_name = 'sitemap';

    // Page title. Practically needed only for the main pages of the segment, since will be overridden otherwise
    #[\Override]
    protected string $title = 'Sitemap Index';

    // Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    #[\Override]
    protected string $h1 = 'Sitemap Index';

    // Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    #[\Override]
    protected string $og_desc = 'Sitemap Index';

    // Max elements per sitemap page
    #[\Override]
    protected int $max_elements = 50000;

    // Flag indicating the main index file (index.xml)
    #[\Override]
    protected bool $main_index = true;

    // Query for countables
    #[\Override]
    protected string $query = '
                    SELECT \'threads\' AS `link`, \'Forum Threads\' AS `name`, COUNT(*) AS `count` FROM `talks__threads` WHERE `private`=0 AND `talks__threads`.`published`<=CURRENT_TIMESTAMP(6)
                    UNION ALL
                    SELECT \'users\' AS `link`, \'Users\' AS `name`, COUNT(*) AS `count` FROM `uc__users` WHERE `system`!=1
                    UNION ALL
                    SELECT \'bics\' AS `link`, \'Russian Bank Codes\' AS `name`, COUNT(*) AS `count` FROM `bic__list`
                ';

    /**
     * Generation of the page data.
     *
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        if (
            $this->max_elements > 50000
            || $this->max_elements < 10
        ) {
            $this->max_elements = 50000;
        }

        $this->h2_push = [];
        // Sitemap for general links (non-countable)
        $links = $this->main_index ? [
            ['loc' => 'general.xml', 'name' => 'General links'],
        ] : [];

        // Get countable links
        try {
            $link_types = Query::query($this->query, return: 'all');
        } catch (\Throwable) {
            $link_types = [];
        }

        // Generate links
        foreach ($link_types as $link_type) {
            if ($link_type['count'] <= $this->max_elements) {
                $links[] = ['loc' => $link_type['link'].'.xml', 'name' => $link_type['name']];
            } else {
                $pages = (int) \ceil($link_type['count'] / $this->max_elements);
                for ($page = 1; $page <= $pages; ++$page) {
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
