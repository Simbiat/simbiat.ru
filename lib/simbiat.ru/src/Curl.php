<?php
declare(strict_types=1);
namespace Simbiat;

#Class for cUrl related functions. Needed more for settings' uniformity
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
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2|CURL_SSLVERSION_MAX_TLSv1_3,
        CURLOPT_DEFAULT_PROTOCOL => 'https',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        #These options are supposed to improve speed, but do nto seem to work for websites that I parse at the moment
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

    #Function to download avatar
    public function imageDownload(string $from, string $to): bool
    {
        #Download to temp
        if (@file_put_contents(sys_get_temp_dir().'/'.basename($to), @fopen($from, 'r'))) {
            #Create directory if missing
            if (!is_dir(dirname($to))) {
                #Create it recursively
                @mkdir(dirname($to), recursive: true);
            }
            #Copy to actual location
            @copy(sys_get_temp_dir().'/'.basename($to), $to);
            @unlink(sys_get_temp_dir().'/'.basename($to));
        } else {
            return false;
        }
        return is_file($to);
    }
    
    #Not exactly Curl, but plan is that it will be used for avatars, at least, that may need to be downloaded first
    #Possibly will be moved to some other more suitable class in the future
    public static function toWebP(string $image): string|false
    {
        #Check if file exists
        if (!is_file($image)) {
            return false;
        }
        #Get MIME type
        $mime = mime_content_type($image);
        if (!in_array($mime, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            return false;
        }
        #Set new name
        $newName = str_replace('.'.pathinfo($image, PATHINFO_EXTENSION), '.webp', $image);
        #Create GD object from file
        $gd = match($mime) {
            'image/avif' => imagecreatefromavif($image),
            'image/bmp' => imagecreatefrombmp($image),
            'image/gif' => imagecreatefromgif($image),
            'image/jpeg' => imagecreatefromjpeg($image),
            'image/png' => imagecreatefrompng($image),
            'image/webp' => imagecreatefromwebp($image),
        };
        #Ensure that True Color is used
        imagepalettetotruecolor($gd);
        #Enable alpha blending
        imagealphablending($gd, true);
        #Save the alpha data
        imagesavealpha($gd, true);
        #Save the file
        if (imagewebp($gd, $newName, IMG_WEBP_LOSSLESS)) {
            #Remove source image, if we did not just overwrite it
            if ($image !== $newName) {
                @unlink($image);
            }
            return $newName;
        } else {
            return false;
        }
    }
}
