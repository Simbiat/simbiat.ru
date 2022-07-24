<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Profile extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/profile', 'name' => 'Profile']
    ];
    #Sub service name
    protected string $subServiceName = 'profile';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Profile';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Your profile';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to edit your profile';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        $outputArray['userData'] = HomePage::$dbController->selectRow('SELECT *, null as `password`, null as `pw_reset` FROM `uc__users` WHERE `userid`=:userid;', [':userid' => [$_SESSION['userid'], 'int']]);
        return $outputArray;
    }
}
