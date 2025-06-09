<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;

use Simbiat\Website\Abstracts\Search;

class Threads extends Search
{
    #Items to display per page for lists
    public int $listItems = 25;
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'thread';
    #Name of the table to search use
    protected string $table = 'talks__threads';
    #List of fields
    protected string $fields = '`talks__threads`.`thread_id` as `id`, `talks__threads`.`section_id`, `talks__threads`.`name`, `language`, `pinned`, `talks__threads`.`system`, `talks__threads`.`private`, `talks__threads`.`closed`, `talks__threads`.`created`, `talks__threads`.`author`, `talks__threads`.`updated`, `talks__threads`.`editor`, `talks__threads`.`last_post`, `talks__threads`.`last_poster`, `for_creation`.`username` as `author_name`, `for_update`.`username` as `editor_name`, `for_last_post`.`username` as `last_poster_name`, `talks__types`.`type` as `detailedType`, `og_image`, (SELECT `post_id` FROM `talks__posts` WHERE `talks__posts`.`thread_id`=`talks__threads`.`thread_id` ORDER BY `created` ASC LIMIT 1) as `firstPost`, CONCAT(\'/assets/images/uploaded/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) as `icon`';
    #Optional JOIN string, if needed
    protected string $join = 'LEFT JOIN `uc__users` as `for_creation` ON `talks__threads`.`author`=`for_creation`.`user_id` LEFT JOIN `uc__users` as `for_update` ON `talks__threads`.`author`=`for_update`.`user_id` LEFT JOIN `uc__users` as `for_last_post` ON `talks__threads`.`last_poster`=`for_last_post`.`user_id` INNER JOIN `talks__sections` ON `talks__threads`.`section_id`=`talks__sections`.`section_id` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`type_id` LEFT JOIN `sys__files` ON `talks__types`.`icon`=`sys__files`.`file_id`';
    #Default order (for the main page, for example)
    protected string $orderDefault = '`updated` DESC';
    #Order for list pages
    protected string $orderList = '`updated` DESC, `name` ASC';
    #The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
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
