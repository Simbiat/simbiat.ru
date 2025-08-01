<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\About;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Website extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/website', 'name' => 'Website']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '(\/about\/website)';
    #Regex replace pattern
    protected string $replace_with = '/about/tech';
}
