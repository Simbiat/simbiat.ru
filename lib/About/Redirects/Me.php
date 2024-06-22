<?php
declare(strict_types = 1);
namespace Simbiat\About\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Me extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/me', 'name' => 'Me']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '(\/about\/me)';
    #Regex replace pattern
    protected string $replaceWith = '#hiThere';
}
