<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

class StaticPage extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [];
    #Sub service name
    protected string $subServiceName = '';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = '';
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 0;

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        return [];
    }
}
