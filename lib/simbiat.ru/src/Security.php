<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;
use Simbiat\HTTP20\Headers;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Security
{

    #Static sanitizer config for a little bit performance
    public static ?HtmlSanitizerConfig $sanitizerConfig = null;

    #Function to validate password
    public static function passValid(int|string $id, string $password, string $hash): bool
    {
        #Validate password
        try {
            if (password_verify($password, $hash)) {
                #Check if it needs rehashing
                if (password_needs_rehash($hash, PASSWORD_ARGON2ID, Config\Security::$argonSettings)) {
                    #Rehash password and reset strikes (if any)
                    self::passChange($id, $password);
                } else {
                    #Reset strikes (if any)
                    self::resetStrikes($id);
                }
                return true;
            } else {
                #Increase strike count
                HomePage::$dbController->query(
                    'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `userid`=:userid',
                    [':userid' => [strval($id), 'string']]);
                return false;
            }
        } catch (\Throwable) {
            return false;
        }
    }

    public static function resetStrikes(int|string $id): bool
    {
        return HomePage::$dbController->query(
            'UPDATE `uc__users` SET `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [strval($id), 'string']
            ]
        );
    }

    #Function to hash password. Used mostly as a wrapper in case of future changes
    public static function passHash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, Config\Security::$argonSettings);
    }

    #Function to change the password
    public static function passChange(int|string $id, string $password): bool
    {
        return HomePage::$dbController->query(
            'UPDATE `uc__users` SET `password`=:password, `strikes`=0, `pw_reset`=NULL WHERE `userid`=:userid;',
            [
                ':userid' => [strval($id), 'string'],
                ':password' => [Security::passHash($password), 'string'],
            ]
        );
    }

    #Function to encrypt stuff
    public static function encrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }
        #Generate IV
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-GCM'));
        #This is where tag will be written by OpenSSL
        $tag = '';
        #Ecnrypt and als get the tag
        $encrypted = openssl_encrypt($data, 'AES-256-GCM', hex2bin(Config\Security::$aesSettings['passphrase']), OPENSSL_RAW_DATA, $iv, $tag);
        #Ecnrypt and prepend IV and tag
        return base64_encode($iv.$tag.$encrypted);
    }

    #Function to decrypt stuff
    public static function decrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }
        #Decode
        $data = base64_decode($data);
        #Get IV
        $iv = substr($data, 0, 12);
        #Get tag
        $tag = substr($data, 12, 16);
        #Strip them from data
        $data = substr($data, 28);
        return openssl_decrypt($data, 'AES-256-GCM', hex2bin(Config\Security::$aesSettings['passphrase']), OPENSSL_RAW_DATA, $iv, $tag);
    }

    #Function to help protect against CSRF. Suggested using for forms or APIs. Needs to be used before any writes to $_SESSION
    public static function antiCSRF(array $allowOrigins = [], bool $originRequired = false, bool $exit = true): bool
    {
        #Get CSRF token
        $token = $_POST['X-CSRF-Token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null;
        #Get origin
        #In some cases Origin can be empty. In case of forms we can try checking Referer instead.
        #In case of proxy is being used we should try taking the data from X-Forwarded-Host.
        $origin = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? NULL;
        #Check if token is provided
        if (!empty($token)) {
            #Check if CSRF token is present in session data
            if (!empty($_SESSION['CSRF'])) {
                #Check if they match. hash_equals helps mitigate timing attacks
                if (hash_equals($_SESSION['CSRF'], $token) === true) {
                    #Check if HTTP Origin is among allowed ones, if we want to restrict them.
                    #Note that this will be applied to forms or APIs you want to restrict. For global restriction use \Simbiat\HTTP20\headers->security()
                    if (empty($allowOrigins) ||
                        #If origins are limited
                        (
                            #Check if origin is not present and is enforced
                            (empty($origin) && $originRequired === false) ||
                            #Check if origin is present
                            (!empty($origin) &&
                                #Check if it's a valid origin and is allowed
                                (preg_match('/'. Headers::originRegex.'/i', $origin) === 1 || in_array($origin, $allowOrigins))
                            )
                        )
                    ) {
                        #All checks passed
                        return true;
                    } else {
                        $reason = 'Bad origin';
                    }
                } else {
                    $reason = 'Different hashes';
                }
            } else {
                $reason = 'No token in session';
            }
        } else {
            $reason = 'No token from client';
        }
        #Log attack details. Suppressing errors, so that values will be turned into NULLs if they are not set
        self::log('CSRF', 'CSRF attack detected', [
            'reason' => $reason,
            'page' => @$_SERVER['REQUEST_URI'],
            'forwarded' => @$_SERVER['HTTP_X_FORWARDED_HOST'],
            'origin' => @$_SERVER['HTTP_ORIGIN'],
            'referer' => @$_SERVER['HTTP_REFERER'],
        ]);
        #Send 403 error code in header, with option to force close connection
        if (!HomePage::$staleReturn) {
            HomePage::$headers->clientReturn('403', $exit);
        }
        return false;
    }

    #Function to generate CSRF token
    public static function genCSRF(): string
    {
        try {
            $token = bin2hex(random_bytes(32));
        } catch (\Throwable) {
            $token = '';
        }
        @header('X-CSRF-Token: '.$token);
        return $token;
    }

    public static function sanitizeHTML(string $string, bool $head = false): string
    {
        #Check if config has been created already
        if (self::$sanitizerConfig) {
            $config = self::$sanitizerConfig;
        } else {
            $config = (new HtmlSanitizerConfig())->allowSafeElements()->allowRelativeLinks()->allowRelativeMedias()->forceHttpsUrls()->allowLinkSchemes(['https', 'mailto'])->allowMediaSchemes(['https']);
            #Block some extra elements
            foreach (['aside', 'basefont', 'body', 'font', 'footer', 'form', 'header', 'hgroup', 'html', 'input', 'main', 'nav', 'option', 'ruby', 'select', 'selectmenu', 'template', 'textarea',] as $element) {
                #Need to update the original, because clone is returned, instead of the same instance.
                $config = $config->blockElement($element);
            }
            #Allow class attribute
            $config = $config->allowAttribute('class', '*');
            #Save config to static for future reuse
            self::$sanitizerConfig = $config;
        }
        #Allow some property attributes for meta tags
        if ($head) {
            $config = $config->allowAttribute('property', 'meta');
        }
        $sanitizer = new HtmlSanitizer($config);
        #Remove excessive new lines
        $string = preg_replace('/(^(<br \/>\s*)+)|((<br \/>\s*)+$)/mi', '', preg_replace('/(\s*<br \/>\s*){5,}/mi', '<br>', $string));
        if ($head) {
            return $sanitizer->sanitizeFor('head', $string);
        } else {
            return $sanitizer->sanitize($string);
        }
    }

    #Function to calculate optimal parameters for Argon2 hashing (password used is just a random test one) or return existing ones.
    #Clarification for whoever reads this: while it is recommended to first allocate as much memory as possible and then increase the number of iterations, if this logic is applied to a high-load web-server it will become highly likely to get the memory exhaustion during concurrent runs of the validation. Thus, this automated function first calculates the number of iterations and then memory.
    #This does not necessarily mean reduction of security, but if you feel unsafe, adjust the setting file generated by this function to settings that work better for you. Keep in mind, that amount of memory is recommended to be a value of power of 2 (1024 is the minimum one).
    public static function argonCalc(bool $forceRefresh = false): array
    {
        #Load Argon settings if argon.json exists
        if (is_file(Config\Common::$securityCache. 'argon.json') && !$forceRefresh) {
            #Read the file
            $argon = json_decode(file_get_contents(Config\Common::$securityCache. 'argon.json'), true);
            if (is_array($argon)) {
                #Update settings, if they are present and comply with minimum requirements
                if (!isset($argon['memory_cost']) || $argon['memory_cost'] < 1024) {
                    $argon['memory_cost'] = 1024;
                }
                if (!isset($argon['time_cost']) || $argon['time_cost'] < 1) {
                    $argon['time_cost'] = 1;
                }
                if (!isset($argon['threads']) || $argon['threads'] < 1) {
                    $argon['threads'] = 1;
                }
                return $argon;
            }
        }
        #Create directory if missing
        if (!is_dir(Common::$securityCache)) {
            mkdir(Common::$securityCache);
        }
        #Calculate number of available threads
        $threads = Helpers::countCores()*2;
        #Calculate iterations
        $iterations = 0;
        do {
            $iterations++;
            $start = microtime(true);
            password_hash('rel@t!velyl0ngte$t5tr1ng', PASSWORD_ARGON2ID, ['threads' => $threads, 'time_cost' => $iterations]);
            $end = microtime(true);
        } while (($end - $start) < 1.0);
        #Calculate memory. We start from power = 9, because Argon supports minimum value of 1024 (power = 10)
        $power = 9;
        do {
            $power++;
            $memory = 2**$power;
            $start = microtime(true);
            password_hash('rel@t!velyl0ngte$t5tr1ng', PASSWORD_ARGON2ID, ['threads' => $threads, 'time_cost' => $iterations, 'memory_cost' => $memory]);
            $end = microtime(true);
        } while (($end - $start) < 1.0);
        $argonSettings = ['threads' => $threads, 'time_cost' => $iterations, 'memory_cost' => $memory];
        #Write config file
        file_put_contents(Common::$securityCache.'argon.json', json_encode($argonSettings, JSON_PRETTY_PRINT));
        return $argonSettings;
    }

    #Function to generate passphrase for encrypt and decrypt functions
    public static function genCrypto(bool $forceRefresh = false): array
    {
        if (is_file(Config\Common::$securityCache. 'aes.json') && !$forceRefresh) {
            $aes = json_decode(file_get_contents(Config\Common::$securityCache.'aes.json'), true);
            if (is_array($aes)) {
                if (isset($aes['passphrase'])) {
                    return $aes;
                }
            }
        }
        #Create directory if missing
        if (!is_dir(Common::$securityCache)) {
            mkdir(Common::$securityCache);
        }
        $passphrase = bin2hex(openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-GCM')));
        #Using array, in case some other settings will be required in the future
        $cryptoSettings = ['passphrase' => $passphrase];
        #Write config file
        file_put_contents(Common::$securityCache. 'aes.json', json_encode($cryptoSettings, JSON_PRETTY_PRINT));
        return $cryptoSettings;
    }

    #Function to log actions
    public static function log(string $type, string $action, mixed $extras = NULL): bool
    {
        if (!empty($extras)) {
            $extras = json_encode($extras, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        }
        #Get IP
        $ip = $_SESSION['IP'] ?? null;
        #Get username
        $userid = $_SESSION['userid'] ?? null;
        #Get User Agent
        $ua = $_SESSION['UA']['full'] ?? null;
        try {
            HomePage::$dbController->query(
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
        } catch (\Throwable $exception) {
            #Just log to file. Generally we do not lose much if this fails
            Errors::error_log($exception);
            return false;
        }
    }

    #Setting cookie for remembering user
    public static function rememberMe(string $id = '', null|string|int $userid = null): void
    {
        try {
            #Generate cookie ID
            if (empty($id)) {
                $id = bin2hex(random_bytes(64));
            }
            #Generate cookie password
            $pass = bin2hex(random_bytes(128));
            #Write cookie data to DB
            if (HomePage::$dbController === null) {
                #If we can't write to DB for some reason - do not share any data with client
                return;
            }
            if (HomePage::$dbController !== null && (!empty($_SESSION['userid']) || !empty($userid))) {
                HomePage::$dbController->query('INSERT INTO `uc__cookies` (`cookieid`, `validator`, `userid`) VALUES (:cookie, :pass, :id) ON DUPLICATE KEY UPDATE `validator`=:pass, `time`=CURRENT_TIMESTAMP();',
                    [
                        ':cookie' => $id,
                        ':pass' => hash('sha3-512', $pass),
                        ':id' => $userid ?? [$_SESSION['userid'], 'int'],
                    ]
                );
            } else {
                return;
            }
            #Set options
            $options = ['expires' => time()+60*60*24*30, 'path' => '/', 'domain' => Common::$http_host, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict'];
            #Set cookie value
            $value = json_encode(['id' => Security::encrypt($id), 'pass'=> $pass],JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
            setcookie('rememberme_'.Common::$http_host, $value, $options);
        } catch (\Throwable) {
            #Do nothing, since not critical
        }
    }
}
