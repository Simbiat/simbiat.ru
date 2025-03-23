<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\Talks;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\HomePage;
use Simbiat\Website\Talks\Section;

class Sections extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['close', 'open', 'markprivate', 'markpublic', 'order']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add section', 'delete' => 'Delete section', 'edit' => 'Edit section', 'close' => 'Close section', 'open' => 'Open section',
                                'markprivate' => 'Mark the section as private', 'markpublic' => 'Mark the section as public', 'order' => 'Change order of the section',
    ];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;
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
            #Limit accidental spam by extra checks
            if (HomePage::$method !== 'POST' && $path[1] === 'add') {
                return ['http_error' => 405, 'reason' => 'Incorrect method or verb used'];
            }
            #Only support adding a new post here
            return (new Section)->add();
        } else {
            if (!is_numeric($path[0])) {
                return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` is not numeric'];
            }
            $section = (new Section($path[0]))->get();
            if (is_null($section->id)) {
                return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
            }
            return match($path[1]) {
                'edit' => $section->edit(),
                'delete' => $section->delete(),
                'markprivate' => $section->setPrivate(true),
                'markpublic' => $section->setPrivate(),
                'close' => $section->setClosed(true),
                'open' => $section->setClosed(),
                'order' => $section->order(),
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
