<?php

declare(strict_types=1);

namespace App\Service\Search;

final class CrossworldLinkshells extends Search
{
    // Type of entity to return as static value in results (required for frontend routing)
    #[\Override]
    protected string $entity_type = 'linkshell';

    // Name of the table to search use
    #[\Override]
    protected string $table = 'ffxiv__linkshell';

    // List of fields
    #[\Override]
    protected string $fields = '`ls_id` as `id`, `name`, `crossworld`, `updated`';

    // Optional WHERE clause for every SELECT
    #[\Override]
    protected string $where = '`crossworld`=1';

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
    ];

    // List of optional columns for direct comparison
    #[\Override]
    protected array $exact = [
        'ls_id',
        'name',
    ];

    // List of optional columns for LIKE %% comparison
    #[\Override]
    protected array $like = [
        'name',
    ];
}
