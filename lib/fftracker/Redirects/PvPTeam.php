<?php
declare(strict_types = 1);
namespace Simbiat\fftracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class PvPTeam extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/pvpteams', 'name' => 'PvP Teams']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/pvpteam\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/pvpteams/$1';
}
