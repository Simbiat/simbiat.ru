<?php
declare(strict_types=1);
namespace Simbiat;

#Class for cUrl related functions. Needed more for settings' uniformity
use Simbiat\HTTP20\Common;

class Curl
{
    #cURL options
    protected array $CURL_OPTIONS = [
        CURLOPT_POST => false,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        #Allow caching and reuse of already open connections
        CURLOPT_FRESH_CONNECT => false,
        CURLOPT_FORBID_REUSE => false,
        #Let cURL determine appropriate HTTP version
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_HTTPHEADER => ['Content-type: text/html; charset=utf-8', 'Accept-Language: en'],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.134 Safari/537.36 Edg/103.0.1264.71',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2|CURL_SSLVERSION_MAX_TLSv1_3,
        CURLOPT_DEFAULT_PROTOCOL => 'https',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        #These options are supposed to improve speed, but do not seem to work for websites that I parse at the moment
        #CURLOPT_SSL_FALSESTART => true,
        #CURLOPT_TCP_FASTOPEN => true,
    ];
    #cURL Handle is static to allow reuse of single instance, if possible and needed
    public static \CurlHandle|null|false $curlHandle = null;

    public final function __construct()
    {
        #Check if cURL handle already created and create it if not
        if (!self::$curlHandle instanceof \CurlHandle) {
            self::$curlHandle = curl_init();
            if (self::$curlHandle !== false) {
                if(!curl_setopt_array(self::$curlHandle, $this->CURL_OPTIONS)) {
                    self::$curlHandle = false;
                }
            }
        }
    }

    public function getPage(string $link): string|false
    {
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        #Get page contents
        curl_setopt(self::$curlHandle, CURLOPT_HEADER, true);
        curl_setopt(self::$curlHandle, CURLOPT_URL, $link);
        #Get response
        $response = curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode !== 200) {
            return false;
        } else {
            return substr($response, curl_getinfo(self::$curlHandle, CURLINFO_HEADER_SIZE));
        }
    }
    
    public function getFile(string $link): array|false
    {
        #Generate random name. Using 64 to be consistent with sha3-512 hash
        try {
            $name = hash('sha3-512', random_bytes(64)).'.download';
        } catch (\Throwable) {
            #Use microseconds, if we somehow failed to get random value, since it''s unlikely we get more than 1 file upload at the same microsecond
            $name = microtime().'.download';
        }
        #Set temp filepath
        $filepath = sys_get_temp_dir().'/'.$name;
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        #Get file
        curl_setopt(self::$curlHandle, CURLOPT_URL, $link);
        $fp = fopen($filepath, 'wb');
        if ($fp === false) {
            return false;
        }
        curl_setopt(self::$curlHandle, CURLOPT_HEADER, false);
        curl_setopt(self::$curlHandle, CURLOPT_FILE, $fp);
        #Get response
        $response = curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        #Close file
        @fclose($fp);
        if ($response === false || $httpCode !== 200) {
            return false;
        } else {
            #Rename file to give it a proper extension
            $mime = mime_content_type($filepath);
            $newName = pathinfo($filepath, PATHINFO_FILENAME).'.'.(array_search($mime, Common::extToMime) ?? preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/ui', '$2', $link));
            rename($filepath, sys_get_temp_dir().'/'.$newName);
            $filepath = sys_get_temp_dir().'/'.$newName;
            return [
                'server_name' => $newName,
                'server_path' => sys_get_temp_dir(),
                'user_name' => preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/ui', '$1$2', basename($link)),
                'size' => filesize($filepath),
                'type' => $mime,
                'hash' => hash_file('sha3-512', $filepath),
            ];
        }
    }

    public function ifExists($remoteFile): bool
    {
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        #Initialize cUrl
        curl_setopt(self::$curlHandle, CURLOPT_NOBODY, true);
        curl_setopt(self::$curlHandle, CURLOPT_URL, $remoteFile);
        curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        curl_close(self::$curlHandle);
        #Check code
        if ($httpCode === 200) {
            return true;
        } else {
            return false;
        }
    }
}
