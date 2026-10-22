<?php

declare(strict_types=1);

namespace App\Controller\Abstracts;

/**
 * Class for static pages, not requiring database connection.
 */
abstract class StaticPage extends Page
{
    /**
     * @var bool Flag to indicate this is a static page
     */
    #[\Override]
    protected bool $static = true;

    /**
     * @var string Cache strategy: aggressive, private, live, month, week, day, hour
     */
    #[\Override]
    protected string $cache_strategy = 'week';

    /**
     * Static pages have all the data in Twig templates, thus we usually return an empty array.
     *
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        return [];
    }
}
