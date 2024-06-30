<?php
declare(strict_types = 1);

namespace Simbiat\Website\Twig;

#Twig environment
use Simbiat\Website\Config;
use Simbiat\Website\Twig\Extension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class EnvironmentGenerator
{
    private static ?Environment $environment = null;
    
    public static function getTwig(): Environment
    {
        if (!self::$environment) {
            $templatesDir = Config::$workDir.'/twig/';
            #Initiate Twig
            self::$environment = new Environment(new FilesystemLoader($templatesDir), ['cache' => Config::$workDir.'/data/cache/twig/', 'auto_reload' => true, 'autoescape' => 'html', 'use_yield' => true]);
            self::$environment->addExtension(new Extension());
        }
        return self::$environment;
    }
}
