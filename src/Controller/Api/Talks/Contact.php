<?php

declare(strict_types=1);

namespace App\Controller\Api\Talks;

use App\Controller\Api\Api;
use App\Entity\Thread;
use App\Security\Security;

final class Contact extends Api
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
    protected array $methods = ['POST' => ''];

    /**
     * @var array<string, string> Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
     */
    #[\Override]
    protected array $verbs = ['add' => 'Submit support request'];

    protected function genData(array $path): array
    {
        // Only creation of new threads. 26 is the static ID of the forum where the threads go
        $_POST['thread_data']['parent_id'] = 26;
        // contact_form_email
        // Generate ticket ID
        $ticket = Security::genToken(8);
        $_POST['thread_data']['name'] = '[Contact form] '.$ticket;
        $_SESSION['permissions'] = ['can_post'];

        return new Thread()->add();
    }
}
