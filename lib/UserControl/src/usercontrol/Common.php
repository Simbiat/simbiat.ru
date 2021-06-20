<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Simbiat\Database\Controller;

trait Common
{
    #Flag for SEO statistics
    public static bool $SEOtracking = true;
    #Cached DB controller
    public static ?Controller $dbController = NULL;
    #Whether SMS OTP is supported
    public static bool $sms = false;

    #Function to log actions
    private function log(string $type, string $action, mixed $extras = NULL): bool
    {
        if (!empty($extras)) {
            $extras = json_encode($extras, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        }
        #Get IP
        $ip = $this->getIP();
        #Get username
        $userid = @$_SESSION['userid'];
        try {
            #Cache DB controller, if not done already
            if (self::$dbController === NULL) {
                self::$dbController = new Controller;
            }
            self::$dbController->query(
                'INSERT INTO `uc__logs` (`time`, `type`, `action`, `userid`, `ip`, `useragent`, `extra`) VALUES (current_timestamp(), (SELECT `typeid` FROM `uc__log_types` WHERE `name`=:type), :action, :userid, :ip, :ua, :extras);',
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
                        (empty($_SERVER['HTTP_USER_AGENT']) ? NULL : $_SERVER['HTTP_USER_AGENT']),
                        (empty($_SERVER['HTTP_USER_AGENT']) ? 'null' : 'string'),
                    ],
                    ':extras' => [
                        (empty($extras) ? NULL : $extras),
                        (empty($extras) ? 'null' : 'string'),
                    ],
                ]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    #Function to return IP
    public function getIP(): ?string
    {
        #Check if behind proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            #Get list of IPs, that do validate as proper IP
            $ips = array_filter(array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])), function($value) {
                return filter_var($value, FILTER_VALIDATE_IP);
            });
            #Check if any are left
            if (!empty($ips)) {
                #Get the right-most IP
                return array_pop($ips);
            }
        }
        #Check if REMOTE_ADDR is set (it's more appropriate and secure to use it)
        if(!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            if ($ip !== false) {
                return $ip;
            }
        }
        #Check if Client-IP is set. Can be easily spoofed, but it's not like we have a choice at this moment
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
            if ($ip !== false) {
                return $ip;
            }
        }
        return NULL;
    }

    #Get Bot name, OS and Browser for user agent
    private function getUA(): ?array
    {
        #Check if User Agent is present
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return NULL;
        }
        #Force full versions
        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
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
        return ['bot' => NULL, 'os' => ($os !== NULL ? substr($os, 0, 100) : NULL), 'client' => ($client !== NULL ? substr($client, 0, 100) : NULL)];
    }
}
