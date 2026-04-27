<?php
declare(strict_types = 1);

namespace App\Controller\Api\UserControl;

use App\Controller\Api\Api;
use App\Entity\User;

/**
 * Password reminder
 */
class Remind extends Api
{
    #Flag to indicate that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Flag to indicate need to validate CSRF
    protected bool $csrf = true;
    
    /**
     * This is the actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        return new User()->remind();
    }
}
