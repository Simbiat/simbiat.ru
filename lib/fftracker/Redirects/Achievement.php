<?php
declare(strict_types = 1);
namespace Simbiat\fftracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Achievement extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/achievements', 'name' => 'Achievements']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/achievement\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/achievements/$1';
}
