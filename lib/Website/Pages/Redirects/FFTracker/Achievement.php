<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\FFTracker;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Achievement extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/achievements', 'name' => 'Achievements']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/fftracker\/achievement[^s](.*)';
    #Regex to replace with
    protected string $replace_with = '/fftracker/achievements/$1';
}
