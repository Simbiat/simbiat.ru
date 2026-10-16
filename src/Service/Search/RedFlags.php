<?php

declare(strict_types=1);

namespace App\Service\Search;

final class RedFlags extends Search
{
    // Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'character';

    // Name of the table to search use
    protected string $table = 'ffxiv__character';

    // List of fields
    protected string $fields = '`ffxiv__character`.`character_id` as `id`, `name`, `avatar` AS `icon`, `updated`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id`';

    // Optional WHERE clause for every SELECT
    protected string $where = '`ffxiv__character`.`character_id` IN (SELECT `character_id` FROM `ffxiv__red_flags`)';

    // Default order (for the main page, for example)
    protected string $order_default = '`name` ASC';

    // Order for list pages
    protected string $order_list = '`name` ASC';
}
