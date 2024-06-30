<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol;

use Simbiat\Website\usercontrol\Pages\Activation;
use Simbiat\Website\usercontrol\Pages\Avatars;
use Simbiat\Website\usercontrol\Pages\Emails;
use Simbiat\Website\usercontrol\Pages\FFTracker;
use Simbiat\Website\usercontrol\Pages\Password;
use Simbiat\Website\usercontrol\Pages\Profile;
use Simbiat\Website\usercontrol\Pages\Removal;
use Simbiat\Website\usercontrol\Pages\Sessions;
use Simbiat\Website\usercontrol\Pages\Unsubscribe;

class Router extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['activate', 'register', 'emails', 'unsubscribe', 'profile', 'password', 'removal', 'sessions', 'fftracker', 'avatars'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href'=>'/uc/', 'name'=>'User Cabinet']
    ];
    protected string $title = 'User Cabinet';
    protected string $h1 = 'User Cabinet';
    protected string $ogdesc = 'User Cabinet';
    protected string $serviceName = 'uc';
    protected string $redirectMain = '/uc/profile/';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'activate' => (new Activation)->get(array_slice($path, 1)),
            'emails' => (new Emails)->get(array_slice($path, 1)),
            'unsubscribe' => (new Unsubscribe)->get(array_slice($path, 1)),
            'password' => (new Password)->get(array_slice($path, 1)),
            'profile' => (new Profile)->get(array_slice($path, 1)),
            'removal' => (new Removal)->get(array_slice($path, 1)),
            'sessions' => (new Sessions)->get(array_slice($path, 1)),
            'fftracker' => (new FFTracker)->get(array_slice($path, 1)),
            'avatars' => (new Avatars)->get(array_slice($path, 1)),
            'register' => ['subServiceName' => 'registration'],
        };
    }
}
