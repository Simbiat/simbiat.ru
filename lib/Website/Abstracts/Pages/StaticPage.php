<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Abstracts\Page;

/**
 * Class for static pages, not requiring database connection
 */
class StaticPage extends Page
{
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'week';
    
    /**
     * Static pages have all the data in Twig templates, thus we just return empty array
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        return [];
    }
}
