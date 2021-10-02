<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Search;
use Simbiat\Abstracts\Search;

class Achievements extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'achievement';
    #Name of the table to search use
    protected string $table = 'ffxiv__achievement';
    #List of fields
    protected string $fields = '`achievementid` as `id`, `name`, `icon`';
    #Optional WHERE clause
    protected string $where = '';
    #Condition for search
    protected string $whatToSearch = 'IF(`achievementid` = :what, 99999, MATCH (`name`, `howto`) AGAINST (:match IN BOOLEAN MODE))';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`name` ASC';
}
