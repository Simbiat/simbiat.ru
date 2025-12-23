<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\Talks;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Entities\Post;
use Simbiat\Website\HomePage;

class Posts extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['like', 'dislike']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add post', 'delete' => 'Delete post', 'edit' => 'Edit post', 'like' => 'Like a post', 'dislike' => 'Dislike a post'];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag indicating, that lack of authentication can be bypassed by an access_token
    protected bool $access_token_possible = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    
    protected function genData(array $path): array
    {
        #Reset verb for consistency, if it's not set
        if (empty($path[1])) {
            $path[1] = '';
        }
        #Check for ID
        if (empty($path[0])) {
            #Limit accidental spam by extra checks
            if (HomePage::$method !== 'POST' && $path[1] === 'add') {
                return ['http_error' => 405, 'reason' => 'Incorrect method or verb used'];
            }
            #Only support adding a new post here
            return (new Post)->add();
        } else {
            if (!\is_numeric($path[0])) {
                return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` is not numeric'];
            }
            $post = (new Post($path[0]))->get();
            if (\is_null($post->id)) {
                return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
            }
            #If post is being deleted - require CSRF. Deletion is final, so it's critical.
            #Adding and editing a post can take a lot of time, thus CSRF will easily expire. Since these operations do not change user data - not so critical.
            #Liking and disliking is even less critical.
            if ($path[1] === 'delete') {
                if (!$this->antiCSRF($this->allowed_origins)) {
                    return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
                }
            }
            return match($path[1]) {
                'like' => $post->like(),
                'dislike' => $post->like(true),
                'edit' => $post->edit(),
                'delete' => $post->delete(),
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
