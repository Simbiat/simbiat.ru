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
    protected string $ogimage = '/ogimages/devicedetector.png';
    #Directories relative to working dir
    protected array $dirs = [
        'bottype' => ['path' => '/img/devicedetector/bot/category', 'name' => 'Bot Types'],
        'bot' => ['path' => '/img/devicedetector/bot', 'name' => 'Bots'],
        'clienttype' => ['path' => '/img/devicedetector/client/type', 'name' => 'Client Types'],
        'os' => ['path' => '/img/devicedetector/client/os', 'name' => 'Operating Systems'],
        'osfamily' => ['path' => '/img/devicedetector/client/os/family', 'name' => 'Operating System Families'],
        'browser' => ['path' => '/img/devicedetector/client/browser', 'name' => 'Browsers'],
        'browserengine' => ['path' => '/img/devicedetector/client/browser/engine', 'name' => 'Browser Engines'],
        'browserfamily' => ['path' => '/img/devicedetector/client/browser/family', 'name' => 'Browser Families'],
        'app' => ['path' => '/img/devicedetector/client/mobile app', 'name' => 'Applications'],
        'library' => ['path' => '/img/devicedetector/client/library', 'name' => 'Libraries'],
        'feedreader' => ['path' => '/img/devicedetector/client/feed reader', 'name' => 'Feed Readers'],
        'pim' => ['path' => '/img/devicedetector/client/pim', 'name' => 'Personal Information Managers'],
        'mediaplayer' => ['path' => '/img/devicedetector/client/mediaplayer', 'name' => 'Media Players'],
        'devicetype' => ['path' => '/img/devicedetector/device/type', 'name' => 'Device Types'],
        'brand' => ['path' => '/img/devicedetector/device/brand', 'name' => 'Device Brands'],
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
        $fileDetails['icon'] = $fileDetails['path'].'/'.$fileDetails['filename'];
    }
}
