<?php
declare(strict_types = 1);
namespace Simbiat\About\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Contacts extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/about/contacts', 'name' => 'Contacts']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '(\/about\/contacts)';
    #Regex replace pattern
    protected string $replaceWith = '#contacts';
}
