<?php
declare(strict_types = 1);

namespace App\Controller\Redirects\BICTracker;

use App\Controller\Abstracts\Redirect;

class ToBics extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/bictracker/bic', 'name' => 'Legacy']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '\/bictracker\/bic\/(.*)';
    #Regex replace pattern
    protected string $replace_with = '/bictracker/bics/$1';
}
