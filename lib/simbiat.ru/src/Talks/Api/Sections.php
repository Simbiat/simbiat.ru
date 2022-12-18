<?php
declare(strict_types=1);
namespace Simbiat\Talks\Api;

use Simbiat\Abstracts\Api;
use Simbiat\Talks\Entities\Section;

class Sections extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ['add', 'edit'], 'DELETE' => 'delete', 'PATCH' => ['close', 'open', 'markprivate', 'markpublic', 'order']];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['add' => 'Add section', 'delete' => 'Delete post', 'edit' => 'Edit section', 'close' => 'Close section', 'open' => 'Open section',
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
            $path[1] = '';
        }
        #Check for ID
        if (empty($path[0])) {
            #Only support adding a new post here
            return (new Section)->add();
        } else {
            $section = (new Section($path[0]))->get();
            if (is_null($section->id)) {
                return ['http_error' => 404, 'reason' => 'ID `'.$path[0].'` not found'];
            }
            #Check permissions
            if (
                in_array($path[1], ['edit', 'close', 'open', 'markprivate', 'markpublic', 'order']) && !in_array('editSections', $_SESSION['permissions']) ||
                $path[1] === 'add' && !in_array('addSections', $_SESSION['permissions']) ||
                $path[1] === 'delete' && !in_array('removeSections', $_SESSION['permissions'])
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
                'order' => $section->order(),
                default => ['http_error' => 405, 'reason' => 'Unsupported API verb used'],
            };
        }
    }
}
