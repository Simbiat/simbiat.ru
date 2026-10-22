<?php

declare(strict_types=1);

namespace App\Service\Search;

final class Posts extends Search
{
    /**
     * @var int Items to display per page for lists
     */
    #[\Override]
    public int $list_items = 50;

    /**
     * @var string Type of entity to return as static value in results (required for frontend routing)
     */
    #[\Override]
    protected string $entity_type = 'post';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'talks__posts';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = "`talks__posts`.`post_id` as `id`, `talks__posts`.`thread_id`, `talks__threads`.`name`, `talks__threads`.`og_image`, `talks__types`.`type` as `detailed_type`, `reply_to`, `locked`, `talks__posts`.`system`, `talks__posts`.`created`, `talks__posts`.`published`, `talks__posts`.`author`, `talks__posts`.`updated`, `talks__posts`.`editor`, `text`, `for_creation`.`username` as `author_name`, `for_update`.`username` as `editor_name`, COALESCE((SELECT CONCAT('/assets/images/avatars/', SUBSTRING(`sys__files`.`file_id`, 1, 2), '/', SUBSTRING(`sys__files`.`file_id`, 3, 2), '/', SUBSTRING(`sys__files`.`file_id`, 5, 2), '/', `sys__files`.`file_id`, '.', `sys__files`.`extension`) AS `avatar` FROM `uc__avatars` LEFT JOIN `sys__files` ON `uc__avatars`.`file_id`=`sys__files`.`file_id` WHERE `uc__avatars`.`user_id`=`for_creation`.`user_id` AND `uc__avatars`.`current`=1), '/assets/images/avatar.svg') AS `avatar`, COUNT(CASE WHEN `talks__likes`.`like_value` < 0 THEN 1 END) as `dislikes`, COUNT(CASE WHEN `talks__likes`.`like_value` > 0 THEN 1 END) as `likes`, COALESCE(MAX(CASE WHEN `talks__likes`.`user_id` = :user_id THEN `talks__likes`.`like_value` END), 0) as `is_liked`";

    /**
     * @var string Optional JOIN string, if needed
     */
    #[\Override]
    protected string $join = 'LEFT JOIN `uc__users` as `for_creation` ON `talks__posts`.`author`=`for_creation`.`user_id` LEFT JOIN `uc__users` as `for_update` ON `talks__posts`.`editor`=`for_update`.`user_id` INNER JOIN `talks__threads` ON `talks__posts`.`thread_id`=`talks__threads`.`thread_id` INNER JOIN `talks__sections` ON `talks__sections`.`section_id`=`talks__threads`.`section_id` INNER JOIN `talks__types` ON `talks__sections`.`type`=`talks__types`.`type_id` LEFT JOIN `talks__likes` ON `talks__posts`.`post_id` = `talks__likes`.`post_id`';

    /**
     * @var string Optional GROUP BY
     */
    #[\Override]
    protected string $group_by = '`talks__posts`.`post_id`';

    /**
     * @var string Default order (for the main page, for example)
     */
    #[\Override]
    protected string $order_default = '`talks__posts`.`updated` DESC';

    /**
     * @var string Order for list pages
     */
    #[\Override]
    protected string $order_list = '`talks__posts`.`published` DESC';

    /**
     * @var array<int, string> List of FULLTEXT columns. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $fulltext = [
        'text',
    ];

    /**
     * @var array<int, string> List of optional columns for direct comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $exact = [
        'text',
    ];

    /**
     * @var array<int, string> List of optional columns for LIKE %% comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $like = [
        'text',
    ];
}
