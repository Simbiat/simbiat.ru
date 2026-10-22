<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Notification\Test;

/**
 * API endpoint to control notifications.
 */
final class Notifications extends Api
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
    protected array $methods = ['GET' => 'read'];

    /**
     * @var array<string, string> Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = [
        'read' => 'Mark a notification read',
    ];

    /**
     * This is an actual API response generation based on further details of the $path.
     *
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        if (empty($path[0])) {
            $path[0] = 'read';
        }

        if ($path[0] === 'read') {
            // Using Test notification, as most benign one
            new Test()::markRead($_GET['uuid'] ?? '', echo: true);

            return [];
        }

        return ['http_error' => 400, 'reason' => 'Unsupported verb'];
    }
}
