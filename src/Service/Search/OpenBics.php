<?php

declare(strict_types=1);

namespace App\Service\Search;

final class OpenBics extends Search
{
    /**
     * @var string Type of entity to return as static value in results (required for frontend routing)
     */
    #[\Override]
    protected string $entity_type = 'bic';

    /**
     * @var string Name of the table to search use
     */
    #[\Override]
    protected string $table = 'bic__list';

    /**
     * @var string List of fields
     */
    #[\Override]
    protected string $fields = '`BIC` as `id`, `NameP` as `name`, `DateOut`, `Updated` as `updated`';

    /**
     * @var string Optional WHERE clause for every SELECT
     */
    #[\Override]
    protected string $where = '`DateOut` IS NULL';

    /**
     * @var string Optional WHERE clause for SELECT where search term is defined
     */
    #[\Override]
    protected string $where_search = '`BIC` IN (SELECT `BIC` FROM `bic__swift` WHERE `SWBIC`=:what) OR `BIC` IN (SELECT `BIC` FROM `bic__accounts` WHERE `Account`=:what)';

    /**
     * @var string Default order (for main page, for example)
     */
    #[\Override]
    protected string $order_default = '`Updated` DESC';

    /**
     * @var string Order for list pages
     */
    #[\Override]
    protected string $order_list = '`NameP` ASC';

    /**
     * @var array<int, string> List of FULLTEXT columns. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $fulltext = [
        'NameP',
        'Adr',
    ];

    /**
     * @var array<int, string> List of optional columns for direct comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $exact = [
        'BIC',
        'OLD_NEWNUM',
        'RegN',
        'VKEY',
    ];

    /**
     * @var array<int, string> List of optional columns for LIKE %% comparison. The higher in the list a field is the more weight/relevancy condition with it will have
     */
    #[\Override]
    protected array $like = [
        'NameP',
        'Adr',
    ];
}
