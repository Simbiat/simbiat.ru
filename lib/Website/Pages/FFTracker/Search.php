<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker;

use Simbiat\Website\Search\Achievements;
use Simbiat\Website\Search\Characters;
use Simbiat\Website\Search\Companies;
use Simbiat\Website\Search\CrossworldLinkshells;
use Simbiat\Website\Search\Linkshells;
use Simbiat\Website\Search\PVP;

class Search extends \Simbiat\Website\Abstracts\Pages\Search
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/search', 'name' => 'Search']
    ];
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
        'characters' => ['name' => 'Characters', 'class' => Characters::class],
        'freecompanies' => ['name' => 'Free Companies', 'class' => Companies::class],
        'pvpteams' => ['name' => 'PvP Teams', 'class' => PVP::class],
        'linkshells' => ['name' => 'Linkshells', 'class' => Linkshells::class],
        'crossworld_linkshells' => ['name' => 'Crossworld Linkshells', 'class' => CrossworldLinkshells::class],
        'achievements' => ['name' => 'Achievements', 'class' => Achievements::class],
    ];
    #Items to display per page for search results per type
    protected int $searchItems = 6;
    #Full title to be used for description metatags when having a search value
    protected string $fullTitle = 'Search for `%s` on Final Fantasy XIV Tracker';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewFF'];
}
