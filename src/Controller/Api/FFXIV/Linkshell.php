<?php
declare(strict_types = 1);

namespace App\Controller\Api\FFXIV;

class Linkshell extends General
{
    #Entity class name
    protected string $entity_class = \App\Entity\FFXIV\Linkshell::class;
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
