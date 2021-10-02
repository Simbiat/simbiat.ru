<?php
declare(strict_types=1);
namespace Simbiat\About\Pages;

use Simbiat\Abstracts\Page;

class Changelog extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/changelog', 'name' => 'Changelog']
    ];
    #Sub service name
    protected string $subServiceName = 'changelog';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Changelog';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Changelog';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Changelog';

    protected function generate(array $path): array
    {
        return [];
    }
}
