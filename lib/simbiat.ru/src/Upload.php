<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Abstracts\Api;

class Upload extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = true;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = false;

    protected function genData(array $path): array
    {
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])){
            #Headers required for TinyMCE
            header('Access-Control-Allow-Credentials: true');
            header('P3P: CP="There is no P3P policy."');
            #Sanitize input
            if (preg_match('/([^\w\s\d\-_~,;:\[\]\(\).])|([.]{2,})/', $temp['name'])) {
                return ['http_error' => 400, 'reason' => 'Invalid file name'];
            }
        
            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                header("HTTP/1.1 400 Invalid extension.");
                return ['http_error' => 400, 'reason' => 'Invalid extension'];
            }
        
            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);
        
            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
            echo json_encode(array('location' => $baseurl . $filetowrite));
        } else {
            return ['http_error' => 500];
        }
        return ['response' => true];
    }
}
