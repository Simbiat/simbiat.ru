<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;
use Simbiat\Database\Controller;
use Simbiat\HomePage;

trait Common
{
    #Flag for SEO statistics
    public static bool $SEOTracking = true;
    #Cached DB controller
    public static ?Controller $dbController = NULL;

    #Function to log actions
    private function log(string $type, string $action, mixed $extras = NULL): bool
    {
        if (!empty($extras)) {
            $extras = json_encode($extras, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        }
        #Get IP
        $ip = @$_SESSION['IP'] ?? null;
        #Get username
        $userid = @$_SESSION['userid'] ?? null;
        #Get User Agent
        $ua = @$_SESSION['UA']['full'] ?? null;
        try {
            #Cache DB controller, if not done already
            if (self::$dbController === NULL) {
                self::$dbController = HomePage::$dbController;
            }
            self::$dbController->query(
                'INSERT INTO `sys__logs` (`time`, `type`, `action`, `userid`, `ip`, `useragent`, `extra`) VALUES (current_timestamp(), (SELECT `typeid` FROM `sys__log_types` WHERE `name`=:type), :action, :userid, :ip, :ua, :extras);',
                [
                    ':type' => $type,
                    ':action' => $action,
                    ':userid' => [
                        (empty($userid) ? NULL : $userid),
                        (empty($userid) ? 'null' : 'string'),
                    ],
                    ':ip' => [
                        (empty($ip) ? NULL : $ip),
                        (empty($ip) ? 'null' : 'string'),
                    ],
                    ':ua' => [
                        (empty($ua) ? NULL : $ua),
                        (empty($ua) ? 'null' : 'string'),
                    ],
                    ':extras' => [
                        (empty($extras) ? NULL : $extras),
                        (empty($extras) ? 'null' : 'string'),
                    ],
                ]
            );
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to return IP
    public function getIP(): ?string
    {
        $ip = null;
        #Check if behind proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            #Get list of IPs, that do validate as proper IP
            $ips = array_filter(array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])), function($value) {
                return filter_var($value, FILTER_VALIDATE_IP);
            });
            #Check if any are left
            if (!empty($ips)) {
                #Get the right-most IP
                $ip = array_pop($ips);
            }
        }
        if (empty($ip)) {
            #Check if REMOTE_ADDR is set (it's more appropriate and secure to use it)
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            }
        }
        if (empty($ip)) {
            #Check if Client-IP is set. Can be easily spoofed, but it's not like we have a choice at this moment
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
            }
        }
        if (!empty($ip)) {
            #Attempt to get country and city, if they are not already present in DB. And only do it if DB is already up.
            if (HomePage::$dbController !== null) {
                try {
                    if (!HomePage::$dbController->check('SELECT `ip` FROM `seo__ips` WHERE `ip`=:ip;', [':ip' => $ip])) {
                        #Get data from ipinfo.io
                        $ipinfo = (new IPinfo(settings: ['guzzle_opts' => ['verify' => false]]))->getDetails($ip);
                        #Write it to DB
                        if (empty($ipinfo->bogon) && !empty($ipinfo->country_name) && !empty($ipinfo->city)) {
                            HomePage::$dbController->query('INSERT IGNORE INTO `seo__ips` (`ip`, `country`, `city`) VALUES (:ip, :country, :city);', [
                                ':ip' => $ip,
                                ':country' => $ipinfo->country_name,
                                ':city' => $ipinfo->city,
                            ]);
                        }
                    }
                } catch (\Throwable) {
                    #Do nothing, this is not critical
                }
            }
            return $ip;
        } else {
            return null;
        }
    }

    #Get Bot name, OS and Browser for user agent
    private function getUA(): ?array
    {
        #Check if User Agent is present
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return NULL;
        }
        #Force full versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        #Initialize device detector
        $dd = (new DeviceDetector($_SERVER['HTTP_USER_AGENT']));
        $dd->parse();
        #Get bot name
        $bot = $dd->getBot();
        if ($bot !== NULL) {
            #Do not waste resources on bots
            return ['bot' => substr($bot['name'], 0, 64), 'os' => NULL, 'client' => NULL];
        }
        #Get OS
        $os = $dd->getOs();
        #Concat OS and version
        $os = trim(($os['name'] ?? '').' '.($os['version'] ?? ''));
        #Force OS to be NULL, if it's empty
        if (empty($os)) {
            $os = NULL;
        }
        #Get client
        $client = $dd->getClient();
        #Concat client and version
        $client = trim(($client['name'] ?? '').' '.($client['version'] ?? ''));
        #Force client to be NULL, if it's empty
        if (empty($client)) {
            $client = NULL;
        }
        return ['bot' => NULL, 'os' => ($os !== NULL ? substr($os, 0, 100) : NULL), 'client' => ($client !== NULL ? substr($client, 0, 100) : NULL), 'full' => $_SERVER['HTTP_USER_AGENT']];
    }
}
