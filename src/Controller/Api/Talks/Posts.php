<?php

declare(strict_types=1);

namespace App\Controller\Api\Talks;

use App\Controller\Api\Api;
use App\Entity\Post;
use App\HomePage;

final class Posts extends Api
{
    // Flag to indicate, that this is the lowest level
    #[\Override]
    protected bool $final_node = true;

    // Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    #[\Override]
    protected array $methods = ['POST' => ['add'], 'DELETE' => 'delete', 'PATCH' => ['edit', 'like', 'dislike', 'move']];

    // Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    #[\Override]
    protected array $verbs = ['add' => 'Add post', 'delete' => 'Delete post', 'edit' => 'Edit post', 'like' => 'Like a post', 'dislike' => 'Dislike a post', 'move' => 'Move post to different thread'];

    // Flag indicating that authentication is required
    #[\Override]
    protected bool $authentication_needed = true;

    // Flag indicating, that lack of authentication can be bypassed by an access_token
    #[\Override]
    protected bool $access_token_possible = true;

    protected function genData(array $path): array
    {
        // Check for ID
        if (empty($path[0])) {
            // Limit accidental spam by extra checks
            if (HomePage::$method !== 'POST') {
                return ['http_error' => 405, 'reason' => 'Incorrect method or verb used'];
            }

            // Only support adding a new post here
            return new Post()->add();
        }

        if (!\is_numeric($path[0])) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` is not numeric'];
        }

        $post = new Post($path[0])->get();
        if ($post->id === null) {
            return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
        }

        // If post is being deleted - require CSRF. Deletion is final, so it's critical.
        // Adding and editing a post can take a lot of time, thus CSRF will easily expire. Since these operations do not change user data - not so critical.
        // Liking and disliking is even less critical.
        if (
            $path[1] === 'delete'
            || HomePage::$method === 'DELETE'
        ) {
            if (!$this->antiCSRF()) {
                return ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
            }
        }

        return match ($path[1]) {
            'like' => $post->like(),
            'dislike' => $post->like(dislike: true),
            'edit' => $post->edit(),
            'delete' => $post->delete(),
            'move' => $post->move(),
            default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
        };
    }
}
