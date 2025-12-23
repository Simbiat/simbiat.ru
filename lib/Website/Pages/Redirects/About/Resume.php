<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Redirects\About;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Resume extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/resume', 'name' => 'Resume']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '(\/about\/resume)';
    #Regex replace pattern
    protected string $replace_with = '/about/me';
}
