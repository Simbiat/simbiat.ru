<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\usercontrol\User;

/**
 * Handle user registration
 */
class Register extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = true;
    
    /**
     *
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        return new User()->register();
    }
}
