<?php
declare(strict_types=1);
namespace Simbiat\Talks\Search;
use Simbiat\Abstracts\Search;

class Sections extends Search
{
    #Items to display per page for lists
    protected int $listItems = 25;
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'section';
    #Name of the table to search use
    protected string $table = 'talks__sections';
    #List of fields
    protected string $fields = '`sectionid`, `name`, `talks__sections`.`description`, `talks__types`.`type`, `talks__sections`.`parentid`, `closed`, `system`, `private`, `talks__sections`.`created`, `talks__sections`.`updated`, `talks__sections`.`createdby`, `talks__sections`.`updated`, `talks__sections`.`updatedby`, `for_creation`.`username` as `createdby_name`, `for_update`.`username` as `updatedby_name`, COALESCE(`talks__sections`.`icon`, `talks__types`.`icon`) as `icon`';
    #Optional JOIN string, in case it's needed
    protected string $join = 'INNER JOIN `uc__users` as `for_creation` ON `talks__sections`.`createdby`=`for_creation`.`userid` INNER JOIN `uc__users` as `for_update` ON `talks__sections`.`createdby`=`for_update`.`userid` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`typeid`';
    #Count argument. In some cases you may want to count a certain column, instead of using * (default).
    protected string $countArgument = '`sectionid`';
    #Default order (for main page, for example)
    protected string $orderDefault = '`sequence` DESC, `name` ASC';
    #Order for list pages
    protected string $orderList = '`sequence` DESC, `name` ASC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
        'description',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'name',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
    ];
}
