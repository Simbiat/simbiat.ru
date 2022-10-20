<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\usercontrol\Api\Avatars;
use Simbiat\usercontrol\Api\Cookies;
use Simbiat\usercontrol\Api\Emails;
use Simbiat\usercontrol\Api\FFLink;
use Simbiat\usercontrol\Api\Login;
use Simbiat\usercontrol\Api\Logout;
use Simbiat\usercontrol\Api\Password;
use Simbiat\usercontrol\Api\Profile;
use Simbiat\usercontrol\Api\Register;
use Simbiat\usercontrol\Api\Remind;
use Simbiat\usercontrol\Api\Sessions;
use Simbiat\usercontrol\Api\Username;

class Api extends \Simbiat\Abstracts\Api
{
    #Supported edges
    protected array $subRoutes = [
        'register', 'login', 'remind', 'logout', 'emails', 'password', 'username', 'profile', 'fflink', 'avatars', 'cookies', 'sessions',
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
        };
    }
}
