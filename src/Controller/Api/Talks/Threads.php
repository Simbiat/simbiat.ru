<?php
declare(strict_types = 1);

namespace App\Controller\Api\Talks;

use App\Controller\Api\Api;
use App\Entity\Thread;
use App\HomePage;

class Threads extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['move', 'close', 'open', 'mark_private', 'mark_public', 'pin', 'unpin']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add thread', 'delete' => 'Delete thread', 'edit' => 'Edit thread', 'move' => 'Move thread', 'close' => 'Close thread', 'open' => 'Open thread',
        'mark_private' => 'Mark the thread as private', 'mark_public' => 'Mark the thread as public', 'pin' => 'Pin the thread', 'unpin' => 'Unpin the thread',
    ];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    
    protected function genData(array $path): array
    {
        #Check for ID
        if (empty($path[0])) {
            #Limit accidental spam by extra checks
            if (HomePage::$method !== 'POST') {
                return ['http_error' => 405, 'reason' => 'Incorrect method or verb used'];
            }
            #Only support adding a new post here
            return new Thread()->add();
        }
        if (!\is_numeric($path[0])) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` is not numeric'];
        }
        #If we are not adding a thread (which can take some time with writing up a post) - check CSRF token
        if (!$this->antiCSRF($this->allowed_origins)) {
            return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
        }
        $thread = new Thread($path[0])->get();
        if ($thread->id === null) {
            return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
        }
        return match ($path[1]) {
            'edit' => $thread->edit(),
            'delete' => $thread->delete(),
            'mark_private' => $thread->setPrivate(true),
            'mark_public' => $thread->setPrivate(),
            'close' => $thread->setClosed(true),
            'open' => $thread->setClosed(),
            'pin' => $thread->setPinned(true),
            'unpin' => $thread->setPinned(),
            'move' => $thread->move(),
            default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
        };
    }
}
