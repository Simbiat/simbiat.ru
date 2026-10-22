<?php

declare(strict_types=1);

namespace App\Controller\Api\FFXIV;

final class PvPTeam extends General
{
    /**
     * @var string Entity class name
     */
    #[\Override]
    protected string $entity_class = \App\Entity\FFXIV\PvPTeam::class;

    /**
     * @var string Name to show in errors
     */
    #[\Override]
    protected string $name_for_errors = 'PvP Team';

    /**
     * @var string Name for links
     */
    #[\Override]
    protected string $name_for_links = 'pvpteam';

    /**
     * @var array<string, string> Description of the node
     */
    #[\Override]
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV PvP Team',
        'ID_regexp' => '/^[a-z0-9]{40}$/m',
    ];
}
