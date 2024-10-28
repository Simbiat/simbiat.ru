<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol\Pages;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\HomePage;
use Simbiat\Website\usercontrol\User;

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
        #Allow `blob:`
        @header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' blob:; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        $outputArray = [];
        #Get avatars list
        $outputArray['avatars'] = (new User($_SESSION['userid']))->getAvatars();
        return $outputArray;
    }
}
