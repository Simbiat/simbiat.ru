<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\About;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Contacts extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/about/contacts', 'name' => 'Contacts']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '(\/about\/contacts)';
    #Regex replace pattern
    protected string $replace_with = '#footer_contacts';
}
