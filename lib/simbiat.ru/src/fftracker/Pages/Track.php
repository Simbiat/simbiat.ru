<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Pages;

use Simbiat\Abstracts\Pages\StaticPage;

class Track extends StaticPage
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
    #Link to JS module for preload
    protected string $jsModule = 'fftracker/track';
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['viewFF'];

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        return [];
    }
}
