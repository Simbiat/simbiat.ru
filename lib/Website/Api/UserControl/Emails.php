<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Entities\Email;

class Emails extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => 'add', 'DELETE' => 'delete', 'PATCH' => ['activate', 'subscribe']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [
        'add' => 'Add email',
        'delete' => 'Delete mail',
        'activate' => 'Request activation email for an email address',
        'subscribe' => 'Subscribe to email notifications',
    ];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = true;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = true;
    
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
                return new Email($_POST['email'])->add(true);
            case 'delete':
                return ['response' => new Email($_POST['email'])->delete()];
            case 'subscribe':
                return ['response' => new Email($_POST['email'])->subscribe()];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported verb'];
        }
    }
}
