<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages;

use Simbiat\Website\Abstracts\Pages\FileListing;

class DeviceDetector extends FileListing
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/simplepages/devicedetector', 'name' => 'Device Detector']
    ];
    #Sub service name
    protected string $subservice_name = 'devicedetector';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = 'Device Detector Icons';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = 'Device Detector Icons';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = 'Icons or logos of operating systems, browsers and other applications based on respective items detectable by matomo-org/device-detector library';
    protected string $og_image = '/ogimages/devicedetector.webp';
    #Directories relative to working dir
    protected array $dirs = [
        'bottype' => ['path' => '/lib/DDCIcons/src/icons/bot/category', 'name' => 'Bot Types'],
        'bot' => ['path' => '/lib/DDCIcons/src/icons/bot', 'name' => 'Bots'],
        'clienttype' => ['path' => '/lib/DDCIcons/src/icons/client/type', 'name' => 'Client Types'],
        'os' => ['path' => '/lib/DDCIcons/src/icons/client/os', 'name' => 'Operating Systems'],
        'osfamily' => ['path' => '/lib/DDCIcons/src/icons/client/os/family', 'name' => 'Operating System Families'],
        'browser' => ['path' => '/lib/DDCIcons/src/icons/client/browser', 'name' => 'Browsers'],
        'browserengine' => ['path' => '/lib/DDCIcons/src/icons/client/browser/engine', 'name' => 'Browser Engines'],
        'browserfamily' => ['path' => '/lib/DDCIcons/src/icons/client/browser/family', 'name' => 'Browser Families'],
        'app' => ['path' => '/lib/DDCIcons/src/icons/client/mobile app', 'name' => 'Applications'],
        'library' => ['path' => '/lib/DDCIcons/src/icons/client/library', 'name' => 'Libraries'],
        'feedreader' => ['path' => '/lib/DDCIcons/src/icons/client/feed reader', 'name' => 'Feed Readers'],
        'pim' => ['path' => '/lib/DDCIcons/src/icons/client/pim', 'name' => 'Personal Information Managers'],
        'mediaplayer' => ['path' => '/lib/DDCIcons/src/icons/client/mediaplayer', 'name' => 'Media Players'],
        'devicetype' => ['path' => '/lib/DDCIcons/src/icons/device/type', 'name' => 'Device Types'],
        'brand' => ['path' => '/lib/DDCIcons/src/icons/device/brand', 'name' => 'Device Brands'],
    ];
    
    protected function extra(array &$file_details): void
    {
        $file_details['name'] = match($file_details['basename']) {
            'OS2' => 'OS/2',
            'GNULinux' => 'GNU/Linux',
            'MTK  Nucleus' => 'MTK / Nucleus',
            'Perl RESTClient' => 'Perl REST::Client',
            'HTTP Tiny' => 'HTTP:Tiny',
            'ＡＵＸ' => 'AUX',
            default => $file_details['basename'],
        };
        $file_details['icon'] = \str_replace('/lib/DDCIcons/src/icons/', '/assets/images/devicedetector/', $file_details['path']).'/'.$file_details['filename'];
    }
}
