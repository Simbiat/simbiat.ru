<?php
declare(strict_types=1);
namespace Simbiat\Talks\Api;

use Simbiat\Abstracts\Api;
use Simbiat\Talks\Entities\Thread;

class Threads extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['close', 'open', 'markprivate', 'markpublic', 'pin', 'unpin']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add thread', 'delete' => 'Delete thread', 'edit' => 'Edit thread', 'close' => 'Close thread', 'open' => 'Open thread',
                                'markprivate' => 'Mark the thread as private', 'markpublic' => 'Mark the thread as public', 'pin' => 'Pin the thread', 'unpin' => 'Unpin the thread',
    ];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = false;
    
    protected function genData(array $path): array
    {
        #Reset verb for consistency, if it's not set
        if (empty($path[1])) {
            $path[1] = 'add';
        }
        #Check for ID
        if (empty($path[0])) {
            #Only support adding a new post here
            if (!in_array('canPost', $_SESSION['permissions'])) {
                return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
            }
            return (new Thread)->add();
        } else {
            #If we are not adding a thread (which can take some time with writing up a post) - check CSRF token
            if ($path[1] !== 'add') {
                if (!$this->antiCSRF($this->allowedOrigins)) {
                    return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
                }
            }
            $section = (new Thread($path[0]))->get();
            if (is_null($section->id)) {
                return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
            }
            #Check permissions
            if (
                (in_array($path[1], ['markprivate', 'markpublic']) && !in_array('markPrivate', $_SESSION['permissions'])) ||
                (in_array($path[1], ['pin', 'unpin']) && !in_array('canPin', $_SESSION['permissions'])) ||
                ($path[1] === 'add' && !in_array('canPost', $_SESSION['permissions'])) ||
                ($path[1] === 'delete' && !in_array('removeThreads', $_SESSION['permissions'])) ||
                ($_SESSION['userid'] === $section->createdBy &&
                    (
                        (in_array($path[1], ['close', 'open']) && !in_array('closeOwnThreads', $_SESSION['permissions'])) ||
                        ($path[1] === 'edit' && !in_array('editOwnThreads', $_SESSION['permissions']))
                    )
                ) ||
                ($_SESSION['userid'] !== $section->createdBy &&
                    (
                        (in_array($path[1], ['close', 'open']) && !in_array('closeOthersThreads', $_SESSION['permissions'])) ||
                        ($path[1] === 'edit' && !in_array('editOthersThreads', $_SESSION['permissions']))
                    )
                )
            ) {
                return ['http_error' => 403, 'reason' => 'Lacking permission for `'.$path[1].'` action'];
            }
            return match($path[1]) {
                'add' => $section->add(),
                'edit' => $section->edit(),
                'delete' => $section->delete(),
                'markprivate' => ['response' => $section->setPrivate(true)],
                'markpublic' => ['response' => $section->setPrivate()],
                'close' => ['response' => $section->setClosed(true)],
                'open' => ['response' => $section->setClosed()],
                'pin' => ['response' => $section->setPinned(true)],
                'unpin' => ['response' => $section->setPinned()],
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
