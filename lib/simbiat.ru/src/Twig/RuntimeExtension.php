<?php
declare(strict_types=1);
namespace Simbiat\Twig;

use Simbiat\HomePage;
use Simbiat\HTTP20\HTML;
use Simbiat\Security;
use Twig\Extension\RuntimeExtensionInterface;

class RuntimeExtension implements RuntimeExtensionInterface
{
    private HTML $HTML;

    public function __construct()
    {
        $this->HTML = new HTML;
    }

    public function genBread(array $items): string
    {
        return $this->HTML->breadcrumbs($items);
    }

    public function timeline(array $items, int $brLimit = 0): string
    {
        return $this->HTML->timeline($items, brLimit: $brLimit);
    }

    public function pagination(int $current, int $total, string $prefix): string
    {
        return $this->HTML->pagination($current, $total, prefix: $prefix);
    }

    public function basename(string $string): string
    {
        return basename($string);
    }

    public function sanitize(string $string, bool $head = false): string
    {
        return Security::sanitizeHTML($string, $head);
    }

    public function timeTag(string $string, string $format = 'd/m/Y H:i', string $classes = ''): string
    {
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
    }

    public function tooltip(string $string): string
    {
        return '<img class="tooltipFootnote" alt="tooltip" src="/img/tooltip.svg" data-tooltip="'.$string.'">';
    }

    public function linkTags(array $links, string $type = 'header'): string
    {
        return HomePage::$headers->links($links, $type);
    }
}
