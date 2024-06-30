<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Search;
use Simbiat\Website\Abstracts\Search;
use Simbiat\Website\fftracker\Entity;

class PVP extends Search
{
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = 'pvpteam';
    #Name of the table to search use
    protected string $table = 'ffxiv__pvpteam';
    #List of fields
    protected string $fields = '`pvpteamid` as `id`, `name`, `crest_part_1`, `crest_part_2`, `crest_part_3`, `updated`';
    #Default order (for main page, for example)
    protected string $orderDefault = '`Updated` DESC';
    #Order for list pages
    protected string $orderList = '`name` ASC';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [
        'name',
    ];
    #List of optional columns for direct comparison
    protected array $exact = [
        'pvpteamid',
        'name',
    ];
    #List of optional columns for LIKE %% comparison
    protected array $like = [
        'name',
    ];
    
    protected function postProcess(array $results): array
    {
        return Entity::cleanCrestResults($results);
    }
}
