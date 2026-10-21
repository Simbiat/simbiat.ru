<?php

declare(strict_types=1);

// TODO: need to either be absorbed into config/packages/twig.yaml or become a service.

namespace App\Twig;

// Twig environment
use Twig\Environment;
use Twig\Extension\CoreExtension;

/**
 * Class to prepare the Twig environment object.
 */
final class EnvironmentGenerator
{
    private static ?Environment $environment = null;

    /**
     * Function to get the actual Twig environment object.
     *
     * @return \Twig\Environment
     */
    public static function getTwig(): Environment
    {
        if (self::$environment !== null) {
            # TODO: needs to move to kernel.request listener
            self::$environment->getExtension(CoreExtension::class)->setTimezone($_SESSION['timezone'] ?? $_SERVER['HTTP_X_CLIENT_TIMEZONE'] ?? 'UTC');
        }

        return self::$environment;
    }
}
