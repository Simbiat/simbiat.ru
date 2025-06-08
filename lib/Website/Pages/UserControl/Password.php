<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use Simbiat\Website\Abstracts\Page;

class Password extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/password', 'name' => 'Password']
    ];
    #Sub service name
    protected string $subServiceName = 'password';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Password';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Password change';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to change password';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #Link to JS module for preload
    protected string $jsModule = 'uc/password';
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        if ($_SESSION['user_id'] === 1) {
            #Check if password reset is being attempted
            if (!empty($path[0]) && preg_match('/\d+/u', $path[0]) === 1) {
                #Check token
                if (empty($path[1])) {
                    return ['http_error' => 403];
                }
                $outputArray = ['user_id' => $path[0], 'token' => $path[1]];
            } else {
                #Not authorized
                return ['http_error' => 403];
            }
        }
        return $outputArray;
    }
}
