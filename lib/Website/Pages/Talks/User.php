<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\Talks;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

class User extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/talks/users/', 'name' => 'Users']
    ];
    #Sub service name
    protected string $subServiceName = 'user';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'User profile';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'User profile';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $ogdesc = 'User profile';
    #Flag to indicate editor mode
    protected bool $editMode = false;
    
    #This is the actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        if (empty($path[0])) {
            Headers::redirect('https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config::$http_host) === 1 ? 'www.' : '').Config::$http_host.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/uc/profile/');
        }
        $user = new \Simbiat\Website\usercontrol\User($path[0]);
        $outputArray = [];
        $outputArray['userData'] = $user->getArray();
        if (empty($outputArray['userData']['id'])) {
            return ['http_error' => 404, 'reason' => 'User does not exist'];
        }
        #Get FF characters
        $outputArray['fftracker'] = $user->getFF();
        #Get last posts and threads
        $outputArray['threads'] = $user->getThreads();
        $outputArray['posts'] = $user->getPosts();
        #Update meta
        $this->title = $outputArray['userData']['username'];
        $this->h1 = $this->title;
        $this->ogdesc = 'Public profile of '.$outputArray['userData']['username'];
        #Setup OG profile for characters
        $outputArray['ogtype'] = 'profile';
        $outputArray['ogextra'] =
            '<meta property="profile:username" content="'.htmlspecialchars($outputArray['userData']['username'], ENT_QUOTES | ENT_SUBSTITUTE).'" />'.
            ($outputArray['userData']['name']['first_name'] === null ? '' : '<meta property="profile:first_name" content="'.htmlspecialchars($outputArray['userData']['name']['first_name'], ENT_QUOTES | ENT_SUBSTITUTE).'" />').
            ($outputArray['userData']['name']['last_name'] === null ? '' : '<meta property="profile:last_name" content="'.htmlspecialchars($outputArray['userData']['name']['last_name'], ENT_QUOTES | ENT_SUBSTITUTE).'" />').
            ($outputArray['userData']['sex'] === null ? '' : '<meta property="profile:gender" content="'.htmlspecialchars(($outputArray['userData']['sex'] === 1 ? 'male' : 'female'), ENT_QUOTES | ENT_SUBSTITUTE).'" />');
        #Set flag indicating that we are in edit mode
        $outputArray['editMode'] = $this->editMode;
        return $outputArray;
    }
}
