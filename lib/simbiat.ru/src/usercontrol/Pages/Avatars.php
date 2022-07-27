<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Avatars extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/avatars', 'name' => 'Avatars']
    ];
    #Sub service name
    protected string $subServiceName = 'avatars';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Avatars';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Your avatars';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to edit your avatars';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = 'uc/avatars';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        $outputArray['avatars'] = HomePage::$dbController->selectRow('SELECT `url`, `current` FROM `uc__user_to_avatar` WHERE `userid`=:userid;', [':userid' => [$_SESSION['userid'], 'int']]);
        return $outputArray;
    }
}
