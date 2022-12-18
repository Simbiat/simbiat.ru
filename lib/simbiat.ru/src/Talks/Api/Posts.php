<?php
declare(strict_types=1);
namespace Simbiat\Talks\Api;

use Simbiat\Abstracts\Api;
use Simbiat\Talks\Entities\Post;

class Posts extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => 'add', 'DELETE' => 'delete', 'PATCH' => ['like', 'dislike', 'edit']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add post', 'delete' => 'Delete post', 'edit' => 'Edit post', 'like' => 'Like a post', 'dislike' => 'Dislike a post'];
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
            $path[1] = '';
        }
        #Check for ID
        if (empty($path[0])) {
            #Only support adding a new post here
            return (new Post)->add();
        } else {
            $post = (new Post($path[0]))->get();
            if (is_null($post->id)) {
                return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
            }
            #If post is being deleted - require CSRF. Deletion is final, so it's critical.
            #Adding and editing a post can take a lot of time, thus CSRF will easily expire. Since these operations do not change user data - not so critical.
            #Liking and disliking is even less critical.
            if ($path[1] === 'delete') {
                if (!$this->antiCSRF($this->allowedOrigins)) {
                    return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
                }
            }
            return match($path[1]) {
                'like' => $post->like(),
                'dislike' => $post->like(true),
                'edit' => ['response' => $post->edit()],
                'delete' => ['response' => $post->delete()],
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
