<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;

final class Logout extends Api
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
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = true;

    protected function genData(array $path): array
    {
        return ['response' => new User($_SESSION['user_id'])->logout()];
    }
}
