<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class PvPTeam extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/pvpteams', 'name' => 'PvP Teams']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/fftracker\/pvpteam[^s](.*)';
    #Regex replace pattern
    protected string $replace_with = '/fftracker/pvpteams/$1';
}
