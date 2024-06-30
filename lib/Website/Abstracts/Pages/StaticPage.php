<?php
declare(strict_types=1);
namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Abstracts\Page;

class StaticPage extends Page
{
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Cache age set to 0 by default, because there is normally no need to cache static pages
    protected int $cacheAge = 0;
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'week';

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        return [];
    }
}
