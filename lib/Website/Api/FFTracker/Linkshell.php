<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class Linkshell extends General
{
    #Entity class name
    protected string $entity_class = \Simbiat\FFXIV\Linkshell::class;
    #Name to show in errors
    protected string $name_for_errors = 'Linkshell';
    #Name for links
    protected string $name_for_links = 'linkshell';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Linkshell',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
