<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol\Pages;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use GeoIp2\Database\Reader;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

/**
 * Page to manage user sessions
 */
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
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Get sessions
        $outputArray['sessions'] = Config::$dbController->selectAll('SELECT `time`, `cookieid`, `sessionid`, `uc__sessions`.`ip`, `useragent` FROM `uc__sessions` WHERE `userid`=:userid ORDER BY `time` DESC', [':userid' => $_SESSION['userid']]);
        #Get cookies
        $outputArray['cookies'] = Config::$dbController->selectAll('SELECT `time`, `cookieid`, `uc__cookies`.`ip`, `useragent` FROM `uc__cookies` WHERE `userid`=:userid ORDER BY `time` DESC', [':userid' => $_SESSION['userid']]);
        #Get logs
        $outputArray['logs'] = Config::$dbController->selectAll('SELECT `time`, `action`, `sys__logs`.`ip`, `useragent` FROM `sys__logs` WHERE `userid`=:userid AND `type` IN (1, 2, 3, 6, 7, 8, 9) ORDER BY `time` DESC LIMIT 50', [':userid' => $_SESSION['userid']]);
        #Create useragent object
        #Force full versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        #Initialize device detector
        $dd = (new DeviceDetector());
        #Prevent unnecessary trips to DB file by "caching" found IPs, since it's unlikely to have too many different ones
        $ips = [];
        #Expand useragent
        foreach (['sessions', 'cookies', 'logs'] as $type) {
            foreach ($outputArray[$type] as $key => $item) {
                if (!isset($ips[$item['ip']])) {
                    try {
                        $ips[$item['ip']] = [];
                        $geoIp = (new Reader(Config::$geoip.'GeoLite2-City.mmdb'))->city($item['ip']);
                    } catch (\Throwable) {
                        #Do nothing, not critical
                    } finally {
                        $ips[$item['ip']] = ['country' => $geoIp->country->name ?? null, 'city' => $geoIp->city->name ?? null];
                    }
                }
                $outputArray[$type][$key]['country'] = $ips[$item['ip']]['country'];
                $outputArray[$type][$key]['city'] = $ips[$item['ip']]['city'];
                $dd->setUserAgent($item['useragent']);
                $dd->parse();
                #Get OS
                $outputArray[$type][$key]['os'] = $dd->getOs();
                #Get client
                $outputArray[$type][$key]['client'] = $dd->getClient();
                #Set OS and client icon if they exist
                if (is_file(Config::$imgDir.'/devicedetector/client/os/'.$outputArray[$type][$key]['os']['name'].'.webp')) {
                    $outputArray[$type][$key]['os']['icon'] = '/assets/images/devicedetector/client/os/'.$outputArray[$type][$key]['os']['name'].'.webp';
                }
                if (is_file(Config::$imgDir.'/devicedetector/client/'.$outputArray[$type][$key]['client']['type'].'/'.$outputArray[$type][$key]['client']['name'].'.webp')) {
                    $outputArray[$type][$key]['client']['icon'] = '/assets/images/devicedetector/client/'.$outputArray[$type][$key]['client']['type'].'/'.$outputArray[$type][$key]['client']['name'].'.webp';
                }
                #Set country icon, if flag exists
                if (!empty($outputArray[$type][$key]['country']) && is_file(Config::$imgDir.'/flags/'.$outputArray[$type][$key]['country'].'.svg')) {
                    $outputArray[$type][$key]['countryIcon'] = '/assets/images/flags/'.$outputArray[$type][$key]['country'].'.svg';
                }
            }
        }
        $outputArray['current_session'] = session_id();
        return $outputArray;
    }
}
