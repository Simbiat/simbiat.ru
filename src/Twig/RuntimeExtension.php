<?php
declare(strict_types = 1);

namespace App\Twig;

use App\Service\Sanitization;
use JetBrains\PhpStorm\Pure;
use Simbiat\CuteBytes;
use Simbiat\http20\HTML;
use Simbiat\http20\Links;
use Simbiat\SandClock;
use Simbiat\StringHelpers\Convert;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Collection of methods to pass to expose to Twig
 */
final class RuntimeExtension implements RuntimeExtensionInterface
{
    /**
     * Generate a link for the file
     * @param string $filename
     *
     * @return string
     */
    #[Pure] public function uploadedLink(string $filename): string
    {
        return Sanitization::getUploadedFileLink($filename);
    }
    
    /**
     * Format numeric as bytes
     * @param int|string|float $bytes
     *
     * @return string
     */
    public function cuteBytes(int|string|float $bytes): string
    {
        return CuteBytes::bytes($bytes);
    }
    
    /**
     * Generate breadcrumbs
     * @param array $items
     *
     * @return string
     */
    public function genBread(array $items): string
    {
        return HTML::breadcrumbs($items);
    }
    
    /**
     * Generate timeline
     * @param array $items    Array of items
     * @param int   $br_limit Maximum number of `<br>` elements between items
     *
     * @return string
     */
    public function timeline(array $items, int $br_limit = 0): string
    {
        return Sanitization::sanitizeHTML(HTML::timeline($items, br_limit: $br_limit), 'timeline');
    }
    
    /**
     * Generate pagination
     * @param int    $current Current page
     * @param int    $total   Total pages
     * @param string $prefix  Prefix to use
     *
     * @return string
     */
    public function pagination(int $current, int $total, string $prefix): string
    {
        return HTML::pagination($current, $total, 7, prefix: $prefix, tooltip: 'data-tooltip');
    }
    
    /**
     * PHP's basename function
     * @param string $string
     *
     * @return string
     */
    public function basename(string $string): string
    {
        return \basename($string);
    }
    
    /**
     * PHP's is_numeric function
     *
     * @param mixed $string
     *
     * @return bool
     * @noinspection PhpMethodNamingConventionInspection
     */
    public function is_numeric(mixed $string): bool
    {
        return \is_numeric($string);
    }
    
    /**
     * Sanitize HTML string
     * @param string $string
     * @param bool   $head
     *
     * @return string
     */
    public function sanitize(string $string, bool $head = false): string
    {
        return Sanitization::sanitizeHTML($string, $head ? 'head' : 'body');
    }
    
    /**
     * Generate `<time>` tag
     * @param int|string $string
     * @param string     $format
     * @param string     $classes
     *
     * @return string
     * @throws \DateInvalidTimeZoneException
     */
    public function timeTag(int|string $string, string $format = 'd/m/Y H:i', string $classes = ''): string
    {
        #Set time zone
        $timezone = $_SESSION['timezone'] ?? 'UTC';
        if (!\in_array($timezone, \timezone_identifiers_list(), true)) {
            $timezone = 'UTC';
        }
        #Create DateTime object while converting the time
        $datetime = SandClock::convertTimezone($string, 'UTC', $timezone);
        $datetime->setTimezone(new \DateTimeZone($timezone));
        return '<time datetime="'.$datetime->format('c').'"'.(empty($classes) ? '' : 'class="'.$classes.'"').'>'.$datetime->format($format).'</time>';
    }
    
    /**
     * Generate `<link>` tags
     * @param array  $links
     * @param string $type
     *
     * @return string
     */
    public function linkTags(array $links, string $type = 'header'): string
    {
        return Links::links($links, $type);
    }
    
    /**
     * Prettify URL
     * @param string $string
     *
     * @return string
     */
    public function prettyURL(string $string): string
    {
        return Convert::prettyURL($string, '_');
    }
}