<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

class Listing extends Search
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker', 'name' => 'FFXIV Tracker']
    ];
    protected bool $list = true;
}
