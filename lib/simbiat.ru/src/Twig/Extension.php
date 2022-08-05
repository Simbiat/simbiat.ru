<?php
declare(strict_types=1);
namespace Simbiat\Twig;

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
            new TwigFunction('sanitize', [$runtime, 'sanitize'], ['is_safe' => ['html']]),
            new TwigFunction('timeTag', [$runtime, 'timeTag'], ['is_safe' => ['html']]),
            new TwigFunction('tooltip', [$runtime, 'tooltip'], ['is_safe' => ['html']]),
            new TwigFunction('linkTags', [$runtime, 'linkTags'], ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals(): array
    {
        $defaults = [
            'site_name' => $GLOBALS['siteconfig']['site_name'],
            'domain' => $GLOBALS['siteconfig']['domain'],
            'url' => $GLOBALS['siteconfig']['domain'].'/'.($_SERVER['REQUEST_URI'] ?? 'no_request_uri'),
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
            'isPROD' => HomePage::$PROD,
            #List of LINK tags
            'link_tags' => $GLOBALS['siteconfig']['links'],
            #Time used as version of the JS file for cache busting
            'js_version' => filemtime($GLOBALS['siteconfig']['jsdir'].'main.min.js'),
            #Save data flag
            'save_data' => $save_data,
            'unsupported' => false,
            'ogdesc' => $GLOBALS['siteconfig']['ogdesc'],
            'ogextra' => $GLOBALS['siteconfig']['ogextra'],
            'ogimage' => $GLOBALS['siteconfig']['ogimage'],
            #Flag whether GET is present
            'hasGet' => !empty($_GET),
        ]);
    }
}
