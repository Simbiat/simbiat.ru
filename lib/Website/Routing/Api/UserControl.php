<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing\Api;

use Simbiat\Website\Api\UserControl\Avatars;
use Simbiat\Website\Api\UserControl\Cookies;
use Simbiat\Website\Api\UserControl\Emails;
use Simbiat\Website\Api\UserControl\FFLink;
use Simbiat\Website\Api\UserControl\Login;
use Simbiat\Website\Api\UserControl\Logout;
use Simbiat\Website\Api\UserControl\Password;
use Simbiat\Website\Api\UserControl\Profile;
use Simbiat\Website\Api\UserControl\Register;
use Simbiat\Website\Api\UserControl\Remind;
use Simbiat\Website\Api\UserControl\Remove;
use Simbiat\Website\Api\UserControl\Sessions;
use Simbiat\Website\Api\UserControl\Username;

class UserControl extends \Simbiat\Website\Abstracts\Api
{
    #Supported edges
    protected array $sub_routes = [
        'register', 'login', 'remind', 'logout', 'emails', 'password', 'username', 'profile', 'fflink', 'avatars', 'cookies', 'sessions', 'remove'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routes_description = [
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
    protected bool $top_level = false;

    protected function genData(array $path): array
    {
        return match($path[0]) {
            'remind' => (new Remind)->route(\array_slice($path, 1)),
            'login' => (new Login)->route(\array_slice($path, 1)),
            'logout' => (new Logout)->route(\array_slice($path, 1)),
            'register' => (new Register)->route(\array_slice($path, 1)),
            'emails' => (new Emails)->route(\array_slice($path, 1)),
            'password' => (new Password)->route(\array_slice($path, 1)),
            'username' => (new Username)->route(\array_slice($path, 1)),
            'profile' => (new Profile)->route(\array_slice($path, 1)),
            'fflink' => (new FFLink)->route(\array_slice($path, 1)),
            'cookies' => (new Cookies)->route(\array_slice($path, 1)),
            'sessions' => (new Sessions)->route(\array_slice($path, 1)),
            'avatars' => (new Avatars)->route(\array_slice($path, 1)),
            'remove' => (new Remove)->route(\array_slice($path, 1)),
        };
    }
}
