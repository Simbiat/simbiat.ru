<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;

use Simbiat\FFXIV\Entities\AbstractEntity;
use Simbiat\Website\Abstracts\Search;

class PVP extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'pvpteam';
    #Name of the table to search use
    protected string $table = 'ffxiv__pvpteam';
    #List of fields
    protected string $fields = '`pvp_id` as `id`, `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `updated`';
    #Default order (for the main page, for example)
    protected string $order_default = '`Updated` DESC';
    #Order for list pages
    protected string $order_list = '`name` ASC';
    #THe next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'pvp_id',
        'name',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
    ];
    
    protected function postProcess(array $results): array
    {
        return AbstractEntity::cleanCrestResults($results);
    }
}
