<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;
use Simbiat\Website\Abstracts\Search;

class OpenBics extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'bic';
    #Name of the table to search use
    protected string $table = 'bic__list';
    #List of fields
    protected string $fields = '`BIC` as `id`, `NameP` as `name`, `DateOut`, `Updated` as `updated`';
    #Optional WHERE clause for every SELECT
    protected string $where = '`DateOut` IS NULL';
    #Optional WHERE clause for SELECT where search term is defined
    protected string $whereSearch = '`BIC` IN (SELECT `BIC` FROM `bic__swift` WHERE `SWBIC`=:what) OR `BIC` IN (SELECT `BIC` FROM `bic__accounts` WHERE `Account`=:what)';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`NameP` ASC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'NameP',
        'Adr',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'BIC',
        'OLD_NEWNUM',
        'RegN',
        'VKEY',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'NameP',
        'Adr',
    ];
}
