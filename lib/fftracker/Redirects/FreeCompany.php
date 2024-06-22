<?php
declare(strict_types = 1);
namespace Simbiat\fftracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class FreeCompany extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/freecompanies', 'name' => 'Free Companies']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/fftracker\/freecompany\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/fftracker/freecompanies/$1';
}
