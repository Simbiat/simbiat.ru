<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Pages\Statistics;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

class General extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'Statistics']
    ];
    #Sub service name
    protected string $subServiceName = 'statistics';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Statistics';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Statistics';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Statistics';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewFF'];
    protected bool $static = true;
    #Name of JSON file to attempt to ingest
    protected string $jsonToIngest = '';
    
    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Attempt to ingest dats from JSON file
        try {
            if (empty($this->jsonToIngest)) {
                return ['http_error' => 500, 'reason' => 'No JSON file defined for category'];
            }
            $outputArray['ffstats']['category'] = $this->jsonToIngest;
            if (is_file(Config::$statistics.$this->jsonToIngest.'.json')) {
                $outputArray['ffstats']['data'] = json_decode(file_get_contents(Config::$statistics.$this->jsonToIngest.'.json'), flags: JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY);
                $this->lastModified($outputArray['ffstats']['data']['time'] ?? 0);
            } else {
                return ['http_error' => 500, 'reason' => 'File `'.$this->jsonToIngest.'.json` not found'];
            }
        } catch (\Throwable $exception) {
            return ['http_error' => 500, 'reason' => 'Failed to read `'.$this->jsonToIngest.'.json` file'];
        }
        #Placeholder
        return $outputArray;
    }
}
