<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;

class Statistics extends Page
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

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = HomePage::$headers;
        #Sanitize ID
        $id = $path[0] ?? '';


        #Placeholder
        return ['http_error' => 503, 'construction' => true, 'error_page' => true];
    }
}
