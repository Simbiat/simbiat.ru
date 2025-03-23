<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Linkshell extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/linkshell[^s](.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/linkshells/$1';
}
