<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\BICTracker;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Errors;

class Bic extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of a Russian organization based on Bank Identification Code',
        'ID_regexp' => '/^\d+$/mi',
    ];
    
    protected function genData(array $path): array
    {
        try {
            $data = new \Simbiat\BIC\BIC($path[0])->getArray();
        } catch (\UnexpectedValueException) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
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
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/bictracker/bics/'.$path[0]],
        ];
        return $result;
    }
}
