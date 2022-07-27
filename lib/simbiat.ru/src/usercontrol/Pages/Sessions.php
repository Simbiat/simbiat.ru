<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Sessions extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/sessions', 'name' => 'Sessions']
    ];
    #Sub service name
    protected string $subServiceName = 'sessions';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Sessions';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Active sessions';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to manage active sessions';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = 'uc/sessions';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        return $outputArray;
    }
}
