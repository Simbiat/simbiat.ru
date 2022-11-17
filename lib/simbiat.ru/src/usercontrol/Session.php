<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use ipinfo\ipinfo\IPinfo;
use Simbiat\Config\Common;
use Simbiat\Config\Talks;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Security;

class Session implements \SessionHandlerInterface, \SessionIdInterface, \SessionUpdateTimestampHandlerInterface
{
    #Default lifetime for session in seconds (15 minutes)
    private int $sessionLife;

    public function __construct(int $sessionLife = 2700)
    {
        #Set session name for easier identification. '__Host-' prefix signals to the browser that both the Path=/ and Secure attributes are required, so that subdomains cannot modify the session cookie.
        session_name('__Host-sess_'.preg_replace('/[^a-zA-Z\d\-_]/', '', Common::$http_host ?? 'simbiat'));
        if ($sessionLife < 0) {
            $sessionLife = 2700;
        }
        $this->sessionLife = $sessionLife;
    }

    ##########################
    #\SessionHandlerInterface#
    ##########################
    public function open(string $path, string $name): bool
    {
        #If controller was initialized - session is ready
        if (HomePage::$dbController === null) {
            return false;
        } else {
            return true;
        }
    }

    public function close(): bool
    {
        #No need to do anything at this point
        return true;
    }

    public function read(string $id): string
    {
        #Get session data
        try {
            $data = HomePage::$dbController->selectValue('SELECT `data` FROM `uc__sessions` WHERE `sessionid` = :id AND `time` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL :life SECOND)', [':id' => $id, ':life' => [$this->sessionLife, 'int']]);
        } catch (\Throwable) {
            $data = '';
        }
        if (!empty($data)) {
            #Decrypt data
            $data = Security::decrypt($data);
            #Deserialize to check if UserAgent data is present
            $data = unserialize($data);
        } else {
            $data = [];
        }
        #Login through cookie if present
        $data = array_merge($data, $this->cookieLogin());
        $this->dataRefresh($data);
        return serialize($data);
    }

    public function write(string $id, string $data): bool
    {
        #Deserialize to check if UserAgent data is present
        $data = unserialize($data);
        #Prepare empty array
        $queries = [];
        #Update SEO related tables, if this was determined to be a new page view
        if (empty($data['UA']['bot']) && $data['IP'] !== null && $data['newView']) {
            #Update unique visitors
            $queries[] = [
                'INSERT INTO `seo__visitors` SET `ip`=:ip, `os`=:os, `client`=:client ON DUPLICATE KEY UPDATE `views`=`views`+1;',
                [
                    #Data that makes this visitor unique
                    ':ip' => [$data['IP'], 'string'],
                    ':os' => [
                        (empty($data['UA']['os']) ? '' : $data['UA']['os']),
                        'string',
                    ],
                    ':client' => [
                        (empty($data['UA']['client']) ? '' : $data['UA']['client']),
                        'string',
                    ],
                ],
            ];
            #Update page views
            $page = substr(preg_replace('/^.*:\/\/[^\/]*\//ui', '', HomePage::$canonical), 0, 256);
            if (empty($page)) {
                $page = 'index.php';
            }
            $queries[] = [
                'INSERT INTO `seo__pageviews` SET `page`=:page, `referer`=:referer, `ip`=:ip, `os`=:os, `client`=:client ON DUPLICATE KEY UPDATE `views`=`views`+1;',
                [
                    #What page is being viewed
                    ':page' => $page,
                    #Optional referer (if sent from other sources)
                    ':referer' => [
                        (empty($_SERVER['HTTP_REFERER']) ? '' : substr($_SERVER['HTTP_REFERER'], 0, 256)),
                        'string',
                    ],
                    #Data that identify this visit as unique
                    ':ip' => [$data['IP'], 'string'],
                    ':os' => [
                        (empty($data['UA']['os']) ? '' : $data['UA']['os']),
                        'string',
                    ],
                    ':client' => [
                        (empty($data['UA']['client']) ? '' : $data['UA']['client']),
                        'string',
                    ],
                ],
            ];
        }
        #Write session data
        $queries[] = [
            'INSERT INTO `uc__sessions` SET `sessionid`=:id, `cookieid`=:cookieid, `userid`=:userid, `bot`=:bot, `ip`=:ip, `useragent`=:useragent, `username`=:username, `page`=:page, `data`=:data ON DUPLICATE KEY UPDATE `time`=UTC_TIMESTAMP(), `userid`=:userid, `bot`=:bot, `ip`=:ip, `useragent`=:useragent, `username`=:username, `page`=:page, `data`=:data;',
            [
                ':id' => $id,
                #Whether cookie is associated with this session
                ':cookieid' => [
                    (empty($data['cookieid']) ? NULL : $data['cookieid']),
                    (empty($data['cookieid']) ? 'null' : 'string'),
                ],
                #Whether this is a bot
                ':bot' => [(empty($data['UA']['bot']) ? 0 : 1), 'int'],
                ':ip' => [
                    (empty($data['IP']) ? NULL : $data['IP']),
                    (empty($data['IP']) ? 'null' : 'string'),
                ],
                #Useragent details only for logged-in users for ability of review of active sessions
                ':useragent' => [
                        (empty($data['UA']['full']) ? NULL : $data['UA']['full']),
                        (empty($data['UA']['full']) ? 'null' : 'string'),
                    ],
                #Either username (if logged in) or bot name, if it's a bot
                ':username' => [
                    (empty($data['username']) ? NULL : $data['username']),
                    (empty($data['username']) ? 'null' : 'string'),
                ],
                ':userid' => [$data['userid'], 'int'],
                #What page is being viewed
                ':page' => (empty($_SERVER['REQUEST_URI']) ? 'index.php' : substr($_SERVER['REQUEST_URI'], 0, 256)),
                #Actual session data
                ':data' => [
                    (empty($data) ? '' : Security::encrypt(serialize($data))),
                    'string',
                ],
            ],
        ];
        try {
            return HomePage::$dbController->query($queries);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'Queries: '.json_encode($queries));
            return false;
        }
    }
    
    private function getClientData(array &$data): void
    {
        if (empty($data['UA'])) {
            #Add UserAgent data
            #This is done to make the data readily available as soon as session is created and somewhat improve performance
            $data['UA'] = $this->getUA();
        }
        if (empty($data['IP'])) {
            #Add IP data
            #This is done to make the data readily available as soon as session is created and somewhat improve performance
            $data['IP'] = $this->getIP();
        }
    }
    
    #Custom function to refresh data, which needs refreshing on every session (IP for tracking, groups for access control, names for rendering, etc.)
    private function dataRefresh(array &$data): void
    {
        #Try to get the data
        try {
            $this->getClientData($data);
            #Add previous and current pages to attempt to determine if this is a page refresh or a new visit
            $data['newView'] = false;
            if (empty($data['prevPage']) && empty($data['curPage'])) {
                $data['curPage'] = HomePage::$canonical;
                $data['prevPage'] = null;
                $data['newView'] = true;
            } else {
                if ($data['curPage'] !== HomePage::$canonical) {
                    $data['prevPage'] = $data['curPage'];
                    $data['curPage'] = HomePage::$canonical;
                    $data['newView'] = true;
                }
            }
            #Check if IP is banned
            if (!empty($data['IP'])) {
                try {
                    $data['bannedIP'] = HomePage::$dbController->check('SELECT `ip` FROM `ban__ips` WHERE `ip`=:ip', [':ip' => $data['IP']]);
                } catch (\Throwable) {
                    $data['bannedIP'] = false;
                }
            }
            #Add CSRF token, if missing
            if (empty($data['CSRF'])) {
                $data['CSRF'] = Security::genToken();
            } else {
                @header('X-CSRF-Token: '.$data['CSRF']);
            }
            if (empty($data['userid'])) {
                $data['userid'] = Talks::unknownUserID;
                $data['username'] = $data['UA']['bot'] ?? null;
                $data['timezone'] = null;
                $data['groups'] = [];
                $data['activated'] = false;
                $data['deleted'] = false;
                $data['banned'] = false;
                $data['avatar'] = null;
            } else {
                $user = (new User($data['userid']))->get();
                #Assign some data to the session
                if ($user->id) {
                    $data['username'] = $user->username;
                    $data['timezone'] = $user->timezone;
                    $data['groups'] = $user->groups;
                    $data['activated'] = $user->activated;
                    $data['deleted'] = $user->deleted;
                    $data['banned'] = $user->banned;
                    $data['avatar'] = $user->currentAvatar;
                } else {
                    $data['userid'] = Talks::unknownUserID;
                    $data['username'] = (!empty($data['UA']['bot']) ? $data['UA']['bot'] : null);
                }
            }
        } catch (\Throwable) {
            $data['userid'] = Talks::unknownUserID;
            $data['username'] = (!empty($data['UA']['bot']) ? $data['UA']['bot'] : null);
        }
    }

    #Function to return IP
    private function getIP(): ?string
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
        $dd = (new DeviceDetector($_SERVER['HTTP_USER_AGENT'], ClientHints::factory($_SERVER)));
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

    private function cookieLogin(): array
    {
        $cookieName = str_replace(['.', ' '], '_', 'rememberme_'.Common::$http_host);
        #Check if cookie exists
        if (empty($_COOKIE[$cookieName])) {
            return [];
        }
        #Validate cookie
        try {
            #Decode data
            $data = json_decode($_COOKIE[$cookieName], true);
            if (empty($data['id']) || empty($data['pass'])) {
                #No expected data found
                return [];
            }
            #Cache Security object
            $data['id'] = Security::decrypt($data['id']);
            #Check DB
            if (HomePage::$dbController !== null) {
                #Get user data
                $savedData = HomePage::$dbController->selectRow('SELECT `validator`, `userid` FROM `uc__cookies` WHERE `uc__cookies`.`cookieid`=:id',
                    [':id' => $data['id']]
                );
                if (empty($savedData) || empty($savedData['validator'])) {
                    #No cookie found or no password present
                    return [];
                }
                #Validate cookie password
                if (hash('sha3-512', $data['pass']) !== $savedData['validator']) {
                    #Wrong password
                    return [];
                }
                $user = (new User($savedData['userid']));
                #Reset strikes if any
                $user->resetStrikes();
                #Update cookie
                $user->rememberMe($data['id']);
                #Get client data
                $this->getClientData($savedData);
                #Try to update client information for cookie
                try {
                    HomePage::$dbController->query('UPDATE `uc__cookies` SET `ip`=:ip, `useragent`=:useragent WHERE `cookieid`=:cookie;',
                        [
                            ':cookie' => $data['id'],
                            ':ip' => [
                                (empty($savedData['IP']) ? NULL : $savedData['IP']),
                                (empty($savedData['IP']) ? 'null' : 'string'),
                            ],
                            ':useragent' => [
                                (empty($savedData['UA']['full']) ? NULL : $savedData['UA']['full']),
                                (empty($savedData['UA']['full']) ? 'null' : 'string'),
                            ],
                        ]
                    );
                } catch (\Throwable) {
                    #Do nothing. Not critical
                }
                $savedData['cookieid'] = $data['id'];
                unset($savedData['validator']);
                return $savedData;
            } else {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }
    }

    public function destroy(string $id): bool
    {
        try {
            return HomePage::$dbController->query('DELETE FROM `uc__sessions` WHERE `sessionid`=:id', [':id' => $id]);
        } catch (\Throwable) {
            return false;
        }
    }

    public function gc(int $max_lifetime): false|int
    {
        try {
            if (HomePage::$dbController->query('DELETE FROM `uc__sessions` WHERE `time` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL :life SECOND);', [':life' => [$max_lifetime, 'int']])) {
                return HomePage::$dbController->getResult();
            } else {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }
    }

    #####################
    #\SessionIdInterface#
    #####################
    public function create_sid(): string
    {
        return session_create_id();
    }

    #########################################
    #\SessionUpdateTimestampHandlerInterface#
    #########################################
    public function validateId(string $id): bool
    {
        #Get ID
        try {
            $sessionId = HomePage::$dbController->selectValue('SELECT `sessionId` FROM `uc__sessions` WHERE `sessionId` = :id;', [':id' => $id]);
        } catch (\Throwable) {
            return false;
        }
        #Check if it was returned
        if (empty($sessionId)) {
            #No such session exists
            return false;
        }
        #Validate session id using hash_equals to mitigate timing attacks
        return hash_equals($sessionId, $id);
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        try {
            return HomePage::$dbController->query('UPDATE `uc__sessions` SET `time`= UTC_TIMESTAMP() WHERE `sessionid` = :id;', [':id' => $id]);
        } catch (\Throwable) {
            return false;
        }
    }
}
