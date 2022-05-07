<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Emails extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/activation', 'name' => 'Emails']
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

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Get user ID
        $userid = $_SESSION['userid'] ?? intval($path[0]) ?? null;
        #Get activation ID
        $activation = $path[1] ?? null;
        if (empty($userid)) {
            return ['http_error' => 403];
        }
        $outputArray = [];
        #Show list of mails pending activation to request code resending
        $outputArray['emails'] = HomePage::$dbController->selectPair('SELECT `email`, `activation` FROM `uc__user_to_email` WHERE `userid`=:userid /*AND `activation` IS NOT NULL*/;', [':userid' => [$userid, 'int']]);
        return $outputArray;
    }
}
