<?php
declare(strict_types=1);

namespace App\Security;

use App\Enum\SystemUser;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Query;
use Simbiat\http20\IRI;
use Simbiat\StringHelpers\Decode;
use Simbiat\StringHelpers\Encode;

/**
 * Various security stuff
 */
class Security
{
    public const string EMAIL_REGEX = '/^[a-zA-Z0-9.!#$%&’*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/u';

    /**
     * Function to hash password. Used mostly as a wrapper in case of future changes
     *
     * @param string $password
     *
     * @return string
     */
    public static function passHash(#[\SensitiveParameter] string $password): string
    {
        return \password_hash($password, \PASSWORD_ARGON2ID, Config::$argon_settings);
    }

    /**
     * Function to encrypt stuff.
     *
     * **DO NOT use for permanent data storage**
     *
     * @param string $data
     *
     * @return string
     *
     * @throws \Random\RandomException
     */
    public static function encrypt(#[\SensitiveParameter] string $data): string
    {
        if (empty($data)) {
            return '';
        }
        // Generate IV
        $iv = \random_bytes(\openssl_cipher_iv_length('AES-256-GCM'));
        // This is where OpenSSL will write the tag
        $tag = '';
        // Encrypt and als get the tag
        $encrypted = \openssl_encrypt($data, 'AES-256-GCM', \hex2bin(Config::$encryption_passphrase), \OPENSSL_RAW_DATA, $iv, $tag);

        // Encrypt and prepend IV and tag
        return Encode::base64url($iv.$tag.$encrypted);
    }

    /**
     * Function to decrypt stuff
     *
     * @param string $data
     *
     * @return string
     *
     * @noinspection NoMBMultibyteAlternative
     */
    public static function decrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }
        // Decode
        $data = Decode::base64url($data);
        // Get IV
        $iv = \substr($data, 0, 12);
        // Get tag
        $tag = \substr($data, 12, 16);
        // Strip them from data
        $data = \substr($data, 28);

        return \openssl_decrypt($data, 'AES-256-GCM', \hex2bin(Config::$encryption_passphrase), \OPENSSL_RAW_DATA, $iv, $tag);
    }

    /**
     * Function to generate tokens (for example, CSRF)
     *
     * @param int $length
     *
     * @return string
     */
    public static function genToken(int $length = 32): string
    {
        try {
            $token = \bin2hex(\random_bytes($length));
        } catch (\Throwable) {
            $token = '';
        }

        return $token;
    }

    /**
     * Function to generate passphrase for encrypt and decrypt functions
     *
     * @return string
     *
     * @throws \Random\RandomException
     */
    public static function genCrypto(): string
    {
        $pass = \random_bytes(\openssl_cipher_iv_length('AES-256-GCM'));

        return \bin2hex($pass);
    }

    /**
     * Function to log actions
     *
     * @param int        $type    Action type
     * @param string     $action  Message of the action
     * @param mixed|null $extras  Extra data related to the action
     * @param int|null   $user_id User ID of the user that triggered the action
     *
     * @return bool
     */
    public static function log(int $type, string $action, mixed $extras = null, ?int $user_id = null): bool
    {
        /** @noinspection IsEmptyFunctionUsageInspection Valid case, since mixed type */
        if (
            !empty($extras)
            && !\is_scalar($extras)
        ) {
            try {
                $extras = \json_encode($extras, \JSON_PRETTY_PRINT | \JSON_INVALID_UTF8_SUBSTITUTE | \JSON_UNESCAPED_UNICODE | \JSON_PRESERVE_ZERO_FRACTION | \JSON_THROW_ON_ERROR);
            } catch (\Throwable $throwable) {
                Errors::error_log($throwable);
                $extras = null;
            }
        } else {
            $extras = null;
        }
        // Get IP
        $ip = $_SESSION['ip'] ?? null;
        // Get username
        if ($user_id === null) {
            $user_id = (int) ($_SESSION['user_id'] ?? SystemUser::Unknown->value);
        }
        // Get User Agent
        $ua = $_SESSION['useragent']['full'] ?? null;
        try {
            Query::query(
                'INSERT INTO `sys__logs` (`time`, `type`, `action`, `user_id`, `ip`, `user_agent`, `extra`) VALUES (CURRENT_TIMESTAMP(6), :type, :action, :user_id, :ip, :ua, :extras);',
                [
                    ':type' => [$type, 'int'],
                    ':action' => $action,
                    ':user_id' => [$user_id, 'int'],
                    ':ip' => [
                        ($ip ?? null),
                        ($ip === null ? 'null' : 'string'),
                    ],
                    ':ua' => [
                        ($ua ?? null),
                        ($ua === null ? 'null' : 'string'),
                    ],
                    ':extras' => [
                        ($extras ?? null),
                        ($extras === null ? 'null' : 'string'),
                    ],
                ]
            );

            return true;
        } catch (\Throwable $exception) {
            // Log to the file. Generally we do not lose much if this fails
            Errors::error_log($exception);

            return false;
        }
    }

    /**
     * Sanitize URLs and remove tracking query parameters from them
     *
     * @param string $url
     *
     * @return string
     */
    public static function sanitizeURL(string $url): string
    {
        // First, normalize the string
        $url = \Normalizer::normalize($url, \Normalizer::FORM_C);
        // Check if valid IRI
        if (!IRI::isValidIri($url, 'https')) {
            return '';
        }
        // Attempt to parse it
        $parsed_url = IRI::parseUri($url);
        // Ignore failed strings
        if (!\is_array($parsed_url)) {
            return '';
        }
        // Parse the query string into an associative array
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        \parse_str($parsed_url['query'] ?? '', $query_params);
        // Remove tracking parameters
        foreach ($query_params as $param => $value) {
            if (\in_array($param, Config::$tracking_query_parameters, true)) {
                unset($query_params[$param]);
            }
        }
        // Rebuild the query string
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        $parsed_url['query'] = IRI::rawBuildQuery($query_params);

        // Reconstruct the full URL
        return IRI::restoreUri($parsed_url);
    }

    /**
     * Wrapper for regular `session_regenerate_id`, to always include CSRF regeneration.
     *
     * @param bool $delete_old_session Whether to delete the old associated session or not.
     *
     * @return bool
     */
    public static function session_regenerate_id(bool $delete_old_session = false): bool
    {
        try {
            if (\session_status() === \PHP_SESSION_ACTIVE) {
                \session_regenerate_id($delete_old_session);
            }
            $_SESSION['csrf'] = self::genToken();
            if (!\headers_sent()) {
                \header('X-CSRF-Token: '.$_SESSION['csrf']);
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
