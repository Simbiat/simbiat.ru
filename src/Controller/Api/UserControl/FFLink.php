<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\FFXIV\Character;

final class FFLink extends Api
{
    /**
     * @var bool Flag to indicate, that this is the lowest level
     */
    #[\Override]
    protected bool $final_node = true;

    /**
     * @var array<string, string> Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
     */
    #[\Override]
    protected array $methods = ['POST' => ''];

    /**
     * @var array<string, string> Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = [];

    /**
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = true;

    /**
     * @var bool Flag to indicate need to validate CSRF
     */
    #[\Override]
    protected bool $csrf = true;

    protected function genData(array $path): array
    {
        return new Character($_POST['character_id'] ?? '')->linkUser();
    }
}
