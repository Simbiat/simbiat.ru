<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// TODO: Review the settings when replacing custom CSRF with Symfony's
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'form' => [
            'csrf_protection' => [
                'token_id' => 'submit',
            ],
        ],
        'csrf_protection' => [
            'stateless_token_ids' => [
                'submit',
                'authenticate',
                'logout',
            ],
        ],
    ]);
};
