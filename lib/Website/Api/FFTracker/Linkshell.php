<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class Linkshell extends General
{
    #Entity class name
    protected string $entityClass = \Simbiat\Website\fftracker\Linkshell::class;
    #Name to show in errors
    protected string $nameForErrors = 'Linkshell';
    #Name for links
    protected string $nameForLinks = 'linkshell';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Linkshell',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
