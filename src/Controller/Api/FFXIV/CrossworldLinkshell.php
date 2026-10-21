<?php

declare(strict_types=1);

namespace App\Controller\Api\FFXIV;

final class CrossworldLinkshell extends General
{
    // Entity class name
    #[\Override]
    protected string $entity_class = \App\Entity\FFXIV\CrossworldLinkshell::class;

    // Name to show in errors
    #[\Override]
    protected string $name_for_errors = 'Crossworld Linkshell';

    // Name for links
    #[\Override]
    protected string $name_for_links = 'crossworld_linkshell';

    // Description of the node
    #[\Override]
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Crossworld Linkshell',
        'ID_regexp' => '/^[a-z0-9]{40}$/m',
    ];
}
