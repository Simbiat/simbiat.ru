<?php
declare(strict_types=1);
namespace Simbiat\Website\usercontrol;

use Simbiat\Website\usercontrol\Api\Avatars;
use Simbiat\Website\usercontrol\Api\Cookies;
use Simbiat\Website\usercontrol\Api\Emails;
use Simbiat\Website\usercontrol\Api\FFLink;
use Simbiat\Website\usercontrol\Api\Login;
use Simbiat\Website\usercontrol\Api\Logout;
use Simbiat\Website\usercontrol\Api\Password;
use Simbiat\Website\usercontrol\Api\Profile;
use Simbiat\Website\usercontrol\Api\Register;
use Simbiat\Website\usercontrol\Api\Remind;
use Simbiat\Website\usercontrol\Api\Remove;
use Simbiat\Website\usercontrol\Api\Sessions;
use Simbiat\Website\usercontrol\Api\Username;

class Api extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'register', 'login', 'remind', 'logout', 'emails', 'password', 'username', 'profile', 'fflink', 'avatars', 'cookies', 'sessions', 'remove'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [
        'Register on the website',
        'Login to the website',
        'Password reset',
        'Logout the current session',
        'Emails management',
        'Password change',
        'Username change',
        'Update profile details',
        'Link FFXIV characters',
        'Avatars management',
        'Delete cookies',
        'Delete sessions',
        'Remove the user',
    ];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'remind' => (new Remind)->route(array_slice($path, 1)),
            'login' => (new Login)->route(array_slice($path, 1)),
            'logout' => (new Logout)->route(array_slice($path, 1)),
            'register' => (new Register)->route(array_slice($path, 1)),
            'emails' => (new Emails)->route(array_slice($path, 1)),
            'password' => (new Password)->route(array_slice($path, 1)),
            'username' => (new Username)->route(array_slice($path, 1)),
            'profile' => (new Profile)->route(array_slice($path, 1)),
            'fflink' => (new FFLink)->route(array_slice($path, 1)),
            'cookies' => (new Cookies)->route(array_slice($path, 1)),
            'sessions' => (new Sessions)->route(array_slice($path, 1)),
            'avatars' => (new Avatars)->route(array_slice($path, 1)),
            'remove' => (new Remove)->route(array_slice($path, 1)),
        };
    }
}
