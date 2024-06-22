<?php
declare(strict_types = 1);
namespace Simbiat\bictracker\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Legacy extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker/bics', 'name' => 'Legacy']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '\/bic\/(.*)';
    #Regex replace pattern
    protected string $replaceWith = '/bictracker/search?search=$1';
}
