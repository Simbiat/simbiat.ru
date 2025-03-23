<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing;

use Simbiat\Website\Pages\UserControl\Activation;
use Simbiat\Website\Pages\UserControl\Avatars;
use Simbiat\Website\Pages\UserControl\Emails;
use Simbiat\Website\Pages\UserControl\FFTracker;
use Simbiat\Website\Pages\UserControl\Password;
use Simbiat\Website\Pages\UserControl\Profile;
use Simbiat\Website\Pages\UserControl\Removal;
use Simbiat\Website\Pages\UserControl\Sessions;
use Simbiat\Website\Pages\UserControl\Unsubscribe;

class UserControl extends \Simbiat\Website\Abstracts\Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $subRoutes = ['activate', 'register', 'emails', 'unsubscribe', 'profile', 'password', 'removal', 'sessions', 'fftracker', 'avatars'];
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/', 'name' => 'User Cabinet']
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
