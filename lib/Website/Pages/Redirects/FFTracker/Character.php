<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Character extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/characters', 'name' => 'Characters']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/fftracker\/character[^s](.*)';
    #Regex replace pattern
    protected string $replace_with = '/fftracker/characters/$1';
}
