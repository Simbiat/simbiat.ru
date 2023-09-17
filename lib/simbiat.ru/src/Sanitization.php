<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

#Class for some common sanitization functions
class Sanitization
{

    #Static sanitizer config for a little bit performance
    public static ?HtmlSanitizerConfig $sanitizerConfig = null;

    public static function sanitizeHTML(string $string, bool $head = false): string
    {
        #Check if config has been created already
        if (self::$sanitizerConfig) {
            $config = self::$sanitizerConfig;
        } else {
            $config = (new HtmlSanitizerConfig())->withMaxInputLength(-1)->allowSafeElements()
                        ->allowRelativeLinks()->allowMediaHosts([Common::$http_host])->allowRelativeMedias()
                        ->forceHttpsUrls()->allowLinkSchemes(['https', 'mailto'])->allowMediaSchemes(['https']);
            #Block some extra elements
            foreach (['acronym', 'applet', 'area', 'aside', 'base', 'basefont', 'bgsound', 'big', 'blink', 'body', 'button', 'canvas', 'center', 'content', 'datalist',
                         'dialog', 'dir', 'embed', 'fieldset', 'figure', 'figcaption', 'font', 'footer', 'form', 'frame', 'frameset', 'head', 'header', 'hgroup', 'html',
                         'iframe', 'input', 'image', 'keygen', 'legend', 'link', 'main', 'map', 'marquee', 'menuitem', 'meter', 'nav', 'nobr', 'noembed', 'noframes',
                         'noscript', 'object', 'optgroup', 'option', 'param', 'picture', 'plaintext', 'portal', 'pre', 'progress', 'rb', 'rp', 'rt', 'rtc', 'ruby', 'script',
                         'select', 'selectmenu', 'shadow', 'slot', 'strike', 'style', 'spacer', 'template', 'textarea', 'title', 'tt', 'xmp'] as $element) {
                #Need to update the original, because clone is returned, instead of the same instance.
                $config = $config->blockElement($element);
            }
            #Allow class attribute
            $config = $config->allowAttribute('class', '*');
            #Allow data-* attributes in blockquotes, code and samp
            $config = $config->allowAttribute('data-author', 'blockquote');
            $config = $config->allowAttribute('data-description', ['code', 'samp']);
            $config = $config->allowAttribute('data-source', ['blockquote', 'code', 'samp']);
            #Allow tooltips
            $config = $config->allowAttribute('data-tooltip', '*');
            #Drop title element, since it will create a tooltip using the browser's engine, which can create inconsistent experience
            $config = $config->dropAttribute('title', '*');
            #TinyMCE adds `border` attribute to tables, which we do not use, so dropping it for cleaner code
            $config = $config->dropAttribute('border', '*');
            #Save config to static for future reuse
            self::$sanitizerConfig = $config;
        }
        #Allow some property attributes for meta tags
        if ($head) {
            $config = $config->allowAttribute('property', 'meta');
        }
        #Remove excessive new lines
        $string = preg_replace('/(^(<br \/>\s*)+)|((<br \/>\s*)+$)/mi', '', preg_replace('/(\s*<br \/>\s*){5,}/mi', '<br>', $string));
        #Run the sanitizer
        $sanitizer = new HtmlSanitizer($config);
        if ($head) {
            $string = $sanitizer->sanitizeFor('head', $string);
        } else {
            $string = $sanitizer->sanitize($string);
        }
        return $string;
    }
    
    #Remove controls characters from strings with $fullList controlling whether newlines and tabs should be kept (false will keep them)
    public static function removeNonPrintable(string|array $string, bool $fullList = false): string|array
    {
        if ($fullList) {
            return preg_replace('/[[:cntrl:]]/iu', '', $string);
        } else {
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/iu', '', $string);
        }
    }
    
    #Remove controls characters from strings in an array with $fullList controlling whether newlines and tabs should be kept (false will keep them)
    public static function carefulArraySanitization(array &$array, $fullList = false): void
    {
        foreach($array as &$item) {
            if (is_string($item)) {
                $item = self::removeNonPrintable($item, $fullList);
            }
        }
    }
    
    #Function to convert checkbox values to boolean.
    #Using reference to "simulate" isset()/empty() behaviour (as per https://stackoverflow.com/questions/55060/php-function-argument-error-suppression-empty-isset-emulation)
    #Thus also suppressing respective inspection.
    #I mean, I could use @ when calling it, but if I forget it, it can result in error and even unexpected behaviour.
    /** @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection */
    public static function checkboxToBoolean(mixed &$checkbox): bool
    {
        if (!isset($checkbox)) {
            return false;
        } else {
            if (is_string($checkbox)) {
                if (strtolower($checkbox) === 'off') {
                    return false;
                } else {
                    return true;
                }
            } else {
                return boolval($checkbox);
            }
        }
    }
    
    #Function to sanitize time for creating scheduled section/threads/posts
    public static function scheduledTime(string|int|null &$time, ?string &$timezone = null): ?int
    {
        if (in_array('postScheduled', $_SESSION['permissions'])) {
            if (empty($time)) {
                $time = null;
            } else {
                if (empty($timezone) || !in_array($timezone, timezone_identifiers_list())) {
                    $timezone = 'UTC';
                }
                $datetime = SandClock::convertTimezone($time, $_SESSION['timezone'] ?? $timezone);
                $time = $datetime->getTimestamp();
                $curTime = time();
                #Sections should not be created in the past, so if time is less than current one - correct it
                if (!in_array('postBacklog', $_SESSION['permissions']) && $time < $curTime) {
                    $time = $curTime;
                }
            }
        } else {
            $time = null;
        }
        return $time;
    }
}
