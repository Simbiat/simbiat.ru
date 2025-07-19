<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\Talks;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\HomePage;
use Simbiat\Website\Talks\Thread;

class Threads extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['close', 'open', 'markprivate', 'markpublic', 'pin', 'unpin']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add thread', 'delete' => 'Delete thread', 'edit' => 'Edit thread', 'close' => 'Close thread', 'open' => 'Open thread',
        'markprivate' => 'Mark the thread as private', 'markpublic' => 'Mark the thread as public', 'pin' => 'Pin the thread', 'unpin' => 'Unpin the thread',
    ];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    
    protected function genData(array $path): array
    {
        #Reset verb for consistency if it's not set
        if (empty($path[1])) {
            $path[1] = 'add';
        }
        #Check for ID
        if (empty($path[0])) {
            #Limit accidental spam by extra checks
            if (HomePage::$method !== 'POST' && $path[1] === 'add') {
                return ['http_error' => 405, 'reason' => 'Incorrect method or verb used'];
            }
            #Only support adding a new post here
            return new Thread()->add();
        } else {
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
                'markprivate' => $thread->setPrivate(true),
                'markpublic' => $thread->setPrivate(),
                'close' => $thread->setClosed(true),
                'open' => $thread->setClosed(),
                'pin' => $thread->setPinned(true),
                'unpin' => $thread->setPinned(),
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
