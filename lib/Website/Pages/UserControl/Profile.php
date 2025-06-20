<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\usercontrol\User;

class Profile extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/profile', 'name' => 'Profile']
    ];
    #Sub service name
    protected string $subServiceName = 'profile';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Profile';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Your profile';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to edit your profile';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = 'uc/profile';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        $outputArray['userData'] = new User($_SESSION['user_id'])->getArray();
        $now = new \DateTime();
        $outputArray['timezones'] = [];
        foreach (timezone_identifiers_list() as $timezone) {
            $now->setTimezone(new \DateTimeZone($timezone));
            $outputArray['timezones'][$timezone] = ['offset' => \sprintf('%+03d:%02d', (int)($now->getOffset() / 3600), abs((int)($now->getOffset() % 3600 / 60))), 'current' => ($timezone === $outputArray['userData']['timezone'])];
        }
        return $outputArray;
    }
}
