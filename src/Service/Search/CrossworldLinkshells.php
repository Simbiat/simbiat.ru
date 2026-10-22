<?php

declare(strict_types=1);

namespace App\Service\Search;

final class CrossworldLinkshells extends Search
{
    /**
     * @var string Type of entity to return as static value in results (required for frontend routing)
     */
    #[\Override]
    protected string $entity_type = 'linkshell';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'ffxiv__linkshell';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = '`ls_id` as `id`, `name`, `crossworld`, `updated`';

    /**
     * @var string Optional WHERE clause for every SELECT
     */
    #[\Override]
    protected string $where = '`crossworld`=1';

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
    ];

    /**
     * @var array<int, string> List of optional columns for direct comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $exact = [
        'ls_id',
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
