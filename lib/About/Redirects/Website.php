<?php
declare(strict_types = 1);
namespace Simbiat\About\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Website extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/website', 'name' => 'Website']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '(\/about\/website)';
    #Regex replace pattern
    protected string $replaceWith = '#hiThere';
}
