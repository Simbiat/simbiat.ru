<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class FreeCompany extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/freecompanies', 'name' => 'Free Companies']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/fftracker\/freecompany\/(.*)';
    #Regex replace pattern
    protected string $replace_with = '/fftracker/freecompanies/$1';
}
