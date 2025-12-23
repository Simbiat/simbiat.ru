<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\BICTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Legacy extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/bics', 'name' => 'Legacy']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/bic\/(.*)';
    #Regex replace pattern
    protected string $replace_with = '/bictracker/search?search=$1';
}
