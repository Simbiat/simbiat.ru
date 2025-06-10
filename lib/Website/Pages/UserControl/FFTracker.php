<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\usercontrol\User;

class FFTracker extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/fftracker', 'name' => 'FFXIV']
    ];
    #Sub service name
    protected string $subServiceName = 'fftracker';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'FFXIV Linkage';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'FFXIV Linkage';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to link FFXIV characters';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = ['link_ff'];
    #Link to JS module for preload
    protected string $jsModule = 'uc/fftracker';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        return new User($_SESSION['user_id'])->getFF();
    }
}
