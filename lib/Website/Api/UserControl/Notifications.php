<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\UserControl;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Entities\Notifications\Test;

/**
 * API endpoint to control notifications
 */
final class Notifications extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => 'read'];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [
        'read' => 'Mark a notification read',
    ];
    
    /**
     * This is an actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        if (empty($path[0])) {
            $path[0] = 'read';
        }
        if ($path[0] === 'read') {
            #Using Test notification, as most benign one
            new Test()::markRead($_GET['uuid'] ?? '', true);
            return [];
        }
        return ['http_error' => 400, 'reason' => 'Unsupported verb'];
    }
}
