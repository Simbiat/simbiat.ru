<?php
declare(strict_types = 1);

namespace Simbiat\Website\Pages\UserControl;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use GeoIp2\Database\Reader;
use Simbiat\Database\Query;
use Simbiat\DDCIcons;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Website\Config;

/**
 * Page to manage user sessions
 */
class Sessions extends Page
{
    #Current breadcrumb for navigation
    protected array $breadcrumb = [
        ['href' => '/uc/sessions', 'name' => 'Sessions']
    ];
    #Sub service name
    protected string $subservice_name = 'sessions';
    #Page title. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $title = 'Sessions';
    #Page's H1 tag. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $h1 = 'Active sessions';
    #Page's description. Practically needed only for the main pages of the segment, since will be overridden otherwise
    protected string $og_desc = 'Page to manage active sessions';
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cache_strategy = 'private';
    #Flag indicating that authentication is required
    protected bool $authentication_needed = true;
    #Link to JS module for preload
    protected string $js_module = 'uc/sessions';
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        $output_array = [];
        #Get sessions
        $output_array['sessions'] = Query::query('SELECT `time`, `cookie_id`, `session_id`, `uc__sessions`.`ip`, `user_agent` FROM `uc__sessions` WHERE `user_id`=:user_id ORDER BY `time` DESC', [':user_id' => $_SESSION['user_id']], return: 'all');
        #Get cookies
        $output_array['cookies'] = Query::query('SELECT `time`, `cookie_id`, `uc__cookies`.`ip`, `user_agent` FROM `uc__cookies` WHERE `user_id`=:user_id ORDER BY `time` DESC', [':user_id' => $_SESSION['user_id']], return: 'all');
        #Get logs
        $output_array['logs'] = Query::query('SELECT `time`, `action`, `sys__logs`.`ip`, `user_agent` FROM `sys__logs` WHERE `user_id`=:user_id AND `type` IN (1, 2, 3, 6, 7, 8, 9) ORDER BY `time` DESC LIMIT 50', [':user_id' => $_SESSION['user_id']], return: 'all');
        #Create user_agent object
        #Force full string versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        #Initialize device detector
        $dd = (new DeviceDetector());
        #Prevent unnecessary trips to a DB file by "caching" found IPs, since it's unlikely to have too many different ones
        $ips = [];
        #Expand user_agent
        foreach (['sessions', 'cookies', 'logs'] as $type) {
            foreach ($output_array[$type] as $key => $item) {
                if (!isset($ips[$item['ip']])) {
                    try {
                        $ips[$item['ip']] = [];
                        $geoip = new Reader(Config::$geoip.'GeoLite2-City.mmdb')->city($item['ip']);
                    } catch (\Throwable) {
                        #Do nothing, not critical
                    } finally {
                        $ips[$item['ip']] = ['country' => $geoip->country->name ?? null, 'city' => $geoip->city->name ?? null];
                    }
                }
                $output_array[$type][$key]['country'] = $ips[$item['ip']]['country'];
                $output_array[$type][$key]['city'] = $ips[$item['ip']]['city'];
                $dd->setUserAgent((string)$item['user_agent']);
                $dd->parse();
                #Get OS
                $output_array[$type][$key]['os'] = $dd->getOs();
                #Get client
                $output_array[$type][$key]['client'] = $dd->getClient();
                #Set OS and client icon if they exist
                if (!empty($output_array[$type][$key]['os'])) {
                    $output_array[$type][$key]['os']['icon'] = DDCIcons::getOS($output_array[$type][$key]['os']['name'], $output_array[$type][$key]['os']['family']);
                }
                if (!empty($output_array[$type][$key]['client'])) {
                    $output_array[$type][$key]['client']['icon'] = DDCIcons::getClient($output_array[$type][$key]['client']['name'], $output_array[$type][$key]['client']['type']);
                }
                #Set country icon, if a flag exists
                if (!empty($output_array[$type][$key]['country']) && \is_file(Config::$img_dir.'/flags/'.$output_array[$type][$key]['country'].'.svg')) {
                    $output_array[$type][$key]['country_icon'] = '/assets/images/flags/'.$output_array[$type][$key]['country'].'.svg';
                }
            }
        }
        $output_array['current_session'] = \session_id();
        return $output_array;
    }
}
