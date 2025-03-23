<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\About;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Resume extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/resume', 'name' => 'Resume']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '(\/about\/resume)';
    #Regex replace pattern
    protected string $replaceWith = '/about/me';
}
