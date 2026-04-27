<?php
declare(strict_types = 1);

namespace App\Controller\FFXIV;

use App\Service\Search\Achievements;
use App\Service\Search\Characters;
use App\Service\Search\Companies;
use App\Service\Search\CrossworldLinkshells;
use App\Service\Search\Linkshells;
use App\Service\Search\PVP;

class Search extends \App\Controller\Abstracts\Search
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/search', 'name' => 'Search']
    ];
    #Sub service name
    protected string $subservice_name = 'search';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'FFXIV Tracker Search';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'FFXIV Tracker Search';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'FFXIV Tracker Search';
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
    protected int $search_items = 6;
    #Full title to be used for description metatags when having a search value
    protected string $full_title = 'Search for `%s` on Final Fantasy XIV Tracker';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
}
