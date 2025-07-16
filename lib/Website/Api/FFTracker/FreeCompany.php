<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class FreeCompany extends General
{
    #Entity class name
    protected string $entity_class = \Simbiat\FFXIV\FreeCompany::class;
    #Name to show in errors
    protected string $name_for_errors = 'Free Company';
    #Name for links
    protected string $name_for_links = 'freecompany';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Free Company',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
