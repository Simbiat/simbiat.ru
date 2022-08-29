<?php
declare(strict_types=1);
namespace Simbiat\SimplePages\Pages;

use Simbiat\Abstracts\Pages\StaticPage;
use Simbiat\Config\Common;

class DeviceDetector extends StaticPage
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
    protected string $ogdesc = 'Icons or logos of operating system, browsers and mobile applications based on respective items detectable by matomo-org/device-detector library';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        #OS icons
        $outputArray['icons']['os'] = $this->genList('os');
        #Browser icons
        $outputArray['icons']['browser'] = $this->genList('browser');
        #App icons
        $outputArray['icons']['app'] = $this->genList('mobile app');
        #Library icons
        $outputArray['icons']['library'] = $this->genList('library');
        #Feed reader icons
        $outputArray['icons']['reader'] = $this->genList('feed reader');
        #PIM icons
        $outputArray['icons']['pim'] = $this->genList('pim');
        #Media player icons
        $outputArray['icons']['mediaplayer'] = $this->genList('mediaplayer');
        return $outputArray;
    }
    
    private function genList(string $type): array
    {
        $array = [];
        $icons = array_diff(scandir(Common::$imgDir.'/devicedetector/'.$type), ['..', '.']);
        foreach ($icons as $icon) {
            $name = pathinfo($icon, PATHINFO_FILENAME);
            #Restore names with special symbols
            $name = match($name) {
                'OS2' => 'OS/2',
                'GNULinux' => 'GNU/Linux',
                'MTK  Nucleus' => 'MTK / Nucleus',
                'Perl RESTClient' => 'Perl REST::Client',
                default => $name,
            };
            $array[] = [
                'name' => $name,
                'icon' => '/img/DeviceDetector/'.$type.'/'.$icon,
            ];
        }
        return $array;
    }
}
