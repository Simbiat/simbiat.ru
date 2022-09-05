<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

class Listing extends \Simbiat\Abstracts\Pages\Listing
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/search', 'name' => 'Search']
    ];
    #Service name for breadcrumbs
    protected string $serviceName = 'fftracker';
    #Sub service name
    protected string $subServiceName = 'search';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'FFXIV Tracker Search';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'FFXIV Tracker Search';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'FFXIV Tracker Search';
    #Linking types to classes
    protected array $types = [
        'characters' => ['name' => 'Characters', 'class' => '\Simbiat\fftracker\Search\Characters'],
        'freecompanies' => ['name' => 'Free Companies', 'class' => '\Simbiat\fftracker\Search\Companies'],
        'pvpteams' => ['name' => 'PvP Teams', 'class' => '\Simbiat\fftracker\Search\PVP'],
        'linkshells' => ['name' => 'Linkshells', 'class' => '\Simbiat\fftracker\Search\Linkshells'],
        'achievements' => ['name' => 'Achievements', 'class' => '\Simbiat\fftracker\Search\Achievements'],
    ];
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Search for `%s` on Final Fantasy XIV Tracker';
}
