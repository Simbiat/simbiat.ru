<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class CrossworldLinkshell extends General
{
    #Entity class name
    protected string $entity_class = \Simbiat\FFXIV\CrossworldLinkshell::class;
    #Name to show in errors
    protected string $name_for_errors = 'Crossworld Linkshell';
    #Name for links
    protected string $name_for_links = 'crossworld_linkshell';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Crossworld Linkshell',
        'ID_regexp' => '/^[a-z0-9]{40}$/m',
    ];
}
