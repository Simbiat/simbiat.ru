<?php

declare(strict_types=1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\Email;

final class Emails extends Api
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
    protected array $methods = ['POST' => 'add', 'DELETE' => 'delete', 'PATCH' => ['activate', 'subscribe']];

    /**
     * @var array<string, string> Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = [
        'activate' => 'Request activation email for an email address',
        'add' => 'Add email',
        'delete' => 'Delete mail',
        'subscribe' => 'Subscribe to email notifications',
    ];

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
        if (empty($path[0])) {
            $path[0] = 'add';
        }

        if (empty($_POST['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }

        switch ($path[0]) {
            case 'activate':
                new Email($_POST['email'])->confirm();

                return ['response' => true];
            case 'add':
                return new Email($_POST['email'])->add(confirm: true);
            case 'delete':
                return ['response' => new Email($_POST['email'])->delete()];
            case 'subscribe':
                return ['response' => new Email($_POST['email'])->subscribe()];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported verb'];
        }
    }
}
