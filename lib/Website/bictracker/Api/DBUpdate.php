<?php
declare(strict_types=1);
namespace Simbiat\Website\bictracker\Api;

use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\bictracker\Library;
use Simbiat\Website\HomePage;

class DBUpdate extends Api
{
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['PUT' => ''];
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Description of the node
    protected array $description = [
        'description' => 'Node to force BIC database update',
    ];

    protected function genData(array $path): array
    {
        if (!in_array(HomePage::$method, array_keys($this->methods))) {
            return ['http_error' => 405];
        }
        try {
            $data = (new Library)->update(true);
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        if (is_string($data)) {
            return ['http_error' => 500, 'reason' => $data];
        }
        return ['response' => $data];
    }
}
