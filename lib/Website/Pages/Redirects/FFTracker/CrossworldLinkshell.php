<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class CrossworldLinkshell extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/crossworld_linkshells', 'name' => 'Crossworld Linkshells']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/fftracker\/(crossworldlinkshells|crossworld_?linkshell[^s])(.*)';
    #Regex replace pattern
    protected string $replace_with = '/fftracker/crossworld_linkshells$2';
}
