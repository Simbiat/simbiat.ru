<?php

declare(strict_types=1);

namespace App\Controller\Api\FFXIV;

final class Character extends General
{
    // Entity class name
    #[\Override]
    protected string $entity_class = \App\Entity\FFXIV\Character::class;

    // Name to show in errors
    #[\Override]
    protected string $name_for_errors = 'Character';

    // Name for links
    #[\Override]
    protected string $name_for_links = 'character';

    // Description of the node
    #[\Override]
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV character',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
