<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

class CrossworldLinkshell extends Linkshell
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Sub service name
    protected string $subServiceName = 'linkshell';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Crossworld Linkshell';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Crossworld Linkshell';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Crossworld Linkshell';
    protected const crossworld = true;
}
