<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol;

use Simbiat\Database\Query;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;

/**
 * Implement session handling using database
 */
class Session implements \SessionHandlerInterface, \SessionIdInterface, \SessionUpdateTimestampHandlerInterface
{
    /**
     * Constructor for the class
     * @param int $sessionLife Default lifetime for session in seconds (5 minutes)
     */
    public function __construct(private int $sessionLife = 300)
    {
        if ($this->sessionLife < 0) {
            $this->sessionLife = 300;
        }
        if (!headers_sent()) {
            #Set the session name for easier identification. '__Host-' prefix signals to the browser that both the Path=/ and Secure attributes are required, so that subdomains cannot modify the session cookie.
            session_name('__Host-session_'.preg_replace('/[^a-zA-Z\d\-_]/', '', Config::$http_host ?? 'simbiat'));
            #Set session cookie parameters
            ini_set('session.cookie_lifetime', $this->sessionLife);
            ini_set('session.cookie_secure', Config::$cookieSettings['secure']);
            ini_set('session.cookie_httponly', Config::$cookieSettings['httponly']);
            ini_set('session.cookie_path', Config::$cookieSettings['path']);
            ini_set('session.cookie_samesite', Config::$cookieSettings['samesite']);
            ini_set('session.use_strict_mode', true);
            ini_set('session.use_only_cookies', true);
        }
    }
    
    ##########################
    #\SessionHandlerInterface#
    ##########################
    /**
     * Initialize session
     * @link  https://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool <p>
     *                     The return value (usually TRUE on success, FALSE on failure).
     *                     Note this value is returned internally to PHP for processing.
     *                     </p>
     * @since 5.4
     */
    public function open(string $path, string $name): bool
    {
        #If the controller was initialized - session is ready
        return Query::$dbh !== null;
    }
    
    /**
     * Close the session
     * @link  https://php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4
     */
    public function close(): bool
    {
        #No need to do anything at this point
        return true;
    }
    
    /**
     * Read session data
     *
     * @link  https://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $id The session id to read data for.
     *
     * @return string <p>
     *                   Returns an encoded string of the read data.
     * If nothing was read, it must return false.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4
     */
    public function read(string $id): string
    {
        #Get session data
        try {
            $data = Query::query('SELECT `data` FROM `uc__sessions` WHERE `sessionid` = :id AND `time` >= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL :life SECOND)', [':id' => $id, ':life' => [$this->sessionLife, 'int']], return: 'value');
        } catch (\Throwable) {
            $data = '';
        }
        if (!empty($data)) {
            #Decrypt data
            $data = Security::decrypt($data);
            #Deserialize to check if UserAgent data is present
            $data = unserialize($data, [false]);
        } else {
            $data = [];
        }
        #Login through cookie if it is present
        $data = array_merge($data, $this->cookieLogin());
        $this->dataRefresh($data);
        return serialize($data);
    }
    
    /**
     * Write session data
     * @link  https://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $id   The session id.
     * @param string $data <p>
     *                     The encoded session data. This data is the
     *                     result of the PHP internally encoding
     *                     the $_SESSION superglobal to a serialized
     *                     string and passing it as this parameter.
     *                     Please note sessions use an alternative serialization method.
     *                     </p>
     * @return bool <p>
     *                     The return value (usually TRUE on success, FALSE on failure).
     *                     Note this value is returned internally to PHP for processing.
     *                     </p>
     * @since 5.4
     */
    public function write(string $id, string $data): bool
    {
        #Deserialize to check if UserAgent data is present
        $data = unserialize($data, [false]);
        #Prepare an empty array
        $queries = [];
        #Update SEO-related tables if this was determined to be a new page view
        if (empty($data['UA']['bot'])) {
            if (!empty($data['IP']) && $data['newView']) {
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
                $page = mb_substr(preg_replace('/^.*:\/\/[^\/]*\//u', '', Config::$canonical), 0, 256, 'UTF-8');
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
                            (empty($_SERVER['HTTP_REFERER']) ? '' : mb_substr($_SERVER['HTTP_REFERER'], 0, 256, 'UTF-8')),
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
                'INSERT INTO `uc__sessions` SET `sessionid`=:id, `cookieid`=:cookieid, `userid`=:userid, `ip`=:ip, `useragent`=:useragent, `page`=:page, `data`=:data ON DUPLICATE KEY UPDATE `time`=CURRENT_TIMESTAMP(), `userid`=:userid, `ip`=:ip, `useragent`=:useragent, `page`=:page, `data`=:data;',
                [
                    ':id' => $id,
                    #Whether a cookie is associated with this session
                    ':cookieid' => [
                        (empty($data['cookieid']) ? NULL : $data['cookieid']),
                        (empty($data['cookieid']) ? 'null' : 'string'),
                    ],
                    ':ip' => [
                        (empty($data['IP']) ? NULL : $data['IP']),
                        (empty($data['IP']) ? 'null' : 'string'),
                    ],
                    #Useragent details only for logged-in users for the ability to review active sessions
                    ':useragent' => [
                        (empty($data['UA']['full']) ? NULL : $data['UA']['full']),
                        (empty($data['UA']['full']) ? 'null' : 'string'),
                    ],
                    ':userid' => [$data['userid'], 'int'],
                    #What page is being viewed
                    ':page' => (empty($_SERVER['REQUEST_URI']) ? 'index.php' : mb_substr($_SERVER['REQUEST_URI'], 0, 256, 'UTF-8')),
                    #Actual session data
                    ':data' => [
                        (empty($data) ? '' : Security::encrypt(serialize($data))),
                        'string',
                    ],
                ],
            ];
            #Try to update client information for cookie
            if (!empty($data['cookieid'])) {
                $queries[] = [
                    'UPDATE `uc__cookies` SET `ip`=:ip, `useragent`=:useragent WHERE `cookieid`=:cookie;',
                    [
                        ':cookie' => $data['cookieid'],
                        ':ip' => [
                            (empty($data['IP']) ? NULL : $data['IP']),
                            (empty($data['IP']) ? 'null' : 'string'),
                        ],
                        ':useragent' => [
                            (empty($data['UA']['full']) ? NULL : $data['UA']['full']),
                            (empty($data['UA']['full']) ? 'null' : 'string'),
                        ],
                    ]
                ];
            }
        }
        try {
            if (!empty($queries)) {
                return Query::query($queries);
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, $queries);
            return false;
        }
    }
    
    /**
     * Custom function to refresh data, which needs refreshing on every session (IP for tracking, groups for access control, names for rendering, etc.)
     * @param array $data Main array with the data
     *
     * @return void
     */
    private function dataRefresh(array &$data): void
    {
        #Add UserAgent data
        $data['UA'] = Security::getUA();
        #Add IP data
        $this->getIP($data);
        #Add previous and current pages to attempt to determine if this is a page refresh or a new visit
        $data['newView'] = false;
        if (empty($data['prevPage']) && empty($data['curPage'])) {
            $data['curPage'] = Config::$canonical;
            $data['prevPage'] = null;
            $data['newView'] = true;
        } elseif ($data['curPage'] !== Config::$canonical) {
            $data['prevPage'] = $data['curPage'];
            $data['curPage'] = Config::$canonical;
            $data['newView'] = true;
        }
        try {
            #Check if IP is banned
            if (!empty($data['IP'])) {
                try {
                    $data['bannedIP'] = Query::query('SELECT `ip` FROM `sys__bad_ips` WHERE `ip`=:ip', [':ip' => $data['IP']], return: 'check');
                } catch (\Throwable) {
                    $data['bannedIP'] = false;
                }
            }
            #Add CSRF token, if missing
            if (empty($data['CSRF'])) {
                $data['CSRF'] = Security::genToken();
            } elseif (!headers_sent()) {
                header('X-CSRF-Token: '.$data['CSRF']);
            }
            if (empty($data['userid'])) {
                $data['userid'] = Config::userIDs['Unknown user'];
                $data['username'] = $data['UA']['bot'] ?? null;
                $data['timezone'] = null;
                $data['groups'] = [];
                $data['permissions'] = ['viewPosts', 'viewBic', 'viewFF'];
                $data['activated'] = false;
                $data['avatar'] = null;
            } else {
                $user = new User($data['userid'])->get();
                #Assign some data to the session
                if ($user->id) {
                    $data['username'] = $user->username;
                    $data['timezone'] = $user->timezone;
                    $data['groups'] = $user->groups;
                    $data['permissions'] = $user->permissions;
                    $data['activated'] = $user->activated;
                    $data['avatar'] = $user->currentAvatar;
                    $data['sections'] = $user->sections;
                } else {
                    $data['userid'] = Config::userIDs['Unknown user'];
                    $data['username'] = (!empty($data['UA']['bot']) ? $data['UA']['bot'] : null);
                }
            }
        } catch (\Throwable) {
            $data['userid'] = Config::userIDs['Unknown user'];
            $data['username'] = (!empty($data['UA']['bot']) ? $data['UA']['bot'] : null);
        }
    }
    
    /**
     * Function to return IP, country and city
     */
    private function getIP(array &$data): void
    {
        $ip = null;
        #Get real visitor IP if behind CloudFlare network
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        #Check if behind proxy
        $forwarded = $_SERVER['HTTP_X_FORWARDED'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_FORWARDED'] ?? $_SERVER['HTTP_FORWARDED_FOR'] ?? '';
        if (!empty($forwarded)) {
            #Get a list of IPs that do validate as proper IP
            $ips = array_filter(array_map('trim', explode(',', $forwarded)), static function ($value) {
                return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
            });
            #Check if any are left
            if (!empty($ips)) {
                #Get the right-most IP
                $ip = array_pop($ips);
            }
        }
        #Check if REMOTE_ADDR is set (it's more appropriate and secure to use it)
        if (empty($ip) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
        }
        #Check if Client-IP is set. Can be easily spoofed, but it's not like we have a choice at this moment
        if (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
        }
        $data['IP'] = $ip ?? null;
    }
    
    /**
     * Attempt to log in using a cookie
     * @return array
     */
    private function cookieLogin(): array
    {
        $cookieName = str_replace(['.', ' '], '_', 'rememberme_'.Config::$http_host);
        if (!\is_string($cookieName)) {
            return [];
        }
        #Check if a cookie exists
        if (empty($_COOKIE[$cookieName])) {
            return [];
        }
        #Validate cookie
        try {
            #Decode data
            $data = json_decode($_COOKIE[$cookieName], true, flags: JSON_THROW_ON_ERROR);
            if (empty($data['cookieid']) || empty($data['pass'])) {
                #No expected data found
                return [];
            }
            #Cache Security object
            $data['cookieid'] = Security::decrypt($data['cookieid']);
            $data['pass'] = Security::decrypt($data['pass']);
            #Get user data
            $savedData = Query::query('SELECT `validator`, `userid` FROM `uc__cookies` WHERE `uc__cookies`.`cookieid`=:id',
                [':id' => $data['cookieid']], return: 'row'
            );
            if (empty($savedData) || empty($savedData['validator'])) {
                #No cookie found or no password present
                return [];
            }
            #Validate cookie password
            if (!password_verify($data['pass'], $savedData['validator'])) {
                #Wrong password
                return [];
            }
            $user = (new User($savedData['userid']));
            #Reset strikes if any
            $user->resetStrikes();
            #Update cookie
            $user->rememberMe($data['cookieid']);
            $savedData['cookieid'] = $data['cookieid'];
            unset($savedData['validator']);
            return $savedData;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return [];
        }
    }
    
    /**
     * Destroy a session
     * @link  https://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $id The session ID being destroyed.
     * @return bool <p>
     *                   The return value (usually TRUE on success, FALSE on failure).
     *                   Note this value is returned internally to PHP for processing.
     *                   </p>
     * @since 5.4
     */
    public function destroy(string $id): bool
    {
        try {
            return Query::query('DELETE FROM `uc__sessions` WHERE `sessionid`=:id', [':id' => $id]);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
    
    /**
     * Cleanup old sessions
     * @link  https://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $max_lifetime <p>
     *                          Sessions that have not updated for
     *                          the last max_lifetime seconds will be removed.
     *                          </p>
     * @return int|false <p>
     *                          Returns the number of deleted sessions on success, or false on failure. Prior to PHP version 7.1, the function returned true in case of success.
     *                          Note this value is returned internally to PHP for processing.
     *                          </p>
     * @since 5.4
     */
    public function gc(int $max_lifetime = 300): false|int
    {
        try {
            return Query::query('DELETE FROM `uc__sessions` WHERE `time` <= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL :life SECOND) OR `userid` IN ('.Config::userIDs['System user'].', '.Config::userIDs['Deleted user'].');', [':life' => [$max_lifetime, 'int']], return: 'affected');
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
    
    #####################
    #\SessionIdInterface#
    #####################
    /**
     * Create session ID
     * @link https://php.net/manual/en/sessionidinterface.create-sid.php
     * @return string <p>
     * The new session ID. Notes that this value is returned internally to PHP for processing.
     * </p>
     */
    public function create_sid(): string
    {
        return session_create_id();
    }
    
    #########################################
    #\SessionUpdateTimestampHandlerInterface#
    #########################################
    /**
     * Validate session id
     * @link https://www.php.net/manual/sessionupdatetimestamphandlerinterface.validateid
     * @param string $id The session id
     * @return bool <p>
     *                   Note this value is returned internally to PHP for processing.
     *                   </p>
     */
    public function validateId(string $id): bool
    {
        #Get ID
        try {
            $sessionId = Query::query('SELECT `sessionId` FROM `uc__sessions` WHERE `sessionId` = :id;', [':id' => $id], return: 'value');
        } catch (\Throwable $e) {
            Errors::error_log($e);
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
    
    /**
     * Update the timestamp of a session
     * @link https://www.php.net/manual/sessionupdatetimestamphandlerinterface.updatetimestamp.php
     * @param string $id   The session id
     * @param string $data <p>
     *                     The encoded session data. This data is the
     *                     result of the PHP internally encoding
     *                     the $_SESSION superglobal to a serialized
     *                     string and passing it as this parameter.
     *                     Please note sessions use an alternative serialization method.
     *                     </p>
     * @return bool
     */
    public function updateTimestamp(string $id, string $data): bool
    {
        try {
            return Query::query('UPDATE `uc__sessions` SET `time`= CURRENT_TIMESTAMP() WHERE `sessionid` = :id;', [':id' => $id]);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
}