<?php

declare(strict_types=1);

namespace App\Controller\Api\BIC;

use App\Controller\Api\Api;
use Simbiat\BIC\AccountKeying;

class Keying extends Api
{
    // Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];

    // Flag to indicate, that this is the lowest level
    protected bool $final_node = true;

    // Flag to indicate, that no database is required for this node
    protected bool $static = true;

    // Description of the node
    protected array $description = [
        'ACC_regexp' => '/^\d{5}[\dАВСЕНКМРТХавсенкмртх]\d{14}$/',
        'BIC_regexp' => '/^\d{9}$/',
        'description' => 'Node for checking Russian account keying against a Russian Bank Identification Code',
        'POST' => [
            'account' => 'ACC_regexp',
            'bic' => 'BIC_regexp',
        ],
    ];

    protected function genData(array $path): array
    {
        $bic = $_POST['bic_key'] ?? null;
        $acc = $_POST['account_key'] ?? null;
        // Validate values
        if (empty($bic)) {
            return ['http_error' => 400, 'reason' => 'No BIC provided'];
        }
        if (empty($acc)) {
            return ['http_error' => 400, 'reason' => 'No Account provided'];
        }
        $data = AccountKeying::accCheck($bic, $acc);
        if ($data === false) {
            return ['http_error' => 400, 'reason' => 'Wrong format of either BIC or Account'];
        }
        $result = ['response' => $data];
        // Link header/tag for API
        $result['alt_links'] = [
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/bictracker/keying/'.$bic.'/'.$acc],
        ];

        return $result;
    }
}
