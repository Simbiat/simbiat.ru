<?php
declare(strict_types = 1);

namespace Simbiat\Website\Routing\Api;

use Simbiat\Talks\Api\UserControl\Avatars;
use Simbiat\Talks\Api\UserControl\Cookies;
use Simbiat\Talks\Api\UserControl\Emails;
use Simbiat\Talks\Api\UserControl\FFLink;
use Simbiat\Talks\Api\UserControl\Login;
use Simbiat\Talks\Api\UserControl\Logout;
use Simbiat\Talks\Api\UserControl\Notifications;
use Simbiat\Talks\Api\UserControl\Password;
use Simbiat\Talks\Api\UserControl\Profile;
use Simbiat\Talks\Api\UserControl\Register;
use Simbiat\Talks\Api\UserControl\Remind;
use Simbiat\Talks\Api\UserControl\Remove;
use Simbiat\Talks\Api\UserControl\Sessions;
use Simbiat\Talks\Api\UserControl\Username;
use Simbiat\Website\Abstracts\Api;

class UserControl extends Api
{
    #Supported edges
    protected array $sub_routes = [
        'register', 'login', 'remind', 'logout', 'emails', 'notifications', 'password', 'username', 'profile', 'fflink', 'avatars', 'cookies', 'sessions', 'remove'
    ];
    #Description of the nodes (need to be in same order)
    protected array $routes_description = [
        'Register on the website',
        'Login to the website',
        'Password reset',
        'Logout the current session',
        'Emails management',
        'Marking notifications as read',
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
            'notifications' => (new Notifications)->route(\array_slice($path, 1)),
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
