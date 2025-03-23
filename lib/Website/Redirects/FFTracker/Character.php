<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Character extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/characters', 'name' => 'Characters']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/character[^s](.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/characters/$1';
}
