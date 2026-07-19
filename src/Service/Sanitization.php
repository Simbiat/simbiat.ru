<?php
declare(strict_types = 1);

namespace App\Service;

use JetBrains\PhpStorm\ExpectedValues;
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
    
    #Static sanitizer configs for a little bit of performance
    private(set) static array $sanitizer_config = ['body' => null, 'head' => null, 'timeline' => null];
    
    /**
     * Elements names, that can be used for sanitization
     */
    public const array SANITIZATION_ELEMENT_NAMES = ['body', 'head', 'timeline'];
    
    /**
     * Sanitize HTML string
     *
     * @param string $string String to sanitize
     * @param string $for    Flag indicating for which element we are doing sanitization
     *
     * @return string
     */
    public static function sanitizeHTML(string $string, #[ExpectedValues(self::SANITIZATION_ELEMENT_NAMES)] string $for = 'body'): string
    {
        if (!in_array($for, self::SANITIZATION_ELEMENT_NAMES, true)) {
            return '';
        }
        #Check if config has been created already
        if (self::$sanitizer_config[$for]) {
            $config = self::$sanitizer_config[$for];
        } else {
            $config = self::initSanitizer($for);
        }
        #Remove excessive new lines
        $string = \preg_replace(['/(\s*<br \/>\s*){5,}/mi', '/(^(<br \/>\s*)+)|((<br \/>\s*)+$)/mi'], ['<br>', ''], $string);
        #Run the sanitizer
        $sanitizer = new HtmlSanitizer($config);
        if ($for === 'head') {
            $string = $sanitizer->sanitizeFor('head', $string);
        } else {
            $string = $sanitizer->sanitize($string);
        }
        #TODO add loading="lazy" decoding="async" to all images
        return $string;
    }
    
    /**
     * Helper function to generate HtmlSanitizerConfig if it's not created yet
     *
     * @param string $for Flag indicating for which element we are doing sanitization
     *
     * @return \Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig
     */
    private static function initSanitizer(#[ExpectedValues(self::SANITIZATION_ELEMENT_NAMES)] string $for = 'body'): HtmlSanitizerConfig
    {
        $config = new HtmlSanitizerConfig()->withMaxInputLength(-1)->allowSafeElements()
            ->allowRelativeLinks()->allowMediaHosts([Config::$http_host])->allowRelativeMedias()
            ->forceHttpsUrls()->allowLinkSchemes(['https', 'mailto'])->allowMediaSchemes(['https']);
        #Block some extra elements
        foreach (['acronym', 'applet', 'area', 'aside', 'base', 'basefont', 'bgsound', 'big', 'blink', 'body', 'button', 'canvas', 'center', 'content', 'datalist',
                     'dialog', 'dir', 'embed', 'fieldset', 'figure', 'figcaption', 'font', 'footer', 'form', 'frame', 'frameset', 'head', 'header', 'hgroup', 'html',
                     'iframe', 'input', 'image', 'keygen', 'legend', 'link', 'main', 'map', 'marquee', 'menuitem', 'meter', 'nav', 'nobr', 'noembed', 'noframes',
                     'noscript', 'object', 'optgroup', 'option', 'param', 'picture', 'plaintext', 'portal', 'pre', 'progress', 'rb', 'rp', 'rt', 'rtc', 'ruby', 'script',
                     'select', 'selectmenu', 'shadow', 'slot', 'strike', 'style', 'spacer', 'template', 'textarea', 'title', 'tt', 'xmp']
                 as $element) {
            #Need to update the original, because a clone is returned, instead of the same instance.
            $config = $config->blockElement($element);
        }
        #Allow timeline elements
        if ($for === 'timeline') {
            $config = $config->allowElement('time-line');
            $config = $config->allowElement('time-line-shortcut');
        }
        #Allow some property attributes for meta-tags
        if ($for === 'head') {
            $config = $config->allowAttribute('property', 'meta');
        }
        #Allow class attribute
        $config = $config->allowAttribute('class', '*');
        #Allow ARIA attributes
        foreach (['aria-activedescendant', 'aria-atomic', 'aria-atomic', 'aria-autocomplete', 'aria-busy', 'aria-busy', 'aria-checked', 'aria-colcount', 'aria-colindex',
                     'aria-colspan', 'aria-controls', 'aria-controls', 'aria-current', 'aria-describedby', 'aria-describedby', 'aria-description', 'aria-description',
                     'aria-details', 'aria-details', 'aria-disabled', 'aria-disabled', 'aria-dropeffect', 'aria-dropeffect', 'aria-errormessage', 'aria-errormessage',
                     'aria-errormessage', 'aria-expanded', 'aria-flowto', 'aria-flowto', 'aria-grabbed', 'aria-grabbed', 'aria-haspopup', 'aria-haspopup', 'aria-hidden',
                     'aria-hidden', 'aria-invalid', 'aria-invalid', 'aria-keyshortcuts', 'aria-label', 'aria-label', 'aria-labelledby', 'aria-labelledby', 'aria-level',
                     'aria-live', 'aria-live', 'aria-modal', 'aria-multiline', 'aria-multiselectable', 'aria-orientation', 'aria-owns', 'aria-owns', 'aria-placeholder',
                     'aria-posinset', 'aria-pressed', 'aria-readonly', 'aria-relevant', 'aria-relevant', 'aria-required', 'aria-roledescription', 'aria-rowcount',
                     'aria-rowindex', 'aria-rowspan', 'aria-selected', 'aria-setsize', 'aria-sort', 'aria-valuemax', 'aria-valuemin', 'aria-valuenow', 'aria-valuetext']
                 as $attribute) {
            $config = $config->allowAttribute($attribute, '*');
        }
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
        self::$sanitizer_config[$for] = $config;
        return $config;
    }
    
    /**
     * Remove controls characters from strings and arrays.
     * @param string $string    String to sanitize. Arrays are also accepted, but it's expected that they will have string values only.
     * @param bool   $full_list Flag whether newlines and tabs should also be removed
     *
     * @return string
     */
    public static function removeNonPrintable(string $string, bool $full_list = false): string
    {
        if ($full_list) {
            return \preg_replace('/[[:cntrl:]]/iu', '', $string) ?? '';
        }
        return \preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/iu', '', $string) ?? '';
    }
    
    /**
     * Remove control characters from strings in an array.
     * @param array $array     Array to sanitize
     * @param bool  $full_list Flag whether newlines and tabs should also be removed
     *
     * @return void
     */
    public static function carefulArraySanitization(array &$array, bool $full_list = false): void
    {
        foreach ($array as &$item) {
            if (\is_string($item)) {
                $item = self::removeNonPrintable($item, $full_list);
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
                if (empty($timezone) || !in_array($timezone, \timezone_identifiers_list(), true)) {
                    $timezone = 'UTC';
                }
                $datetime = SandClock::convertTimezone($time, $_SESSION['timezone'] ?? $timezone);
                $time = $datetime->getTimestamp();
                $cur_time = \time();
                #Do not allow past, unless respective permission is present
                if ($time < $cur_time && !in_array('post_backlog', $_SESSION['permissions'], true)) {
                    $time = $cur_time;
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
        $hash_tree = self::hashTree($filename);
        #Check if the file exists in images
        if (\file_exists(Config::$uploaded_img.'/'.$hash_tree.'/'.$filename)) {
            return '/assets/images/uploaded/'.$hash_tree.'/'.$filename;
        }
        if (\file_exists(Config::$uploaded.'/'.$hash_tree.'/'.$filename)) {
            return '/assets/uploaded/'.$hash_tree.'/'.$filename;
        }
        return '/assets/images/noimage.svg';
    }
}
