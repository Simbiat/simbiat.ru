<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;
use Simbiat\Website\Abstracts\Search;

class Characters extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'character';
    #Name of the table to search use
    protected string $table = 'ffxiv__character';
    #List of fields
    protected string $fields = '`ffxiv__character`.`character_id` as `id`, `name`, `avatar` AS `icon`, `updated`, `user_id`';
    #Optional JOIN string, if needed
    protected string $join = 'LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`';
    #Default order (for the main page, for example)
    protected string $order_default = '`Updated` DESC';
    #Order for list pages
    protected string $order_list = '`name` ASC';
    #The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
        'biography',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'ffxiv__character`.`character_id',
        'name',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
        'biography',
    ];
}
