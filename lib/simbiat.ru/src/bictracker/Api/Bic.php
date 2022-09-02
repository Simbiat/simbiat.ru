<?php
declare(strict_types=1);
namespace Simbiat\bictracker\Api;

use Simbiat\Abstracts\Api;
use Simbiat\HTTP20\Headers;

class Bic extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of a Russian organization based on Bank Identification Code',
        'ID_regexp' => '/^\d+$/mi',
    ];

    protected function genData(array $path): array
    {
        try {
            $data = (new \Simbiat\bictracker\Bic($path[0]))->getArray();
        } catch (\UnexpectedValueException) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        #Check if 404
        if (empty($data['id'])) {
            return ['http_error' => 404, 'reason' => 'BIC with ID `'.$path[0].'` is not found on Tracker'];
        }
        if (!empty($data['updated'])) {
            Headers::lastModified($data['dates']['updated'], true);
        }
        $result = ['response' => $data];
        #Link header/tag for API
        $result['alt_links'] = [
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/bictracker/bics/' . $path[0]],
        ];
        return $result;
    }
}
