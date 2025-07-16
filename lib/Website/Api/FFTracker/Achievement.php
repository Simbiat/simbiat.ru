<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class Achievement extends General
{
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => '', 'PUT' => 'update'];
    #Entity class name
    protected string $entity_class = \Simbiat\FFXIV\Achievement::class;
    #Name to show in errors
    protected string $name_for_errors = 'Achievement';
    #Name for links
    protected string $name_for_links = 'achievement';
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['update' => 'Attempt updating entity', 'lodestone' => 'Show data grabbed directly from Lodestone'];
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Achievement',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
