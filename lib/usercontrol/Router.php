<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

class Router extends \Simbiat\Abstracts\Router
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
            'activate' => (new Pages\Activation)->get(array_slice($path, 1)),
            'emails' => (new Pages\Emails)->get(array_slice($path, 1)),
            'unsubscribe' => (new Pages\Unsubscribe)->get(array_slice($path, 1)),
            'password' => (new Pages\Password)->get(array_slice($path, 1)),
            'profile' => (new Pages\Profile)->get(array_slice($path, 1)),
            'removal' => (new Pages\Removal)->get(array_slice($path, 1)),
            'sessions' => (new Pages\Sessions)->get(array_slice($path, 1)),
            'fftracker' => (new Pages\FFTracker)->get(array_slice($path, 1)),
            'avatars' => (new Pages\Avatars)->get(array_slice($path, 1)),
            'register' => ['subServiceName' => 'registration'],
        };
    }
}
