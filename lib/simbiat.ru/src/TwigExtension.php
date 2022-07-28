<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\HTTP20\HTML;
use Simbiat\usercontrol\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('genBread', [new HTML(), 'breadcrumbs'], ['is_safe' => ['html']]),
            new TwigFunction('timeline', [new HTML(), 'timeline'], ['is_safe' => ['html']]),
            new TwigFunction('pagination', [new HTML(), 'pagination'], ['is_safe' => ['html']]),
            new TwigFunction('linkTags', [HomePage::$headers, 'links'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('basename', function(string $string) {
                return basename($string);
            }),
            new TwigFilter('sanitize', function(string $string, bool $head = false) {
                return (new Security())->sanitizeHTML($string, $head);
            }, ['is_safe' => ['html']]),
            new TwigFilter('timeTag', function(string $string, string $format = 'd/m/Y H:i', string $classes = '') {
                if (preg_match('/\d{10}/', $string) === 1) {
                    $datetime = new \DateTime();
                    $datetime->setTimestamp(intval($string));
                } else {
                    try {
                        $datetime = new \DateTime($string);
                    } catch (\Throwable) {
                        $datetime = new \DateTime();
                        $datetime->setTimestamp(intval($string));
                    }
                }
                #Set timezone
                $timezone = $_SESSION['timezone'] ?? 'UTC';
                if (!in_array($timezone, timezone_identifiers_list())) {
                    $timezone = 'UTC';
                }
                $datetime->setTimezone(new \DateTimeZone($timezone));
                return '<time datetime="'.$datetime->format('c').'"'.(empty($classes) ? '' : 'class="'.$classes.'"').'>'.$datetime->format($format).'</time>';
            }, ['is_safe' => ['html']]),
            new TwigFilter('tooltip', function(string $string) {
                return '<img class="tooltipFootnote" alt="tooltip" src="/img/tooltip.svg" data-tooltip="'.$string.'">';
            }, ['is_safe' => ['html']]),
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
