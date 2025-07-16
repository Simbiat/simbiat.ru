<?php
declare(strict_types = 1);

namespace Simbiat\Website\Twig;

#Twig environment
use Simbiat\Website\Config;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class to prepare the Twig environment object
 */
final class EnvironmentGenerator
{
    private static ?Environment $environment = null;
    
    /**
     * Function to get the actual Twig environment object
     *
     * @return \Twig\Environment
     * @throws \Twig\Error\LoaderError
     */
    public static function getTwig(): Environment
    {
        if (!self::$environment) {
            $templates_dir = Config::$work_dir.'/templates/';
            #Initiate Twig
            $loader = new FilesystemLoader($templates_dir);
            $loader->addPath(Config::$work_dir.'/public/assets/images/', 'images'); // this creates the @images namespace
            self::$environment = new Environment($loader, ['cache' => Config::$work_dir.'/data/cache/twig/', 'auto_reload' => true, 'autoescape' => 'html', 'use_yield' => true]);
            self::$environment->addExtension(new Extension());
        }
        return self::$environment;
    }
}
