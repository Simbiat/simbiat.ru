<?php

declare(strict_types=1);

namespace App\Service\Search;

final class Achievements extends Search
{
    // Type of entity to return as static value in results (required for frontend routing)
    #[\Override]
    protected string $entity_type = 'achievement';

    // Name of the table to search use
    #[\Override]
    protected string $table = 'ffxiv__achievement';

    // List of fields
    #[\Override]
    protected string $fields = '`achievement_id` as `id`, `name`, `icon`, `updated`';

    // Default order (for the main page, for example)
    #[\Override]
    protected string $order_default = '`Updated` DESC';

    // Order for list pages
    #[\Override]
    protected string $order_list = '`name` ASC';

    // The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    // the more weight/relevancy condition with it will have (if true)
    // List of FULLTEXT columns
    #[\Override]
    protected array $fulltext = [
        'name',
        'how_to',
    ];

    // List of optional columns for direct comparison
    #[\Override]
    protected array $exact = [
        'achievement_id',
        'name',
    ];

    // List of optional columns for LIKE %% comparison
    #[\Override]
    protected array $like = [
        'name',
        'how_to',
    ];
}
