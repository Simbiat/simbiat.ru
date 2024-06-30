<?php
declare(strict_types=1);
namespace Simbiat\Website\SimplePages\Pages;

use Simbiat\Website\Abstracts\Pages\FileListing;

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
    protected string $ogimage = '/ogimages/devicedetector.png';
    #Directories relative to working dir
    protected array $dirs = [
        'bottype' => ['path' => '/public/assets/images/devicedetector/bot/category', 'name' => 'Bot Types'],
        'bot' => ['path' => '/public/assets/images/devicedetector/bot', 'name' => 'Bots'],
        'clienttype' => ['path' => '/public/assets/images/devicedetector/client/type', 'name' => 'Client Types'],
        'os' => ['path' => '/public/assets/images/devicedetector/client/os', 'name' => 'Operating Systems'],
        'osfamily' => ['path' => '/public/assets/images/devicedetector/client/os/family', 'name' => 'Operating System Families'],
        'browser' => ['path' => '/public/assets/images/devicedetector/client/browser', 'name' => 'Browsers'],
        'browserengine' => ['path' => '/public/assets/images/devicedetector/client/browser/engine', 'name' => 'Browser Engines'],
        'browserfamily' => ['path' => '/public/assets/images/devicedetector/client/browser/family', 'name' => 'Browser Families'],
        'app' => ['path' => '/public/assets/images/devicedetector/client/mobile app', 'name' => 'Applications'],
        'library' => ['path' => '/public/assets/images/devicedetector/client/library', 'name' => 'Libraries'],
        'feedreader' => ['path' => '/public/assets/images/devicedetector/client/feed reader', 'name' => 'Feed Readers'],
        'pim' => ['path' => '/public/assets/images/devicedetector/client/pim', 'name' => 'Personal Information Managers'],
        'mediaplayer' => ['path' => '/public/assets/images/devicedetector/client/mediaplayer', 'name' => 'Media Players'],
        'devicetype' => ['path' => '/public/assets/images/devicedetector/device/type', 'name' => 'Device Types'],
        'brand' => ['path' => '/public/assets/images/devicedetector/device/brand', 'name' => 'Device Brands'],
    ];
    
    protected function extra(array &$fileDetails): void
    {
        $fileDetails['name'] = match($fileDetails['basename']) {
            'OS2' => 'OS/2',
            'GNULinux' => 'GNU/Linux',
            'MTK  Nucleus' => 'MTK / Nucleus',
            'Perl RESTClient' => 'Perl REST::Client',
            'HTTP Tiny' => 'HTTP:Tiny',
            'ＡＵＸ' => 'AUX',
            default => $fileDetails['basename'],
        };
        $fileDetails['icon'] = str_replace('/public/assets/', '/assets/', $fileDetails['path']).'/'.$fileDetails['filename'];
    }
}
