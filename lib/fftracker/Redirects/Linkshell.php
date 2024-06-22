<?php
declare(strict_types = 1);
namespace Simbiat\fftracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Linkshell extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/linkshells', 'name' => 'Linkshells']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/linkshell\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/linkshells/$1';
}
