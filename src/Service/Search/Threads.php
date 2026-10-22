<?php

declare(strict_types=1);

namespace App\Service\Search;

final class Threads extends Search
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
    protected string $entity_type = 'thread';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'talks__threads';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = "`talks__threads`.`thread_id` as `id`, `talks__threads`.`section_id`, `talks__threads`.`name`, `language`, `pinned`, `talks__threads`.`system`, `talks__threads`.`private`, `talks__threads`.`closed`, `talks__threads`.`created`, `talks__threads`.`published`, `talks__threads`.`author`, `talks__threads`.`updated`, `talks__threads`.`editor`, `talks__threads`.`last_post`, `talks__threads`.`last_poster`, `for_creation`.`username` as `author_name`, `for_update`.`username` as `editor_name`, `for_last_post`.`username` as `last_poster_name`, `talks__types`.`type` as `detailed_type`, `og_image`, (SELECT `post_id` FROM `talks__posts` WHERE `talks__posts`.`thread_id`=`talks__threads`.`thread_id` ORDER BY `published` ASC LIMIT 1) as `first_post`, CONCAT('/assets/images/uploaded/', SUBSTRING(`sys__files`.`file_id`, 1, 2), '/', SUBSTRING(`sys__files`.`file_id`, 3, 2), '/', SUBSTRING(`sys__files`.`file_id`, 5, 2), '/', `sys__files`.`file_id`, '.', `sys__files`.`extension`) as `icon`";

    /**
     * @var string Optional JOIN string, if needed
     */
    #[\Override]
    protected string $join = 'LEFT JOIN `uc__users` as `for_creation` ON `talks__threads`.`author`=`for_creation`.`user_id` LEFT JOIN `uc__users` as `for_update` ON `talks__threads`.`author`=`for_update`.`user_id` LEFT JOIN `uc__users` as `for_last_post` ON `talks__threads`.`last_poster`=`for_last_post`.`user_id` INNER JOIN `talks__sections` ON `talks__threads`.`section_id`=`talks__sections`.`section_id` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`type_id` LEFT JOIN `sys__files` ON `talks__types`.`icon`=`sys__files`.`file_id`';

    /**
     * @var string Default order (for the main page, for example)
     */
    #[\Override]
    protected string $order_default = '`updated` DESC';

    /**
     * @var string Order for list pages
     */
    #[\Override]
    protected string $order_list = '`updated` DESC, `name` ASC';

    /**
     * @var array<int, string> List of FULLTEXT columns. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $fulltext = [
        'name',
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
