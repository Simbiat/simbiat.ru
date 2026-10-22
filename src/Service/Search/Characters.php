<?php

declare(strict_types=1);

namespace App\Service\Search;

final class Characters extends Search
{
    /**
     * @var string Type of entity to return as static value in results (required for frontend routing)
     */
    #[\Override]
    protected string $entity_type = 'character';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'ffxiv__character';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = '`ffxiv__character`.`character_id` as `id`, `name`, `avatar` AS `icon`, `updated`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id`';

    /**
     * @var string Default order (for the main page, for example)
     */
    #[\Override]
    protected string $order_default = '`Updated` DESC';

    /**
     * @var string Order for list pages
     */
    #[\Override]
    protected string $order_list = '`name` ASC';

    /**
     * @var array<int, string> List of FULLTEXT columns. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $fulltext = [
        'name',
        'biography',
    ];

    /**
     * @var array<int, string> List of optional columns for direct comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $exact = [
        'ffxiv__character`.`character_id',
        'name',
    ];

    /**
     * @var array<int, string> List of optional columns for LIKE %% comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $like = [
        'name',
        'biography',
    ];
}
