<?php
declare(strict_types=1);
namespace Simbiat\Talks\Search;
use Simbiat\Abstracts\Search;

class Threads extends Search
{
    #Items to display per page for lists
    protected int $listItems = 25;
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'thread';
    #Name of the table to search use
    protected string $table = 'talks__threads';
    #List of fields
    protected string $fields = '`talks__threads`.`threadid` as `id`, `talks__threads`.`sectionid`, `talks__threads`.`name`, `language`, `pinned`, `talks__threads`.`system`, `talks__threads`.`private`, `talks__threads`.`closed`, `talks__threads`.`created`, `talks__threads`.`createdby`, `talks__threads`.`updated`, `talks__threads`.`updatedby`, `for_creation`.`username` as `createdby_name`, `for_update`.`username` as `updatedby_name`, `talks__types`.`type` as `detailedType`, `talks__types`.`icon`, `ogimage`, (SELECT `postid` FROM `talks__posts` WHERE `talks__posts`.`threadid`=`talks__threads`.`threadid` AND `talks__posts`.`created`<=CURRENT_TIMESTAMP() ORDER BY `createdby` ASC LIMIT 1) as `firstPost`';
    #Optional JOIN string, in case it's needed
    protected string $join = 'LEFT JOIN `uc__users` as `for_creation` ON `talks__threads`.`createdby`=`for_creation`.`userid` LEFT JOIN `uc__users` as `for_update` ON `talks__threads`.`createdby`=`for_update`.`userid` INNER JOIN `talks__sections` ON `talks__threads`.`sectionid`=`talks__sections`.`sectionid` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`typeid`';
    #Default order (for main page, for example)
    protected string $orderDefault = '`updated` DESC';
    #Order for list pages
    protected string $orderList = '`updated` DESC, `name` ASC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
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
