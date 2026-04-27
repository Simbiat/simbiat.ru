<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Controller\Abstracts\FileListing;

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
        'bottype' => ['path' => '/packages/DDCIcons/icons/bot/category', 'name' => 'Bot Types'],
        'bot' => ['path' => '/packages/DDCIcons/icons/bot', 'name' => 'Bots'],
        'clienttype' => ['path' => '/packages/DDCIcons/icons/client/type', 'name' => 'Client Types'],
        'os' => ['path' => '/packages/DDCIcons/icons/client/os', 'name' => 'Operating Systems'],
        'osfamily' => ['path' => '/packages/DDCIcons/icons/client/os/family', 'name' => 'Operating System Families'],
        'browser' => ['path' => '/packages/DDCIcons/icons/client/browser', 'name' => 'Browsers'],
        'browserengine' => ['path' => '/packages/DDCIcons/icons/client/browser/engine', 'name' => 'Browser Engines'],
        'browserfamily' => ['path' => '/packages/DDCIcons/icons/client/browser/family', 'name' => 'Browser Families'],
        'app' => ['path' => '/packages/DDCIcons/icons/client/mobile app', 'name' => 'Applications'],
        'library' => ['path' => '/packages/DDCIcons/icons/client/library', 'name' => 'Libraries'],
        'feedreader' => ['path' => '/packages/DDCIcons/icons/client/feed reader', 'name' => 'Feed Readers'],
        'pim' => ['path' => '/packages/DDCIcons/icons/client/pim', 'name' => 'Personal Information Managers'],
        'mediaplayer' => ['path' => '/packages/DDCIcons/icons/client/mediaplayer', 'name' => 'Media Players'],
        'devicetype' => ['path' => '/packages/DDCIcons/icons/device/type', 'name' => 'Device Types'],
        'brand' => ['path' => '/packages/DDCIcons/icons/device/brand', 'name' => 'Device Brands'],
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
        $file_details['icon'] = \str_replace('/packages/DDCIcons/icons/', '/assets/images/devicedetector/', $file_details['path']).'/'.$file_details['filename'];
    }
}
