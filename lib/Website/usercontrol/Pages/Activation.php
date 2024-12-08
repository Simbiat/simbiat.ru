<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol\Pages;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;
use Simbiat\Website\usercontrol\Email;

/**
 * User activation
 */
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
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        #Get user ID
        $userid = $_SESSION['userid'] ?? (int)$path[0] ?? null;
        #Get activation ID
        $activation = $path[1] ?? null;
        if (empty($userid) || empty($activation)) {
            return ['http_error' => 403];
        }
        #Check if user exists
        if (!Config::$dbController->check('SELECT `userid` FROM `uc__users` WHERE `userid`=:userid', [':userid' => [$userid, 'int']])) {
            #While technically this is closer to 404, we do not want potential abuse to get user IDs, although attack vector here is unlikely
            return ['http_error' => 403];
        }
        $outputArray = [];
        #Check if user requires activation
        $outputArray['activation'] = Config::$dbController->check('SELECT `userid` FROM `uc__user_to_group` WHERE `userid`=:userid AND `groupid`=2', [':userid' => [$userid, 'int']]);
        #Get list of mails for the user with activation codes
        $emails = Config::$dbController->selectPair('SELECT `email`, `activation` FROM `uc__emails` WHERE `userid`=:userid AND `activation` IS NOT NULL;', [':userid' => [$userid, 'int']]);
        #Check if provided activation code fits any of those mails
        foreach ($emails as $email => $code) {
            if (password_verify($activation, $code) && (new Email($email))->activate($userid)) {
                $outputArray = ['activated' => true, 'email' => $email];
                break;
            }
        }
        return $outputArray;
    }
}
