<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;

use Simbiat\Website\Abstracts\Search;

class Sections extends Search
{
    #Items to display per page for lists
    public int $listItems = 25;
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'section';
    #Name of the table to search use
    protected string $table = 'talks__sections';
    #List of fields
    protected string $fields = '`section_id`, `talks__sections`.`name`, `talks__sections`.`description`, `sequence`,`talks__types`.`type` as `detailedType`, `talks__sections`.`parent_id`, `closed`, `talks__sections`.`system`, `private`, `talks__sections`.`created`, `talks__sections`.`updated`, `talks__sections`.`author`, `talks__sections`.`updated`, `talks__sections`.`editor`, `for_creation`.`username` as `author_name`, `for_update`.`username` as `editor_name`, CONCAT(\'/assets/images/uploaded/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) as `icon`';
    #Optional JOIN string, if needed
    protected string $join = 'INNER JOIN `uc__users` as `for_creation` ON `talks__sections`.`author`=`for_creation`.`user_id` INNER JOIN `uc__users` as `for_update` ON `talks__sections`.`author`=`for_update`.`user_id` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`type_id` INNER JOIN `sys__files` ON `sys__files`.`file_id`=COALESCE(`talks__sections`.`icon`, `talks__types`.`icon`)';
    #Count argument. In some cases, you may want to count a certain column instead of using * (default).
    protected string $countArgument = '`section_id`';
    #Default order (for the main page, for example)
    protected string $orderDefault = '`sequence` DESC, `name` ASC';
    #Order for list pages
    protected string $orderList = '`sequence` DESC, `name` ASC';
    #The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
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
