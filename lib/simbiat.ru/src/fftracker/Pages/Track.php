<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;

class Track extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/fftracker/track', 'name' => 'Track entity']
    ];
    #Sub service name
    protected string $subServiceName = 'track';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Track entity';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Track entity';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Register entity for tracking on FFXIV Tracker';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $headers = HomePage::$headers;
        #Sanitize ID
        $id = rawurldecode($path[0] ?? '');
        return [];
    }
}
