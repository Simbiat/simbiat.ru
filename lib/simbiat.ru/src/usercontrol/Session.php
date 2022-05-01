<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\HomePage;

class Session implements \SessionHandlerInterface, \SessionIdInterface, \SessionUpdateTimestampHandlerInterface
{
    #Attach common settings
    use Common;

    #Default lifetime for session in seconds (15 minutes)
    private int $sessionLife;

    #Cache of security object
    private ?Security $security = NULL;

    public function __construct(int $sessionLife = 2700)
    {
        #Set session name for easier identification. '__Host-' prefix signals to the browser that both the Path=/ and Secure attributes are required, so that subdomains cannot modify the session cookie.
        session_name('__Host-sess_'.preg_replace('/[^a-zA-Z0-9\-_]/', '', $_SERVER['HTTP_HOST'] ?? 'simbiat'));
        if ($sessionLife < 0) {
            $sessionLife = 2700;
        }
        $this->sessionLife = $sessionLife;
        #Cache DB controller, if not done already
        if (self::$dbController === NULL) {
            try {
                self::$dbController = HomePage::$dbController;
                $this->security = new Security;
            } catch (\Throwable) {
                #Do nothing, session will fail to be opened on `open` call
            }
        }
    }

    ##########################
    #\SessionHandlerInterface#
    ##########################
    public function open(string $path, string $name): bool
    {
        #If controller was initialized - session is ready
        if (self::$dbController === NULL) {
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
            $data = self::$dbController->selectValue('SELECT `data` FROM `uc__sessions` WHERE `sessionid` = :id AND `time` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL :life SECOND)', [':id' => $id, ':life' => [$this->sessionLife, 'int']]);
        } catch (\Throwable) {
            $data = '';
        }
        if (!empty($data)) {
            #Decrypt data
            $data = $this->security->decrypt($data);
            #Deserialize to check if UserAgent data is present
            $data = unserialize($data);
        } else {
            $data = [];
        }
        $this->IPUA($data);
        #Login through cookie if present
        $data = array_merge($data, (new Signinup)->cookieLogin());
        return serialize($data);
    }

    public function write(string $id, string $data): bool
    {
        #Deserialize to check if UserAgent data is present
        $data = unserialize($data);
        $this->IPUA($data);
        #Check if userid is set and exists and reset if it does not
        try {
            if (!empty($data['userid']) && self::$dbController->check('SELECT `userid` FROM `uc__users` WHERE `userid`=:userid', ['userid'=>[$data['userid'], 'int']])) {
                $userid = $data['userid'];
            } else {
                $userid = NULL;
            }
        } catch (\Throwable) {
            $userid = NULL;
        }
        #Cache username (to prevent reading from Session)
        if (empty($userid)) {
            $username = $data['UA']['bot'] ?? NULL;
        } else {
            $username = (!empty($data['UA']['bot']) ? $data['UA']['bot'] : ($data['username'] ?? NULL));
        }
        #Prepare empty array
        $queries = [];
        #Update SEO related tables
        if (self::$SEOTracking === true && empty($data['UA']['bot']) && $data['IP'] !== NULL) {
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
            $queries[] = [
                'INSERT INTO `seo__pageviews` SET `page`=:page, `referer`=:referer, `ip`=:ip, `os`=:os, `client`=:client ON DUPLICATE KEY UPDATE `views`=`views`+1;',
                [
                    #What page is being viewed
                    ':page' => (empty($_SERVER['REQUEST_URI']) ? 'index.php' : substr($_SERVER['REQUEST_URI'], 0, 256)),
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
            'INSERT INTO `uc__sessions` SET `sessionid`=:id, `userid`=:userid, `bot`=:bot, `ip`=:ip, `os`=:os, `client`=:client, `username`=:username, `page`=:page, `data`=:data ON DUPLICATE KEY UPDATE `time`=UTC_TIMESTAMP(), `userid`=:userid, `bot`=:bot, `ip`=:ip, `os`=:os, `client`=:client, `username`=:username, `page`=:page, `data`=:data;',
            [
                ':id' => $id,
                #Whether this is a bot
                ':bot' => [(empty($data['UA']['bot']) ? 0 : 1), 'int'],
                ':ip' => [
                    (empty($data['IP']) ? NULL : $data['IP']),
                    (empty($data['IP']) ? 'null' : 'string'),
                ],
                #Useragent details only for logged-in users for ability of review of active sessions
                ':os' => [
                        (empty($data['UA']['os']) ? NULL : $data['UA']['os']),
                        (empty($data['UA']['os']) ? 'null' : 'string'),
                    ],
                    ':client' => [
                        (empty($data['UA']['client']) ? NULL : $data['UA']['client']),
                        (empty($data['UA']['client']) ? 'null' : 'string'),
                    ],
                #Either username (if logged in) or bot name, if it's a bot
                ':username' => [
                    (empty($username) ? NULL : $username),
                    (empty($username) ? 'null' : 'string'),
                ],
                ':userid' => [
                    (empty($userid) ? NULL : $userid),
                    (empty($userid) ? 'null' : 'int'),
                ],
                #What page is being viewed
                ':page' => (empty($_SERVER['REQUEST_URI']) ? 'index.php' : substr($_SERVER['REQUEST_URI'], 0, 256)),
                #Actual session data
                ':data' => [
                    (empty($data) ? '' : $this->security->encrypt(serialize($data))),
                    'string',
                ],
            ],
        ];
        try {
            return self::$dbController->query($queries);
        } catch (\Throwable) {
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        try {
            return self::$dbController->query('DELETE FROM `uc__sessions` WHERE `sessionid`=:id', [':id' => $id]);
        } catch (\Throwable) {
            return false;
        }
    }

    public function gc(int $max_lifetime): false|int
    {
        try {
            if (self::$dbController->query('DELETE FROM `uc__sessions` WHERE `time` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL :life SECOND);', [':life' => [$max_lifetime, 'int']])) {
                return self::$dbController->getResult();
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
            $sessionId = self::$dbController->selectValue('SELECT `sessionId` FROM `uc__sessions` WHERE `sessionId` = :id;', [':id' => $id]);
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
            return self::$dbController->query('UPDATE `uc__sessions` SET `time`= UTC_TIMESTAMP() WHERE `sessionid` = :id;', [':id' => $id]);
        } catch (\Throwable) {
            return false;
        }
    }

    private function IPUA(array &$data): void
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
        #Add CSRF token, if missing
        if (empty($data['CSRF'])) {
            $data['CSRF'] = $this->security->genCSRF();
        } else {
            @header('X-CSRF-Token: '.$data['CSRF'], true);
        }
    }
}
