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
    #Language override, to be sent in header (if present)
    protected string $language = 'ru-RU';
}
