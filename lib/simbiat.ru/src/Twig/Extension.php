<?php
declare(strict_types=1);
namespace Simbiat\Twig;

use Simbiat\Config\Common;
use Simbiat\HomePage;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class Extension extends AbstractExtension implements GlobalsInterface
{
    public function getFunctions(): array
    {
        $runtime = new RuntimeExtension;
        return [
            new TwigFunction('genBread', [$runtime, 'genBread'], ['is_safe' => ['html']]),
            new TwigFunction('timeline', [$runtime, 'timeline'], ['is_safe' => ['html']]),
            new TwigFunction('pagination', [$runtime, 'pagination'], ['is_safe' => ['html']]),
            new TwigFunction('basename', [$runtime, 'basename']),
            new TwigFunction('is_numeric', [$runtime, 'is_numeric']),
            new TwigFunction('nl2p', [$runtime, 'nl2p'], ['is_safe' => ['html']]),
            new TwigFunction('preg_replace', [$runtime, 'preg_replace'], ['is_safe' => ['html']]),
            new TwigFunction('sanitize', [$runtime, 'sanitize'], ['is_safe' => ['html']]),
            new TwigFunction('htmlCut', [$runtime, 'htmlCut'], ['is_safe' => ['html']]),
            new TwigFunction('timeTag', [$runtime, 'timeTag'], ['is_safe' => ['html']]),
            new TwigFunction('tooltip', [$runtime, 'tooltip'], ['is_safe' => ['html']]),
            new TwigFunction('linkTags', [$runtime, 'linkTags'], ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals(): array
    {
        $defaults = [
            'site_name' => Common::siteName,
            'domain' => Common::$baseUrl,
            'url' => Common::$baseUrl.'/'.($_SERVER['REQUEST_URI'] ?? ''),
            'maintenance' => 1,
            'registration' => 0,
        ];
        if (HomePage::$dbup) {
            #Update default variables with values from database
            try {
                $defaults = array_merge($defaults, HomePage::$dbController->selectPair('SELECT `setting`, `value` FROM `sys__settings`'));
            } catch (\Throwable) {
                #Do nothing, retain defaults
            }
        }
        #Flag for Save-Data header
        if (preg_match('/^on$/i', $_SERVER['HTTP_SAVE_DATA'] ?? '') === 1) {
            $save_data = 'true';
        } else {
            $save_data = 'false';
        }
        return array_merge($defaults, [
            #PROD flag
            'isPROD' => Common::$PROD,
            #List of LINK tags
            'link_tags' => Common::$links,
            #Time used as version of the JS file for cache busting
            'js_version' => filemtime(Common::$jsDir.'/main.min.js'),
            #Save data flag
            'save_data' => $save_data,
            'unsupported' => false,
            #Flag whether GET is present
            'hasGet' => !empty($_GET),
            'http_method' => HomePage::$method,
        ]);
    }
}
