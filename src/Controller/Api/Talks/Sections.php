<?php
declare(strict_types = 1);

namespace App\Controller\Api\Talks;

use App\Controller\Api\Api;
use App\Entity\Section;
use App\HomePage;

class Sections extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['move', 'close', 'open', 'mark_private', 'mark_public']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add section', 'delete' => 'Delete section', 'edit' => 'Edit section', 'move' => 'Move section to another subsection', 'close' => 'Close section', 'open' => 'Open section',
        'mark_private' => 'Mark the section as private', 'mark_public' => 'Mark the section as public',
    ];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = true;
    
    protected function genData(array $path): array
    {
        #Reset verb for consistency, if it's not set
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
            return new Section()->add();
        }
        if (!\is_numeric($path[0])) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` is not numeric'];
        }
        $section = new Section($path[0])->get();
        if ($section->id === null) {
            return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
        }
        return match($path[1]) {
            'edit' => $section->edit(),
            'delete' => $section->delete(),
            'mark_private' => $section->setPrivate(true),
            'mark_public' => $section->setPrivate(),
            'close' => $section->setClosed(true),
            'open' => $section->setClosed(),
            'move' => $section->move(),
            default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
        };
    }
}
