<?php

declare(strict_types=1);

namespace App\Controller\FFXIV;

use App\Service\Search\Achievements;
use App\Service\Search\Characters;
use App\Service\Search\Companies;
use App\Service\Search\CrossworldLinkshells;
use App\Service\Search\Linkshells;
use App\Service\Search\PVP;

final class Search extends \App\Controller\Abstracts\Search
{
    /**
     * Current breadcrumb for navigation
     *
     * @var array<int, array<string, string>>
     */
    #[\Override]
    protected array $breadcrumb = [
        ['href' => '/fftracker/search', 'name' => 'Search'],
    ];

    /**
     * @var string Sub service name
     */
    #[\Override]
    protected string $subservice_name = 'search';

    /**
     * @var string Page title. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $title = 'FFXIV Tracker Search';

    /**
     * @var string Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $h1 = 'FFXIV Tracker Search';

    /**
     * @var string Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
     */
    #[\Override]
    protected string $og_desc = 'FFXIV Tracker Search';

    /**
     * @var array<string, array<string, string>> Linking types to classes
     */
    #[\Override]
    protected array $types = [
        'achievements' => ['name' => 'Achievements', 'class' => Achievements::class],
        'characters' => ['name' => 'Characters', 'class' => Characters::class],
        'crossworld_linkshells' => ['name' => 'Crossworld Linkshells', 'class' => CrossworldLinkshells::class],
        'freecompanies' => ['name' => 'Free Companies', 'class' => Companies::class],
        'linkshells' => ['name' => 'Linkshells', 'class' => Linkshells::class],
        'pvpteams' => ['name' => 'PvP Teams', 'class' => PVP::class],
    ];

    /**
     * @var int Items to display per page for search results per type
     */
    #[\Override]
    protected int $search_items = 6;

    /**
     * @var string Full title to be used for description metatags when having a search value
     */
    #[\Override]
    protected string $full_title = 'Search for `%s` on Final Fantasy XIV Tracker';

    /**
     * @var array<int, string> List of permissions, from which at least 1 is required to have access to the page
     */
    #[\Override]
    protected array $required_permission = ['view_ff'];
}
