<?php
declare(strict_types = 1);

namespace App\Service\Routing;

use App\Controller\UserControl\Activation;
use App\Controller\UserControl\Avatars;
use App\Controller\UserControl\Emails;
use App\Controller\UserControl\FFTracker;
use App\Controller\UserControl\Password;
use App\Controller\UserControl\Profile;
use App\Controller\UserControl\Removal;
use App\Controller\UserControl\Sessions;
use App\Controller\UserControl\Unsubscribe;

class UserControl extends Router
{
    #List supported "paths". Basic ones only, some extra validation may be required further
    protected array $sub_routes = ['activate', 'register', 'emails', 'unsubscribe', 'profile', 'password', 'removal', 'sessions', 'fftracker', 'avatars'];
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/', 'name' => 'User Cabinet']
    ];
    protected string $title = 'User Cabinet';
    protected string $h1 = 'User Cabinet';
    protected string $og_desc = 'User Cabinet';
    protected string $service_name = 'uc';
    protected string $redirect_main = '/uc/profile/';

    #This is actual page generation based on further details of the $path
    protected function pageGen(array $path): array
    {
        return match($path[0]) {
            'activate' => (new Activation)->get(\array_slice($path, 1)),
            'emails' => (new Emails)->get(\array_slice($path, 1)),
            'unsubscribe' => (new Unsubscribe)->get(\array_slice($path, 1)),
            'password' => (new Password)->get(\array_slice($path, 1)),
            'profile' => (new Profile)->get(\array_slice($path, 1)),
            'removal' => (new Removal)->get(\array_slice($path, 1)),
            'sessions' => (new Sessions)->get(\array_slice($path, 1)),
            'fftracker' => (new FFTracker)->get(\array_slice($path, 1)),
            'avatars' => (new Avatars)->get(\array_slice($path, 1)),
            'register' => ['subservice_name' => 'registration'],
        };
    }
}
