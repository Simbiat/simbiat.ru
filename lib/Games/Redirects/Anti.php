<?php
declare(strict_types = 1);
namespace Simbiat\Games\Redirects;

use Simbiat\Abstracts\Pages\Redirect;

class Anti extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/games/anti', 'name' => 'AntiI']
    ];
    #Regex match pattern with / and flags
    protected string $searchFor = '(\/games\/anti)';
    #Regex replace pattern
    protected string $replaceWith = '/games/radicalresonance';
}
