<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

// TODO: review the settings when migrating security-related stuff (sessions, authentication, CSRF, etc.)
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'users_in_memory' => [
                'memory' => null,
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_profiler|_wdt|assets|build)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'users_in_memory',
            ],
        ],
        'access_control' => null,
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
