<?php
declare(strict_types = 1);

namespace App\Controller\Api\BIC;

use App\Controller\Api\Api;
use App\HomePage;
use App\Service\BICLibrary;
use App\Service\Errors;

/**
 * API endpoint to update BIC library
 */
class DBUpdate extends Api
{
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['PUT' => ''];
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Description of the node
    protected array $description = [
        'description' => 'Node to force BIC database update',
    ];
    
    /**
     * This is actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    protected function genData(array $path): array
    {
        if (!\array_key_exists(HomePage::$method, $this->methods)) {
            return ['http_error' => 405];
        }
        try {
            $data = new BICLibrary()->update(true);
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        if (\is_string($data)) {
            return ['http_error' => 500, 'reason' => $data];
        }
        return ['response' => $data];
    }
}
