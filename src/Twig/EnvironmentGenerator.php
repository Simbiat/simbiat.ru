<?php
declare(strict_types = 1);

#TODO: need to either be absorbed into config/packages/twig.yaml or become a service.
namespace App\Twig;

#Twig environment
use App\Service\Config;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
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
            $loader->addPath(Config::$work_dir.'/public/assets/styles/', 'styles'); // this creates the @styles namespace
            self::$environment = new Environment($loader, ['cache' => \sys_get_temp_dir().'/twig/', 'auto_reload' => true, 'autoescape' => 'html', 'use_yield' => true, 'strict_variables' => true]);
            self::$environment->addExtension(new Extension());
            self::$environment->addExtension(new CssInlinerExtension());
        }
        return self::$environment;
    }
}
