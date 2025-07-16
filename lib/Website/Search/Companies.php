<?php
declare(strict_types = 1);

namespace Simbiat\Website\Search;
use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Search;

class Companies extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = 'freecompany';
    #Name of the table to search use
    protected string $table = 'ffxiv__freecompany';
    #List of fields
    protected string $fields = '`fc_id` as `id`, `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `gc_id`, `updated`';
    #Default order (for the main page, for example)
    protected string $order_default = '`Updated` DESC';
    #Order for list pages
    protected string $order_list = '`name` ASC';
    #The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
        'tag',
        'slogan',
        'estate_zone',
        'estate_message',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'fc_id',
        'name',
        'tag',
        'estate_zone',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
        'tag',
        'slogan',
        'estate_zone',
        'estate_message',
    ];
    
    protected function postProcess(array $results): array
    {
        return AbstractTrackerEntity::cleanCrestResults($results);
    }
}
