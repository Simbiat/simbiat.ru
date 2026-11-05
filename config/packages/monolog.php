<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'channels' => [
            'deprecation',
        ],
    ]);
    if ($containerConfigurator->env() === 'dev') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'console' => [
                    'channels' => [
                        '!event',
                        '!doctrine',
                        '!console',
                    ],
                    'process_psr_3_messages' => false,
                    'type' => 'console',
                ],
                'main' => [
                    'channels' => [
                        '!event',
                    ],
                    'level' => 'debug',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'type' => 'stream',
                ],
            ],
        ]);
    }

    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'action_level' => 'error',
                    'channels' => [
                        '!event',
                    ],
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'handler' => 'nested',
                    'type' => 'fingers_crossed',
                ],
                'nested' => [
                    'level' => 'debug',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'type' => 'stream',
                ],
            ],
        ]);
    }

    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'console' => [
                    'channels' => [
                        '!event',
                        '!doctrine',
                    ],
                    'process_psr_3_messages' => false,
                    'type' => 'console',
                ],
                'deprecation' => [
                    'channels' => [
                        'deprecation',
                    ],
                    'formatter' => 'monolog.formatter.json',
                    'path' => 'php://stderr',
                    'type' => 'stream',
                ],
                'main' => [
                    'action_level' => 'error',
                    'buffer_size' => 50,
                    'channels' => [
                        '!deprecation',
                    ],
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'handler' => 'nested',
                    'type' => 'fingers_crossed',
                ],
                'nested' => [
                    'formatter' => 'monolog.formatter.json',
                    'level' => 'debug',
                    'path' => 'php://stderr',
                    'type' => 'stream',
                ],
            ],
        ]);
    }
};
