<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Api;

use Simbiat\Abstracts\Api;
use Simbiat\AccountKeying;

class Keying extends Api
{
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => '', 'POST' => ''];
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Flag to indicate, that no database is required for this node
    protected bool $static = true;

    protected function genData(array $path): array
    {
        $bic = $_POST['bic'] ?? $path[0] ?? null;
        $acc = $_POST['account'] ?? $path[1] ?? null;
        #Validate values
        if (empty($bic)) {
            return ['http_error' => 400, 'reason' => 'No BIC provided'];
        }
        if (empty($acc)) {
            return ['http_error' => 400, 'reason' => 'No Account provided'];
        }
        $data = (new AccountKeying)->accCheck($bic, $acc);
        if ($data === false) {
            return ['http_error' => 400, 'reason' => 'Wrong format of either BIC or Account'];
        }
        $result = ['response' => $data];
        #Link header/tag for API
        $result['alt_links'] = [
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/bictracker/keying/' . $bic.'/'.$acc],
        ];
        return $result;
    }
}
