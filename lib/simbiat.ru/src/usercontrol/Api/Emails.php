<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Api;

use Simbiat\Abstracts\Api;
use Simbiat\HomePage;
use Simbiat\usercontrol\Common;

class Emails extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => '', 'PUT' => 'add', 'DELETE' => 'delete', 'PATCH' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add email',
                                'delete' => 'Delete mail',
                                'activate' => 'Request activation email for an email address',
                                'subscribe' => 'Subscribe to email notifications',
                                'unsubscribe' => 'Unsubscribe from email notifications',
    ];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;

    use Common;

    protected function genData(array $path): array
    {
        if (HomePage::$method === 'DELETE') {
            $path[0] = 'delete';
        } elseif (HomePage::$method === 'PUT') {
            $path[0] = 'add';
        } elseif (HomePage::$method === 'PATCH') {
            if (empty($_POST['verb'])) {
                return ['http_error' => 400, 'reason' => 'No verb provided'];
            } else {
                if (!in_array($_POST['verb'], ['activate', 'subscribe', 'unsubscribe'])) {
                    return ['http_error' => 400, 'reason' => 'Unsupported verb'];
                }
                $path[0] = $_POST['verb'];
            }
        }
        if (empty($path[0])) {
            $path[0] = 'add';
        }
        if (empty($_POST['email'])) {
            return ['http_error' => 400, 'reason' => 'No email provided'];
        }
        switch ($path[0]) {
            case 'activate':
                (new \Simbiat\usercontrol\Emails)->activationMail($_POST['email']);
                return ['response' => true];
            case 'add':
                return (new \Simbiat\usercontrol\Emails)->add($_POST['email']);
            case 'delete':
                return ['response' => (new \Simbiat\usercontrol\Emails)->delete($_POST['email'])];
            case 'subscribe':
                return ['response' => (new \Simbiat\usercontrol\Emails)->subscribe($_POST['email'])];
            case 'unsubscribe':
                return ['response' => (new \Simbiat\usercontrol\Emails)->unsubscribe($_POST['email'])];
            default:
                return ['http_error' => 400, 'reason' => 'Unsupported verb'];
        }
    }
}
