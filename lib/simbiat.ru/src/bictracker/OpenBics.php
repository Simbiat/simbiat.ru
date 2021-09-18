<?php
declare(strict_types=1);
namespace Simbiat\bictracker;
use Simbiat\Abstracts\Search;

class OpenBics extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'bic';
    #Name of the table to search use
    protected string $table = 'bic__list';
    #List of fields
    protected string $fields = '`BIC` as `id`, `NameP` as `name`, `DateOut`';
    #Optional WHERE clause
    protected string $where = '`DateOut` IS NULL';
    #Condition for search
    protected string $whatToSearch = 'IF(`VKEY`=:what OR `BIC`=:what OR `OLD_NEWNUM`=:what OR `RegN`=:what, 99999, MATCH (`NameP`, `Adr`) AGAINST (:match IN BOOLEAN MODE))';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`NameP` ASC';
}
