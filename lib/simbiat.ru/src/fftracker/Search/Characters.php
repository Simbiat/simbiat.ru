<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Search;
use Simbiat\Abstracts\Search;

class Characters extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'character';
    #Name of the table to search use
    protected string $table = 'ffxiv__character';
    #List of fields
    protected string $fields = '`characterid` as `id`, `name`, `avatar` AS `icon`';
    #Optional WHERE clause
    protected string $where = '';
    #Condition for search
    protected string $whatToSearch = 'IF(`characterid` = :what, 99999, MATCH (`name`, `biography`) AGAINST (:match IN BOOLEAN MODE))';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`name` ASC';
}
