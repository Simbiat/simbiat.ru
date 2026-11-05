<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;

final class Logout extends Api
{
    /**
     * @var bool Flag indicating that authentication is required
     */
    #[\Override]
    protected bool $authentication_needed = true;

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
     * This is an actual API response generation based on further details of the $path.
     *
     * @param array $path
     *
     * @psalm-suppress MixedReturnTypeCoercion TODO: needs to be removed during migration to Symfony. Need to use objects instead of ararys.
     *
     * @return array<string, mixed>
     *
     * @throws \RuntimeException|\UnexpectedValueException
     */
    #[\Override]
    protected function genData(array $path): array
    {
        return ['response' => new User($_SESSION['user_id'])->logout()];
    }
}
