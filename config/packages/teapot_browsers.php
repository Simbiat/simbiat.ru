<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('app.teapot_browsers', [
        'Chrome Mobile' => '120.0.0',
        'Firefox Mobile' => '153.0.0',
        'Chrome' => '120.0.0',
        'Microsoft Edge' => '120.0.0',
        'Firefox' => '148.0.0',
        'Safari' => '26.0.0',
        'Opera Mobile' => '80.0.0',
        'Opera' => '106.0.0',
        'Samsung Browser' => '25.0.0',
    ]);
};
