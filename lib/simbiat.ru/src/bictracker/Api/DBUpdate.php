<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Api;

use Simbiat\Abstracts\Api;
use Simbiat\bictracker\Library;
use Simbiat\HomePage;

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
            return ['http_error' => 503, 'reason' => 'Unknown error during request processing'];
        }
        if (is_string($data)) {
            return ['http_error' => 503, 'reason' => $data];
        }
        return ['response' => $data];
    }
}
