<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Database\Controller;
use Simbiat\HTTP20\Headers;

class Security
{
    #Attach common settings
    use Common;

    #Argon settings
    private array $argonSettings = [
        'memory_cost' => 1024,
        #Despite name, this is not time, but number of iterations
        'time_cost' => 2,
        'threads' => 2,
    ];
    #AES settings
    private array $aesSettings;

    public function __construct()
    {
        #Load Argon settings if argon.json exists
        if (is_file($GLOBALS['siteconfig']['security_cache']. 'argon.json')) {
            #Read the file
            $argon = json_decode(file_get_contents($GLOBALS['siteconfig']['security_cache']. 'argon.json'), true);
            #Update settings, if they are present and comply with minimum requirements
            if (isset($argon['memory_cost']) && $argon['memory_cost'] >= 1024) {
                $this->argonSettings['memory_cost'] = $argon['memory_cost'];
            }
            if (isset($argon['time_cost']) && $argon['time_cost'] >= 1) {
                $this->argonSettings['time_cost'] = $argon['time_cost'];
            }
            if (isset($argon['threads']) && $argon['threads'] >= 1) {
                $this->argonSettings['threads'] = $argon['threads'];
            }
        } else {
            #Generate the settings
            $this->argonSettings = (new NoDB)->argonCalc();
        }
        #Load AES settings
        if (is_file($GLOBALS['siteconfig']['security_cache']. 'aes.json')) {
            #Read the file
            $aes = json_decode(file_get_contents($GLOBALS['siteconfig']['security_cache']. 'aes.json'), true);
            if (isset($aes['passphrase'])) {
                $this->aesSettings = $aes;
            } else {
                #Generate the settings
                $this->aesSettings = (new NoDB)->genCrypto();
            }
        } else {
            #Generate the settings
            $this->aesSettings = (new NoDB)->genCrypto();
        }
    }

    #Function to validate password

    /**
     * @throws \Exception
     */
    public function passValid(int|string $id, string $password, string $hash): bool
    {
        #Cache DB controller, if not done already
        if (self::$dbController === NULL) {
            self::$dbController = new Controller;
        }
        #Validate password
        if (password_verify($password, $hash)) {
            #Check if it needs rehashing
            if (password_needs_rehash($hash, PASSWORD_ARGON2ID, $this->argonSettings)) {
                #Rehash password and reset strikes (if any)
                self::$dbController->query(
                    'UPDATE `uc__users` SET `password`=:password, `strikes`=0 WHERE `userid`=:userid;',
                    [
                        ':userid' => [strval($id), 'string'],
                        ':password' => [$this->passHash($password), 'string'],
                    ]
                );
            } else {
                #Reset strikes (if any)
                self::$dbController->query(
                    'UPDATE `uc__users` SET `strikes`=0 WHERE `userid`=:userid;',
                    [
                        ':userid' => [strval($id), 'string']
                    ]
                );
            }
            return true;
        } else {
            #Increase strike count
            self::$dbController->query(
                'UPDATE `uc__users` SET `strikes`=`strikes`+1 WHERE `userid`=:userid',
                [':userid' => [strval($id), 'string']]);
            return false;
        }
    }

    #Function to hash password. Used mostly as a wrapper in case of future changes
    public function passHash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, $this->argonSettings);
    }

    #Function to encrypt stuff
    public function encrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }
        #Generate IV
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-GCM'));
        #This is where tag will be written by OpenSSL
        $tag = '';
        #Ecnrypt and als get the tag
        $encrypted = openssl_encrypt($data, 'AES-256-GCM', hex2bin($this->aesSettings['passphrase']), OPENSSL_RAW_DATA, $iv, $tag);
        #Ecnrypt and prepend IV and tag
        return base64_encode($iv.$tag.$encrypted);
    }

    #Function to decrypt stuff
    public function decrypt(string $data): string
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
        return openssl_decrypt($data, 'AES-256-GCM', hex2bin($this->aesSettings['passphrase']), OPENSSL_RAW_DATA, $iv, $tag);
    }

    #Function to help protect against CSRF. Suggested to use for forms or APIs. Needs to be used before any writes to $_SESSION
    public function antiCSRF(array $allowOrigins = [], bool $originRequired = false, bool $exit = true): bool
    {
        #Get CSRF token
        $token = $_POST['X-CSRFToken'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'];
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
                    #Check if HTTP Origin is among allowed ones, if we want restrict them.
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
        $this->log('CSRF', 'CSRF attack detected', [
            'reason' => $reason,
            'page' => @$_SERVER['REQUEST_URI'],
            'forwarded' => @$_SERVER['HTTP_X_FORWARDED_HOST'],
            'origin' => @$_SERVER['HTTP_ORIGIN'],
            'referer' => @$_SERVER['HTTP_REFERER'],
        ]);
        #Send 403 error code in header, with option to force close connection
        (new Headers)->clientReturn('403', $exit);
        return false;
    }

    #Function to generate CSRF token

    /**
     * @throws \Exception
     */
    public function genCSRF(): string
    {
        $token = bin2hex(random_bytes(32));
        @header('X-CSRF-Token: '.$token, true);
        return $token;
    }
}
