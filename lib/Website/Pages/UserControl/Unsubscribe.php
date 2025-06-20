<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;

/**
 * Page to unsubscribe email addresses from messages
 */
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
    
    /**
     * Actual page generation based on further details of the $path
     * @param array $path URL path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        if (empty($_GET['token'])) {
            return ['http_error' => 400, 'reason' => 'No email token provided'];
        }
        $email = Security::decrypt($_GET['token']);
        if (preg_match(Security::EMAIL_REGEX, $email) !== 1) {
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
