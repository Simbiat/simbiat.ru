<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\array2table;
use Simbiat\Security;
use Simbiat\usercontrol\Email;

class Unsubscribe extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/unsubscribe', 'name' => 'Unsubscribe']
    ];
    #Sub service name
    protected string $subServiceName = 'unsubscribe';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Unsubscribing';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Unsubscribing';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page used unsubscribing an email from notifications';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        if (empty($_GET['token'])) {
            return ['http_error' => 400, 'reason' => 'No email token provided'];
        }
        $email = Security::decrypt($_GET['token']);
        if (preg_match(array2table::$eMailRegex, $email) !== 1) {
            return ['http_error' => 400, 'reason' => 'Token provided does not represent a valid email'];
        }
        $outputArray = [];
        $outputArray['email'] = $email;
        if (!(new Email($email))->unsubscribe()) {
            $outputArray['http_error'] = 500;
            $outputArray['reason'] = 'Failed to unsubscribe '.$email.'. You can try again, but if issue persists, contact us for assistance.';
        }
        return $outputArray;
    }
}
