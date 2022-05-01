<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\HomePage;

class Activation extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/activation', 'name' => 'Activation']
    ];
    #Sub service name
    protected string $subServiceName = 'activation';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'User/email activation';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'User/email activation';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page used for user or email activation';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        #Get user ID
        $userid = $_SESSION['userid'] ?? intval($path[0]) ?? null;
        #Get activation ID
        $activation = $path[1] ?? null;
        if (empty($userid) || (empty($_SESSION['userid']) && empty($activation))) {
            return ['http_error' => 403];
        }
        #Check if user exists
        if (!HomePage::$dbController->check('SELECT `userid` FROM `uc__users` WHERE `userid`=:userid', [':userid' => [$userid, 'int']])) {
            #While technically this is closer to 404, we do not want potential abuse to get user IDs, although attack vector here is unlikely
            return ['http_error' => 403];
        }
        $outputArray = [];
        #Processing depends on whether activation code is present
        if (empty($activation)) {
            #Show list of mails pending activation to request code resending
            $outputArray['emails'] = HomePage::$dbController->selectPair('SELECT `email` FROM `uc__user_to_email` WHERE `userid`=:userid AND `activation` IS NOT NULL;', [':userid' => [$userid, 'int']]);
        } else {
            #Check if user requires activation
            $outputArray['activation'] = HomePage::$dbController->check('SELECT `userid` FROM `uc__user_to_group` WHERE `userid`=:userid AND `groupid`=2', [':userid' => [$userid, 'int']]);
            #Get list of mails for the user with activation codes
            $emails = HomePage::$dbController->selectPair('SELECT `email`, `activation` FROM `uc__user_to_email` WHERE `userid`=:userid AND `activation` IS NOT NULL;', [':userid' => [$userid, 'int']]);
            #Check if provided activation code fits any of those mails
            foreach ($emails as $email=>$code) {
                if (password_verify($activation, $code) && $this->activate($userid, $email)) {
                    $outputArray = ['activated' => true, 'email' => $email];
                    break;
                }
            }
        }
        return $outputArray;
    }

    private function activate(int $userid, string $email): bool
    {
        $queries = [
            #Remove the code from DB
            ['UPDATE `uc__user_to_email` SET `activation`=NULL WHERE `userid`=:userid AND `email`=:email', [':userid' => [$userid, 'int'], ':email' => $email]],
            #Add user to register users
            ['INSERT IGNORE INTO `uc__user_to_group`(`userid`, `groupid`) VALUES (:userid, 3)', [':userid' => [$userid, 'int']]],
            #Remove user from unverified users
            ['DELETE FROM `uc__user_to_group` WHERE `userid`=:userid AND `groupid`=2', [':userid' => [$userid, 'int']]],
        ];
        return HomePage::$dbController->query($queries);
    }
}
