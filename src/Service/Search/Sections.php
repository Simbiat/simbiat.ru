<?php

declare(strict_types=1);

namespace App\Service\Search;

final class Sections extends Search
{
    /**
     * @var int Items to display per page for lists
     */
    #[\Override]
    public int $list_items = 25;

    /**
     * @var string Type of entity to return as static value in results (required for frontend routing)
     */
    #[\Override]
    protected string $entity_type = 'section';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'talks__sections';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = "`section_id`, `talks__sections`.`name`, `talks__sections`.`description`, `sequence`,`talks__types`.`type` as `detailed_type`, `talks__sections`.`parent_id`, `closed`, `talks__sections`.`system`, `private`, `talks__sections`.`created`, `talks__sections`.`published`, `talks__sections`.`updated`, `talks__sections`.`author`, `talks__sections`.`updated`, `talks__sections`.`editor`, `for_creation`.`username` as `author_name`, `for_update`.`username` as `editor_name`, CONCAT('/assets/images/uploaded/', SUBSTRING(`sys__files`.`file_id`, 1, 2), '/', SUBSTRING(`sys__files`.`file_id`, 3, 2), '/', SUBSTRING(`sys__files`.`file_id`, 5, 2), '/', `sys__files`.`file_id`, '.', `sys__files`.`extension`) as `icon`";

    /**
     * @var string Optional JOIN string, if needed
     */
    #[\Override]
    protected string $join = 'INNER JOIN `uc__users` as `for_creation` ON `talks__sections`.`author`=`for_creation`.`user_id` INNER JOIN `uc__users` as `for_update` ON `talks__sections`.`author`=`for_update`.`user_id` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`type_id` INNER JOIN `sys__files` ON `sys__files`.`file_id`=COALESCE(`talks__sections`.`icon`, `talks__types`.`icon`)';

    /**
     * @var string Count argument. In some cases, you may want to count a certain column instead of using * (default).
     */
    #[\Override]
    protected string $count_argument = '`section_id`';

    /**
     * @var string Default order (for the main page, for example)
     */
    #[\Override]
    protected string $order_default = '`sequence` DESC, `name` ASC';

    /**
     * @var string Order for list pages
     */
    #[\Override]
    protected string $order_list = '`sequence` DESC, `name` ASC';

    /**
     * @var array<int, string> List of FULLTEXT columns. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $fulltext = [
        'name',
        'description',
    ];

    /**
     * @var array<int, string> List of optional columns for direct comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $exact = [
        'name',
    ];

    /**
     * @var array<int, string> List of optional columns for LIKE %% comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $like = [
        'name',
    ];
}
