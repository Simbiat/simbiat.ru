<?php

declare(strict_types=1);

namespace App\Controller\Api\FFXIV;

final class FreeCompany extends General
{
    /**
     * @var string Entity class name
     */
    #[\Override]
    protected string $entity_class = \App\Entity\FFXIV\FreeCompany::class;

    /**
     * @var string Name to show in errors
     */
    #[\Override]
    protected string $name_for_errors = 'Free Company';

    /**
     * @var string Name for links
     */
    #[\Override]
    protected string $name_for_links = 'freecompany';

    /**
     * @var array<string, string> Description of the node
     */
    #[\Override]
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV Free Company',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
