<?php
declare(strict_types = 1);

namespace Simbiat\Website\Redirects\Games;

use Simbiat\Website\Abstracts\Pages\Redirect;

class Anti extends Redirect
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/games/anti', 'name' => 'AntiI']
    ];
    #Regex match pattern with / and flags
    protected string $search_for = '(\/games\/anti)';
    #Regex replace pattern
    protected string $replace_with = '/games/radicalresonance';
}
