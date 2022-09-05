<?php
declare(strict_types=1);
namespace Simbiat\SimplePages\Pages;

use Simbiat\Abstracts\Pages\FileListing;

class DeviceDetector extends FileListing
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/simplepages/devicedetector', 'name' => 'Device Detector']
    ];
    #Sub service name
    protected string $subServiceName = 'devicedetector';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Device Detector Icons';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Device Detector Icons';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Icons or logos of operating systems, browsers and other applications based on respective items detectable by matomo-org/device-detector library';
    #Directories relative to working dir
    protected array $dirs = [
        'os' => ['path' => '/img/devicedetector/os', 'name' => 'Operating Systems'],
        'browser' => ['path' => '/img/devicedetector/browser', 'name' => 'Browsers'],
        'app' => ['path' => '/img/devicedetector/mobile app', 'name' => 'Applications'],
        'library' => ['path' => '/img/devicedetector/library', 'name' => 'Libraries'],
        'feedreader' => ['path' => '/img/devicedetector/feed reader', 'name' => 'Feed Readers'],
        'pim' => ['path' => '/img/devicedetector/pim', 'name' => 'Personal Information Managers'],
        'mediaplayer' => ['path' => '/img/devicedetector/mediaplayer', 'name' => 'Media Players'],
    ];
    
    protected function extra(array &$fileDetails): void
    {
        $fileDetails['name'] = match($fileDetails['basename']) {
            'OS2' => 'OS/2',
            'GNULinux' => 'GNU/Linux',
            'MTK  Nucleus' => 'MTK / Nucleus',
            'Perl RESTClient' => 'Perl REST::Client',
            default => $fileDetails['basename'],
        };
        $fileDetails['icon'] = $fileDetails['path'].'/'.$fileDetails['filename'];
    }
}
