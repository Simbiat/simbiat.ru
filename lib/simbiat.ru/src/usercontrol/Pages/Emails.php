<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Emails extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/emails', 'name' => 'Emails']
    ];
    #Sub service name
    protected string $subServiceName = 'emails';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Emails';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'List of linked emails';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'List of linked emails';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = '/js/Pages/usercontrol/emails.js';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Show list of mails pending activation to request code resending
        $outputArray['emails'] = HomePage::$dbController->selectAll('SELECT `email`, `subscribed`, `activation` FROM `uc__user_to_email` WHERE `userid`=:userid ORDER BY `email`;', [':userid' => [$_SESSION['userid'], 'int']]);
        $outputArray['countActivated'] = count(array_filter(array_column($outputArray['emails'], 'activation'), function($x) { return empty($x); }));
        return $outputArray;
    }
}
