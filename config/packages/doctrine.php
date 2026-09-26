<?php

declare(strict_types=1);

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Pdo\Mysql;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'default' => [
                    'url' => '%env(resolve:DATABASE_URL)%',
                    'server_version' => '13.0.2-MariaDB',
                    'options' => [
                        Mysql::ATTR_FOUND_ROWS => true,
                        PDO::ATTR_TIMEOUT => 1,
                        Mysql::ATTR_INIT_COMMAND => 'SET SESSION character_set_client = \'utf8mb4\',
                                                    SESSION collation_connection = \'utf8mb4_0900_as_cs\',
                                                    SESSION character_set_connection = \'utf8mb4\',
                                                    SESSION character_set_database = \'utf8mb4\',
                                                    SESSION character_set_results = \'utf8mb4\',
                                                    SESSION character_set_server = \'utf8mb4\',
                                                    SESSION time_zone=\'+00:00\'',
                    ],
                    'default_table_options' => [
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_0900_as_cs',
                        'engine' => 'InnoDB',
                    ],
                    'profiling_collect_backtrace' => '%kernel.debug%',
                ],
            ],
        ],
        'orm' => [
            'validate_xml_mapping' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
            'identity_generation_preferences' => [
                PostgreSQLPlatform::class => 'identity',
            ],
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Entity',
                    'prefix' => 'App\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);

    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }

    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
