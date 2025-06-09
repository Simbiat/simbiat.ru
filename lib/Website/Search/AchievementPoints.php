<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;
use Simbiat\Website\Abstracts\Search;

class AchievementPoints extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'character';
    #Name of the table to search use
    protected string $table = 'ffxiv__character';
    #List of fields
    protected string $fields = '`ffxiv__character`.`character_id` as `id`, `name`, `avatar` AS `icon`, `updated`, `user_id`, `achievement_points`';
    #Optional JOIN string, if needed
    protected string $join = 'LEFT JOIN `uc__user_to_ff_character` ON `uc__user_to_ff_character`.`character_id`=`ffxiv__character`.`character_id`';
    #Optional WHERE clause for every SELECT
    protected string $where = '`achievement_points`>0';
    #Default order (for the main page, for example)
    protected string $orderDefault = '`achievement_points` DESC, `name` ASC';
    #Order for list pages
    protected string $orderList = '`achievement_points` DESC, `name` ASC';
}
