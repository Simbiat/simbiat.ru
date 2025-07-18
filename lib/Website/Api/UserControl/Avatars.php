<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\HomePage;
use Simbiat\Website\usercontrol\User;

class Avatars extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => 'add', 'DELETE' => 'delete', 'PATCH' => 'setactive'];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add avatar', 'delete' => 'Delete avatar', 'setactive' => 'Set active avatar'];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = true;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = true;
    
    protected function genData(array $path): array
    {
        #We do not really care for verbs here, only methods
        return match (HomePage::$method) {
            'POST' => new User($_SESSION['user_id'])->addAvatar(true),
            'DELETE' => new User($_SESSION['user_id'])->delAvatar(),
            'PATCH' => new User($_SESSION['user_id'])->setAvatar(),
        };
    }
}
