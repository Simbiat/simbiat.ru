<?php

declare(strict_types=1);

namespace App\Service\Routing\Api;

use App\Controller\Api\Api;
use App\Controller\Api\UserControl\Avatars;
use App\Controller\Api\UserControl\Cookies;
use App\Controller\Api\UserControl\Emails;
use App\Controller\Api\UserControl\FFLink;
use App\Controller\Api\UserControl\Login;
use App\Controller\Api\UserControl\Logout;
use App\Controller\Api\UserControl\Notifications;
use App\Controller\Api\UserControl\Password;
use App\Controller\Api\UserControl\Profile;
use App\Controller\Api\UserControl\Register;
use App\Controller\Api\UserControl\Remind;
use App\Controller\Api\UserControl\Remove;
use App\Controller\Api\UserControl\Sessions;
use App\Controller\Api\UserControl\Username;

final class UserControl extends Api
{
    /**
     * @var array<int, string> Supported edges
     */
    #[\Override]
    protected array $sub_routes = [
        'register', 'login', 'remind', 'logout', 'emails', 'notifications', 'password', 'username', 'profile', 'fflink', 'avatars', 'cookies', 'sessions', 'remove',
    ];

    /**
     * @var array<int, string> Description of the nodes (need to be in the same order)
     */
    #[\Override]
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

    protected function genData(array $path): array
    {
        return match ($path[0]) {
            'remind' => new Remind()->route(\array_slice($path, 1)),
            'login' => new Login()->route(\array_slice($path, 1)),
            'logout' => new Logout()->route(\array_slice($path, 1)),
            'register' => new Register()->route(\array_slice($path, 1)),
            'emails' => new Emails()->route(\array_slice($path, 1)),
            'notifications' => new Notifications()->route(\array_slice($path, 1)),
            'password' => new Password()->route(\array_slice($path, 1)),
            'username' => new Username()->route(\array_slice($path, 1)),
            'profile' => new Profile()->route(\array_slice($path, 1)),
            'fflink' => new FFLink()->route(\array_slice($path, 1)),
            'cookies' => new Cookies()->route(\array_slice($path, 1)),
            'sessions' => new Sessions()->route(\array_slice($path, 1)),
            'avatars' => new Avatars()->route(\array_slice($path, 1)),
            'remove' => new Remove()->route(\array_slice($path, 1)),
        };
    }
}
