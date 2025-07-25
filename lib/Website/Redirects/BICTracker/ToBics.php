<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\BICTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class ToBics extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/bic', 'name' => 'Legacy']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/bictracker\/bic\/(.*)';
    #Regex replace pattern
    protected string $replace_with = '/bictracker/bics/$1';
}
