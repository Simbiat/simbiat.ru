<?php

declare(strict_types=1);

namespace App\Controller\Api\FFXIV;

final class Achievement extends General
{
    /**
     * @var array<string, string> Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
     */
    #[\Override]
    protected array $methods = ['GET' => '', 'PATCH' => 'update'];

    /**
     * @var string Entity class name
     */
    #[\Override]
    protected string $entity_class = \App\Entity\FFXIV\Achievement::class;

    /**
     * @var string Name to show in errors
     */
    #[\Override]
    protected string $name_for_errors = 'Achievement';

    /**
     * @var string Name for links
     */
    #[\Override]
    protected string $name_for_links = 'achievement';

    /**
     * @var array|string[] Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = ['update' => 'Attempt updating entity', 'lodestone' => 'Show data grabbed directly from Lodestone'];

    /**
     * @var array<string, string> Description of the node
     */
    #[\Override]
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Achievement',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
