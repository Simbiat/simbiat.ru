<?php
declare(strict_types=1);
namespace Simbiat\usercontrol\Pages;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Simbiat\Abstracts\Page;
use Simbiat\Config\Common;
use Simbiat\HomePage;

class Sessions extends Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [
        ['href' => '/uc/sessions', 'name' => 'Sessions']
    ];
    #Sub service name
    protected string $subServiceName = 'sessions';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = 'Sessions';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = 'Active sessions';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = 'Page to manage active sessions';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'private';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Link to JS module for preload
    protected string $jsModule = 'uc/sessions';

    #This is actual page generation based on further details of the $path
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Get sessions
        $outputArray['sessions'] = HomePage::$dbController->selectAll('SELECT `time`, `cookieid`, `sessionid`, `uc__sessions`.`ip`, `country`, `city`, `useragent` FROM `uc__sessions` LEFT JOIN `seo__ips` ON `seo__ips`.`ip`=`uc__sessions`.`ip` WHERE `userid`=:userid ORDER BY `time` DESC', [':userid' => $_SESSION['userid']]);
        #Get cookies
        $outputArray['cookies'] = HomePage::$dbController->selectAll('SELECT `time`, `cookieid`, `uc__cookies`.`ip`, `country`, `city`, `useragent` FROM `uc__cookies` LEFT JOIN `seo__ips` ON `seo__ips`.`ip`=`uc__cookies`.`ip` WHERE `userid`=:userid ORDER BY `time` DESC', [':userid' => $_SESSION['userid']]);
        #Get logs
        $outputArray['logs'] = HomePage::$dbController->selectAll('SELECT `time`, `action`, `sys__logs`.`ip`, `country`, `city`, `useragent` FROM `sys__logs` LEFT JOIN `seo__ips` ON `seo__ips`.`ip`=`sys__logs`.`ip` WHERE `userid`=:userid AND `type` IN (1, 2, 3, 6, 7, 8, 9) ORDER BY `time` DESC LIMIT 50', [':userid' => $_SESSION['userid']]);
        #Create useragent object
        #Force full versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        #Initialize device detector
        $dd = (new DeviceDetector());
        #Expand useragent
        foreach (['sessions', 'cookies', 'logs'] as $type) {
            foreach ($outputArray[$type] as $key => $item) {
                $dd->setUserAgent($item['useragent']);
                $dd->parse();
                #Get OS
                $outputArray[$type][$key]['os'] = $dd->getOs();
                #Get client
                $outputArray[$type][$key]['client'] = $dd->getClient();
                #Set OS and client icon if they exist
                if (is_file(Common::$imgDir.'/devicedetector/client/os/'.$outputArray[$type][$key]['os']['name'].'.webp')) {
                    $outputArray[$type][$key]['os']['icon'] = '/img/devicedetector/client/os/'.$outputArray[$type][$key]['os']['name'].'.webp';
                }
                if (is_file(Common::$imgDir.'/devicedetector/client/'.$outputArray[$type][$key]['client']['type'].'/'.$outputArray[$type][$key]['client']['name'].'.webp')) {
                    $outputArray[$type][$key]['client']['icon'] = '/img/devicedetector/client/'.$outputArray[$type][$key]['client']['type'].'/'.$outputArray[$type][$key]['client']['name'].'.webp';
                }
                #Set country icon, if flag exists
                if (!empty($item['country']) && is_file(Common::$imgDir.'/flags/'.$item['country'].'.svg')) {
                    $outputArray[$type][$key]['countryIcon'] = '/img/flags/'.$item['country'].'.svg';
                }
            }
        }
        $outputArray['current_session'] = session_id();
        return $outputArray;
    }
}
