<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\usercontrol\User;

class Logout extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = true;
    
    protected function genData(array $path): array
    {
        
        return ['response' => new User($_SESSION['user_id'])->logout()];
    }
}
