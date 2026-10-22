<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;

/**
 * Password reminder.
 */
final class Remind extends Api
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
     * @var bool Flag to indicate need to validate CSRF
     */
    #[\Override]
    protected bool $csrf = true;

    /**
     * This is the actual API response generation based on further details of the $path.
     *
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        return new User()->remind();
    }
}
