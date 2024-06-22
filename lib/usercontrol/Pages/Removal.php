<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Removal extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/removal', 'name' => 'Removal']
    ];
    #Sub service name
    protected string $subServiceName = 'removal';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Removal';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Profile removal';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to remove your profile';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = 'uc/removal';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #This page is essentially static with 2 buttons.
        #It's not marked as static, because it will still require a DB to get session data, without which it would be useless
        return [];
    }
}
