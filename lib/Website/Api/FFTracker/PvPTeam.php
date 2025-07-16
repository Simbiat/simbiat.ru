<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

class PvPTeam extends General
{
    #Entity class name
    protected string $entity_class = \Simbiat\FFXIV\PvPTeam::class;
    #Name to show in errors
    protected string $name_for_errors = 'PvP Team';
    #Name for links
    protected string $name_for_links = 'pvpteam';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV PvP Team',
        'ID_regexp' => '/^[a-z0-9]{40}$/m',
    ];
}
