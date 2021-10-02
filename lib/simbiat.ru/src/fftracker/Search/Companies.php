<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Search;
use Simbiat\Abstracts\Search;

class Companies extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'freecompany';
    #Name of the table to search use
    protected string $table = 'ffxiv__freecompany';
    #List of fields
    protected string $fields = '`freecompanyid` as `id`, `name`, `crest` AS `icon`';
    #Optional WHERE clause
    protected string $where = '';
    #Condition for search
    protected string $whatToSearch = 'IF(`freecompanyid` = :what, 99999, MATCH (`name`, `tag`, `slogan`, `estate_zone`, `estate_message`) AGAINST (:match IN BOOLEAN MODE))';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`name` ASC';
}
