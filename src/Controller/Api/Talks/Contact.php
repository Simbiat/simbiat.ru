<?php
declare(strict_types = 1);

namespace App\Controller\Api\Talks;

use App\Controller\Api\Api;
use App\Entity\Thread;
use App\Security\Security;
use App\Service\Config;

class Contact extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Submit support request'];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = false;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    
    protected function genData(array $path): array
    {
        #Only creation of new threads
        $_POST['thread_data']['parent_id'] = Config::SUPPORT_SECTION;
        #contact_form_email
        #Generate ticket ID
        $ticket = Security::genToken(8);
        $_POST['thread_data']['name'] = '[Contact form] '.$ticket;
        $_SESSION['permissions'] = ['can_post'];
        return new Thread()->add();
    }
}
