<?php
declare(strict_types=1);
namespace Simbiat\Talks\Pages;

use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;
use Simbiat\HTTP20\Headers;

class User extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/talks/users/', 'name' => 'Users']
    ];
    #Sub service name
    protected string $subServiceName = 'user';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'User profile';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'User profile';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'User profile';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        if (empty($path[0])) {
            Headers::redirect('https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Common::$http_host) === 1 ? 'www.' : '').Common::$http_host.($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/uc/profile/');
        }
        $user = new \Simbiat\usercontrol\User($path[0]);
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
        $this->h1 = $this->title = $outputArray['userData']['username'];
        $this->ogdesc = 'Public profile of '.$outputArray['userData']['username'];
        #Setup OG profile for characters
        $outputArray['ogtype'] = 'profile';
        $outputArray['ogextra'] =
            '<meta property="profile:username" content="'.htmlspecialchars($outputArray['userData']['username']).'" />'.
            (is_null($outputArray['userData']['name']['firstname']) ? '' : '<meta property="profile:first_name" content="'.htmlspecialchars($outputArray['userData']['name']['firstname']).'" />').
            (is_null($outputArray['userData']['name']['lastname']) ? '' : '<meta property="profile:last_name" content="'.htmlspecialchars($outputArray['userData']['name']['lastname']).'" />').
            (is_null($outputArray['userData']['sex']) ? '' : '<meta property="profile:gender" content="'.htmlspecialchars(($outputArray['userData']['sex'] === 1 ? 'male' : 'female')).'" />')
        ;
        return $outputArray;
    }
}
