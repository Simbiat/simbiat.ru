<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use Simbiat\Website\Abstracts\Api;

class Upload extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Flag to indicate need to validate CSRF
    protected bool $csrf = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    
    protected function genData(array $path): array
    {
        #Headers required for TinyMCE
        \header('Access-Control-Allow-Credentials: true');
        \header('P3P: CP="There is no P3P policy."');
        try {
            $upload = new Curl()->upload();
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            return ['response' => true, 'location' => $upload['location']];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to upload file'];
        }
    }
}
