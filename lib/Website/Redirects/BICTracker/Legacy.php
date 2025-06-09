<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\BICTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Legacy extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/bics', 'name' => 'Legacy']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/bic\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/bictracker/search?search=$1';
}
