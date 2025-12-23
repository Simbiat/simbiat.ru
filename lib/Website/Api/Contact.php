<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Config;
use Simbiat\Website\Entities\Thread;
use Simbiat\Website\Security;

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
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    
    protected function genData(array $path): array
    {
        #Only creation of new threads
        $_POST['new_thread']['parent_id'] = Config::$support_section;
        #contact_form_email
        #Generate ticket ID
        $ticket = Security::genToken(8);
        $_POST['new_thread']['name'] = '[Contact form] '.$ticket;
        $_SESSION['permissions'] = ['can_post'];
        return new Thread()->add();
    }
}
