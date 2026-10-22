<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;
use App\HomePage;

final class Avatars extends Api
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
    protected array $methods = ['POST' => 'add', 'DELETE' => 'delete', 'PATCH' => 'setactive'];

    /**
     * @var array<string, string> Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = ['add' => 'Add avatar', 'delete' => 'Delete avatar', 'setactive' => 'Set active avatar'];

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
        // We do not really care for verbs here, only methods
        return match (HomePage::$method) {
            'POST' => new User($_SESSION['user_id'])->addAvatar(set_active: true),
            'DELETE' => new User($_SESSION['user_id'])->delAvatar(),
            'PATCH' => new User($_SESSION['user_id'])->setAvatar(),
        };
    }
}
