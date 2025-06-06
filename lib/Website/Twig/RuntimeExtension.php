<?php
declare(strict_types = 1);

namespace Simbiat\Website\Twig;

use Simbiat\CuteBytes;
use Simbiat\HTML\Cut;
use Simbiat\http20\HTML;
use Simbiat\http20\Links;
use Simbiat\http20\PrettyURL;
use Simbiat\HTML\nl2tag;
use Simbiat\SandClock;
use Simbiat\Website\Sanitization;
use Twig\Extension\RuntimeExtensionInterface;

class RuntimeExtension implements RuntimeExtensionInterface
{
    public function uploadedLink(string $filename): string
    {
        return Sanitization::getUploadedFileLink($filename);
    }
    
    public function cuteBytes(int|string|float $bytes): string
    {
        return CuteBytes::bytes($bytes);
    }
    
    public function genBread(array $items): string
    {
        return HTML::breadcrumbs($items);
    }
    
    public function timeline(array $items, int $brLimit = 0): string
    {
        return HTML::timeline($items, brLimit: $brLimit);
    }
    
    public function pagination(int $current, int $total, string $prefix): string
    {
        return HTML::pagination($current, $total, prefix: $prefix, tooltip: 'data-tooltip');
    }
    
    public function basename(string $string): string
    {
        return basename($string);
    }
    
    public function is_numeric(mixed $string): bool
    {
        return is_numeric($string);
    }
    
    public function nl2p(string $string): string
    {
        $nl2tag = (new nl2tag());
        $nl2tag->preserveNonBreakingSpace = true;
        return $nl2tag->nl2p($string);
    }
    
    public function changelog(string $string): string
    {
        $nl2tag = (new nl2tag());
        $nl2tag->preserveNonBreakingSpace = true;
        return $nl2tag->changelog($string);
    }
    
    public function preg_replace(string $string, string $pattern, string $replace): string
    {
        $newString = preg_replace($pattern, $replace, $string);
        if (!\is_string($newString)) {
            return $string;
        }
        return $newString;
    }
    
    public function sanitize(string $string, bool $head = false): string
    {
        return Sanitization::sanitizeHTML($string, $head);
    }
    
    public function htmlCut(string $string, int $length = 250): string
    {
        return Cut::Cut($string, $length, 3);
    }
    
    public function timeTag(int|string $string, string $format = 'd/m/Y H:i', string $classes = ''): string
    {
        #Set timezone
        $timezone = $_SESSION['timezone'] ?? 'UTC';
        if (!\in_array($timezone, timezone_identifiers_list())) {
            $timezone = 'UTC';
        }
        #Create DateTime object while converting the time
        $datetime = SandClock::convertTimezone($string, 'UTC', $timezone);
        $datetime->setTimezone(new \DateTimeZone($timezone));
        return '<time datetime="'.$datetime->format('c').'"'.(empty($classes) ? '' : 'class="'.$classes.'"').'>'.$datetime->format($format).'</time>';
    }
    
    public function tooltip(string $string): string
    {
        return '<img class="tooltipFootnote" alt="tooltip" src="/assets/images/tooltip.svg" data-tooltip="'.$string.'">';
    }
    
    public function linkTags(array $links, string $type = 'header'): string
    {
        return Links::links($links, $type);
    }
    
    public function prettyURL(string $string): string
    {
        return PrettyURL::pretty($string, '_');
    }
}