<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use JetBrains\PhpStorm\Pure;
use Simbiat\SandClock;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

use function in_array;

/**
 * Class for some common sanitization function
 */
class Sanitization
{
    
    #Static sanitizer config for a little bit of performance
    public static ?HtmlSanitizerConfig $sanitizerConfig = null;
    
    /**
     * Sanitize HTML string
     * @param string $string String to sanitize
     * @param bool   $head   Flag indicating whether we are sanitizing for `head`
     *
     * @return string
     */
    public static function sanitizeHTML(string $string, bool $head = false): string
    {
        #Check if config has been created already
        if (self::$sanitizerConfig) {
            $config = self::$sanitizerConfig;
        } else {
            $config = new HtmlSanitizerConfig()->withMaxInputLength(-1)->allowSafeElements()
                ->allowRelativeLinks()->allowMediaHosts([Config::$http_host])->allowRelativeMedias()
                ->forceHttpsUrls()->allowLinkSchemes(['https', 'mailto'])->allowMediaSchemes(['https']);
            #Block some extra elements
            foreach (['acronym', 'applet', 'area', 'aside', 'base', 'basefont', 'bgsound', 'big', 'blink', 'body', 'button', 'canvas', 'center', 'content', 'datalist',
                         'dialog', 'dir', 'embed', 'fieldset', 'figure', 'figcaption', 'font', 'footer', 'form', 'frame', 'frameset', 'head', 'header', 'hgroup', 'html',
                         'iframe', 'input', 'image', 'keygen', 'legend', 'link', 'main', 'map', 'marquee', 'menuitem', 'meter', 'nav', 'nobr', 'noembed', 'noframes',
                         'noscript', 'object', 'optgroup', 'option', 'param', 'picture', 'plaintext', 'portal', 'pre', 'progress', 'rb', 'rp', 'rt', 'rtc', 'ruby', 'script',
                         'select', 'selectmenu', 'shadow', 'slot', 'strike', 'style', 'spacer', 'template', 'textarea', 'title', 'tt', 'xmp'] as $element) {
                #Need to update the original, because a clone is returned, instead of the same instance.
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
            #Drop the title element, since it will create a tooltip using the browser's engine, which can create an inconsistent experience
            $config = $config->dropAttribute('title', '*');
            #TinyMCE adds the `border` attribute to tables, which we do not use, so dropping it for cleaner code
            $config = $config->dropAttribute('border', '*');
            #Save config to static for future reuse
            self::$sanitizerConfig = $config;
        }
        #Allow some property attributes for meta-tags
        if ($head) {
            $config = $config->allowAttribute('property', 'meta');
        }
        #Remove excessive new lines
        $string = preg_replace(['/(\s*<br \/>\s*){5,}/mi', '/(^(<br \/>\s*)+)|((<br \/>\s*)+$)/mi'], ['<br>', ''], $string);
        #Run the sanitizer
        $sanitizer = new HtmlSanitizer($config);
        if ($head) {
            $string = $sanitizer->sanitizeFor('head', $string);
        } else {
            $string = $sanitizer->sanitize($string);
        }
        return $string;
    }
    
    /**
     * Remove controls characters from strings and arrays.
     * @param string|array $string   String to sanitize. Arrays are also accepted, but it's expected that they will have string values only.
     * @param bool         $fullList Flag whether newlines and tabs should also be removed
     *
     * @return string|array
     */
    public static function removeNonPrintable(string|array $string, bool $fullList = false): string|array
    {
        if ($fullList) {
            return preg_replace('/[[:cntrl:]]/iu', '', $string);
        }
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/iu', '', $string);
    }
    
    /**
     * Remove control characters from strings in an array.
     * @param array $array    Array to sanitize
     * @param bool  $fullList Flag whether newlines and tabs should also be removed
     *
     * @return void
     */
    public static function carefulArraySanitization(array &$array, bool $fullList = false): void
    {
        foreach ($array as &$item) {
            if (\is_string($item)) {
                $item = self::removeNonPrintable($item, $fullList);
            }
        }
    }
    
    /**
     * Function to convert checkbox values to boolean.
     * Using reference to "simulate" isset()/empty() behavior (as per https://stackoverflow.com/questions/55060/php-function-argument-error-suppression-empty-isset-emulation)
     * Thus also suppressing the respective inspection.
     * I mean, I could use @ when calling it, but if I forget it, it can result in error and even unexpected behavior.
     *
     * @param mixed $checkbox
     *
     * @return bool
     */
    /** @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection */
    public static function checkboxToBoolean(mixed &$checkbox): bool
    {
        if (!isset($checkbox)) {
            return false;
        }
        if (\is_string($checkbox)) {
            return mb_strtolower($checkbox, 'UTF-8') !== 'off';
        }
        return (bool)$checkbox;
    }
    
    /**
     * Function to sanitize time for creating scheduled section/threads/posts
     * @param string|int|null $time
     * @param string|null     $timezone
     *
     * @return int|null
     */
    public static function scheduledTime(string|int|null &$time, ?string &$timezone = null): ?int
    {
        if (in_array('post_scheduled', $_SESSION['permissions'], true)) {
            if (empty($time)) {
                $time = null;
            } else {
                if (empty($timezone) || !in_array($timezone, timezone_identifiers_list(), true)) {
                    $timezone = 'UTC';
                }
                $datetime = SandClock::convertTimezone($time, $_SESSION['timezone'] ?? $timezone);
                $time = $datetime->getTimestamp();
                $curTime = time();
                #Sections should not be created in the past, so if time is less than the current one - correct it
                if ($time < $curTime && !in_array('post_backlog', $_SESSION['permissions'], true)) {
                    $time = $curTime;
                }
            }
        } else {
            $time = null;
        }
        return $time;
    }
    
    /**
     * Function to generate a "hash tree" from string
     * @param string $string
     *
     * @return string
     */
    public static function hashTree(string $string): string
    {
        return mb_substr($string, 0, 2, 'UTF-8').'/'.mb_substr($string, 2, 2, 'UTF-8').'/'.mb_substr($string, 4, 2, 'UTF-8');
    }
    
    /**
     * Get a link for the uploaded file based on its filename (ID + extension)
     *
     * @param string $filename
     *
     * @return string
     */
    #[Pure(true)] public static function getUploadedFileLink(string $filename): string
    {
        #Get hash tree
        $hashTree = self::hashTree($filename);
        #Check if the file exists in images
        if (file_exists(Config::$uploaded_img.'/'.$hashTree.'/'.$filename)) {
            return '/assets/images/uploaded/'.$hashTree.'/'.$filename;
        }
        if (file_exists(Config::$uploaded.'/'.$hashTree.'/'.$filename)) {
            return '/assets/uploaded/'.$hashTree.'/'.$filename;
        }
        return '/assets/images/noimage.svg';
    }
}
