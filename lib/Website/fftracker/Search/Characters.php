<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Search;
use Simbiat\Website\Abstracts\Search;

class Characters extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'character';
    #Name of the table to search use
    protected string $table = 'ffxiv__character';
    #List of fields
    protected string $fields = '`characterid` as `id`, `name`, `avatar` AS `icon`, `updated`, `userid`';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`name` ASC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
        'biography',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'characterid',
        'name',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
        'biography',
    ];
}
