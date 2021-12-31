<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Pages;

class Listing extends Search
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/bictracker', 'name' => 'БИК Трекер']
    ];
    protected string $pageWord = 'страница';
    protected bool $list = true;
}
