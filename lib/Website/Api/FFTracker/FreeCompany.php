<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class FreeCompany extends General
{
    #Entity class name
    protected string $entityClass = \Simbiat\Website\fftracker\FreeCompany::class;
    #Name to show in errors
    protected string $nameForErrors = 'Free Company';
    #Name for links
    protected string $nameForLinks = 'freecompany';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Free Company',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
