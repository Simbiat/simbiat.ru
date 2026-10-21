<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;

final class Sessions extends Api
{
    // Flag to indicate that this is the lowest level
    #[\Override]
    protected bool $final_node = true;

    // Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    #[\Override]
    protected array $methods = ['DELETE' => 'delete'];

    // Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    #[\Override]
    protected array $verbs = ['delete' => 'Delete a session'];

    // Flag indicating that authentication is required
    #[\Override]
    protected bool $authentication_needed = true;

    // Flag to indicate need to validate CSRF
    #[\Override]
    protected bool $csrf = true;

    protected function genData(array $path): array
    {
        return ['response' => new User($_SESSION['user_id'])->deleteSession()];
    }
}
