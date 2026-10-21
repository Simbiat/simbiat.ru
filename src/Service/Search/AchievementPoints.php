<?php

declare(strict_types=1);

namespace App\Service\Search;

final class AchievementPoints extends Search
{
    // Type of entity to return as static value in results (required for frontend routing)
    #[\Override]
    protected string $entity_type = 'character';

    // Name of the table to search use
    #[\Override]
    protected string $table = 'ffxiv__character';

    // List of fields
    #[\Override]
    protected string $fields = '`ffxiv__character`.`character_id` as `id`, `name`, `avatar` AS `icon`, `updated`, (SELECT `user_id` FROM `uc__user_to_ff_character` WHERE `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`) AS `user_id`, `achievement_points`';

    // Optional WHERE clause for every SELECT
    #[\Override]
    protected string $where = '`achievement_points`>0';

    // Default order (for the main page, for example)
    #[\Override]
    protected string $order_default = '`achievement_points` DESC, `name` ASC';

    // Order for list pages
    #[\Override]
    protected string $order_list = '`achievement_points` DESC, `name` ASC';
}
