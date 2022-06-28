<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Password extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/password', 'name' => 'Password']
    ];
    #Sub service name
    protected string $subServiceName = 'password';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Password';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Password change';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to change password';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        return $outputArray;
    }
}
