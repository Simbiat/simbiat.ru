<?php
declare(strict_types = 1);
namespace Simbiat\fftracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class CrossworldLinkshell extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/crossworld_linkshells', 'name' => 'Crossworld Linkshells']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/crossworld_?linkshell\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/crossworld_linkshells/$1';
}
