<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\FFTracker\Statistics;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

class General extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/fftracker/statistics', 'name' => 'Statistics']
    ];
    #Sub service name
    protected string $subservice_name = 'statistics';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Statistics';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Statistics';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $og_desc = 'Statistics';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = ['view_ff'];
    protected bool $static = true;
    #Name of JSON file to attempt to ingest
    protected string $json_to_ingest = '';
    
    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $output_array = [];
        #Attempt to ingest dats from JSON file
        try {
            if (empty($this->json_to_ingest)) {
                return ['http_error' => 500, 'reason' => 'No JSON file defined for category'];
            }
            $output_array['ffstats']['category'] = $this->json_to_ingest;
            if (is_file(Config::$statistics.$this->json_to_ingest.'.json')) {
                $output_array['ffstats']['data'] = json_decode(file_get_contents(Config::$statistics.$this->json_to_ingest.'.json'), flags: JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY);
                $this->lastModified($output_array['ffstats']['data']['time'] ?? 0);
            } else {
                return ['http_error' => 500, 'reason' => 'File `'.$this->json_to_ingest.'.json` not found'];
            }
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Failed to read `'.$this->json_to_ingest.'.json` file'];
        }
        #Placeholder
        return $output_array;
    }
}
