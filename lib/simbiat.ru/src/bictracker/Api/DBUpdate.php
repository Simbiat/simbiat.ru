<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Api;

use Simbiat\Abstracts\Api;
use Simbiat\bictracker\Library;
use Simbiat\HomePage;

class DBUpdate extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;

    protected function genData(array $path): array
    {
        try {
            $data = (new Library)->update(true);
        } catch (\Throwable) {
            return ['http_error' => 503, 'reason' => 'Unknown error during request processing'];
        }
        $result = ['response' => $data];
        return $result;
    }
}
