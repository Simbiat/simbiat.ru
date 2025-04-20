<?php
declare(strict_types = 1);

namespace Simbiat\Website;

#Class for cUrl related functions. Needed more for settings' uniformity
use Simbiat\http20\Common;
use Simbiat\http20\Sharing;

use function in_array;

/**
 * Common Curl-related functions
 */
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
        CURLOPT_HTTPHEADER => [],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 Edg/115.0.1901.200',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2 | CURL_SSLVERSION_MAX_TLSv1_3,
        CURLOPT_DEFAULT_PROTOCOL => 'https',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        #These options are supposed to improve speed, but do not seem to work for websites that I parse at the moment
        #CURLOPT_SSL_FALSESTART => true,
        #CURLOPT_TCP_FASTOPEN => true,
    ];
    private static array $headers = [
        'Content-type: text/html; charset=utf-8',
        'Accept-Language: en',
        #Sec-Fetch-* headers
        #Using `none` because we are requesting data from backend, so technically not a cross-site request
        'Sec-Fetch-Site: none',
        'Sec-Fetch-Mode: cors',
    ];
    #cURL Handle is static to allow reuse of a single instance, if possible and needed
    public static \CurlHandle|null|false $curlHandle = null;
    #Allowed MIME types
    public const array allowedMime = [
        #For now only images
        'image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'
    ];
    
    final public function __construct()
    {
        #Check if cURL handle already created and create it if not
        if (!self::$curlHandle instanceof \CurlHandle) {
            self::$curlHandle = curl_init();
            if (self::$curlHandle !== false && !curl_setopt_array(self::$curlHandle, $this->CURL_OPTIONS) && !curl_setopt(self::$curlHandle, CURLOPT_HTTPHEADER, self::$headers)) {
                #Set default headers
                self::$curlHandle = false;
            }
        }
    }
    
    /**
     * Get page content
     * @param string $link
     *
     * @return string|false|int
     */
    public function getPage(string $link): string|false|int
    {
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        #Get page contents
        curl_setopt(self::$curlHandle, CURLOPT_HEADER, true);
        #Directing output to a temporary file, instead of STDOUT, because I've witnessed edge cases, where due to an error (or even a notice) the output is sent to the browser directly even with CURLOPT_RETURNTRANSFER set to true
        curl_setopt(self::$curlHandle, CURLOPT_FILE, tmpfile());
        #For some reason, if we set the CURLOPT_FILE, CURLOPT_RETURNTRANSFER gets reset to false
        curl_setopt(self::$curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$curlHandle, CURLOPT_URL, $link);
        #Get response
        $response = curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        if ($response === false) {
            return false;
        }
        if ($httpCode !== 200) {
            return $httpCode;
        }
        return mb_substr($response, curl_getinfo(self::$curlHandle, CURLINFO_HEADER_SIZE), encoding: 'UTF-8');
    }
    
    /**
     * Download a file
     * @param string $link
     *
     * @return array|false
     */
    public function getFile(string $link): array|false
    {
        #Set the temp filepath
        $filepath = tempnam(sys_get_temp_dir(), 'download');
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        #Get a file
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
        }
        #Rename the file to give it a proper extension
        $mime = mime_content_type($filepath);
        $newName = pathinfo($filepath, PATHINFO_FILENAME).'.'.(array_search($mime, Common::extToMime, true) ?? preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/u', '$2', $link));
        rename($filepath, sys_get_temp_dir().'/'.$newName);
        $filepath = sys_get_temp_dir().'/'.$newName;
        return [
            'server_name' => $newName,
            'server_path' => sys_get_temp_dir(),
            'user_name' => preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/u', '$1$2', basename($link)),
            'size' => filesize($filepath),
            'type' => $mime,
            'hash' => hash_file('sha3-512', $filepath),
        ];
    }
    
    /**
     * POST something
     * @param string $link
     * @param mixed  $payload
     *
     * @return bool
     */
    public function post(string $link, mixed $payload): bool
    {
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        curl_setopt(self::$curlHandle, CURLOPT_POSTFIELDS, $payload);
        curl_setopt(self::$curlHandle, CURLOPT_URL, $link);
        #Get response
        $response = curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        return !($response === false || !in_array($httpCode, [200, 201, 202, 203, 204, 205, 206, 207, 208, 226], true));
    }
    
    /**
     * POST something as a JSON
     * @param string $link
     * @param mixed  $payload
     *
     * @return bool
     */
    public function postJson(string $link, mixed $payload): bool
    {
        if (!self::$curlHandle instanceof \CurlHandle) {
            return false;
        }
        $this->removeHeader('Content-type: text/html; charset=utf-8');
        $this->addHeader('Content-Type: application/json');
        $result = $this->post($link, $payload);
        $this->removeHeader('Content-Type: application/json');
        $this->addHeader('Content-type: text/html; charset=utf-8');
        return $result;
    }
    
    /**
     * Add header to CURL
     * @param string $header
     *
     * @return $this
     */
    public function addHeader(string $header): self
    {
        #Check if the header is already present
        if (!in_array(mb_strtolower($header, 'UTF-8'), array_map('strtolower', self::$headers), true)) {
            #Add it, if not
            self::$headers[] = $header;
            curl_setopt(self::$curlHandle, CURLOPT_HTTPHEADER, self::$headers);
        }
        return $this;
    }
    
    /**
     * Remove header from CURL
     * @param string $header
     *
     * @return $this
     */
    public function removeHeader(string $header): self
    {
        #Check if the header is already present
        $key = array_search(mb_strtolower($header, 'UTF-8'), array_map('strtolower', self::$headers), true);
        if ($key !== false) {
            #Remove it, if yes
            unset(self::$headers[$key]);
            curl_setopt(self::$curlHandle, CURLOPT_HTTPHEADER, self::$headers);
        }
        return $this;
    }
    
    /**
     * Change CURL settings
     * @param int   $option Setting to change
     * @param mixed $value  Value to set
     *
     * @return $this
     */
    public function changeSetting(int $option, mixed $value): self
    {
        curl_setopt(self::$curlHandle, $option, $value);
        return $this;
    }
    
    /**
     * Check if a remote file exists
     * @param $remoteFile
     *
     * @return bool
     */
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
        return $httpCode === 200;
    }
    
    /**
     * Function to process file uploads either through POST/PUT or by using a provided link
     * @param string $link       URL to process, if we are to download a remote file
     * @param bool   $onlyImages Flag to indicate that only images are allowed
     * @param bool   $toWebp     Whether to convert images to WEBP (if possible)
     *
     * @return array
     */
    public function upload(string $link = '', bool $onlyImages = false, bool $toWebp = true): array
    {
        try {
            #Check DB
            if (empty(Config::$dbController)) {
                return ['http_error' => 503, 'reason' => 'Database unavailable'];
            }
            Security::log('File upload', 'Attempted to upload file', ['$_FILES' => $_FILES, 'link' => $link], $_SESSION['userid'] ?? Config::userIDs['System user']);
            if (!empty($link)) {
                $upload = $this->getFile($link);
                if ($upload === false) {
                    return ['http_error' => 500, 'reason' => 'Failed to download remote file'];
                }
            } else {
                $upload = Sharing::upload(Config::$uploaded, exit: false);
                if (!\is_array($upload) || empty($upload[0]['server_name'])) {
                    return ['http_error' => $upload, 'reason' => match ($upload) {
                        405 => 'Unsupported method',
                        415 => 'Unsupported file format',
                        501 => 'Uploads are disabled',
                        507 => 'Not enough space',
                        400 => 'Empty request or file',
                        413 => 'Too large or too many files',
                        409, 403 => 'Failed to write file',
                        411 => 'Length required',
                        default => 'Failed to upload the file'.$upload,
                    }];
                }
                #If $upload had more than 1 file - remove all except the 1st one
                if (\count($upload) > 1) {
                    foreach ($upload as $key => $file) {
                        if ($key !== 0) {
                            @unlink($file['server_path'].'/'.$file['server_name']);
                        }
                    }
                }
                $upload = $upload[0];
            }
            #Check if a file is one of the allowed types
            if (!in_array($upload['type'], self::allowedMime, true)) {
                @unlink($upload['server_path'].'/'.$upload['server_name']);
                return ['http_error' => 400, 'reason' => 'Unsupported file type provided'];
            }
            #Check if we have an image
            if (preg_match('/^image\/.+/ui', $upload['type']) === 1) {
                #Convert to webp if it's a supported format, unless we chose not to
                if ($toWebp) {
                    $converted = Images::toWebP($upload['server_path'].'/'.$upload['server_name']);
                } else {
                    $converted = false;
                }
                if ($converted) {
                    $upload['hash'] = hash_file('sha3-512', $converted);
                    $upload['size'] = filesize($converted);
                    $upload['server_name'] = preg_replace('/(.+)(\..+$)/u', '$1.webp', $upload['server_name']);
                    $upload['new_name'] = $upload['hash'].'.webp';
                    $upload['user_name'] = preg_replace('/(.+)(\..+$)/u', '$1.webp', $upload['user_name']);
                    $upload['type'] = 'image/webp';
                } else {
                    $upload['new_name'] = $upload['server_name'];
                }
                $upload['new_path'] = Config::$uploadedImg;
                $upload['location'] = '/assets/images/uploaded/';
            } else {
                if ($onlyImages) {
                    #We do not accept non-images
                    return ['http_error' => 415, 'reason' => 'File is not an image'];
                }
                $upload['new_name'] = $upload['server_name'];
                $upload['new_path'] = Config::$uploaded;
                $upload['location'] = '/data/uploaded/';
            }
            #Get extension
            $upload['extension'] = pathinfo($upload['server_path'].'/'.$upload['server_name'], PATHINFO_EXTENSION);
            #Get a path for hash-tree structure
            $upload['hash_tree'] = mb_substr($upload['hash'], 0, 2, 'UTF-8').'/'.mb_substr($upload['hash'], 2, 2, 'UTF-8').'/'.mb_substr($upload['hash'], 4, 2, 'UTF-8').'/';
            if (!is_dir($upload['new_path'].'/'.$upload['hash_tree'])) {
                mkdir($upload['new_path'].'/'.$upload['hash_tree'], recursive: true);
            }
            #Set the file location to return in output
            $upload['location'] .= $upload['hash_tree'].$upload['new_name'];
            #Move to the hash-tree directory only if a file is not already present
            if (is_file($upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                #Remove a newly downloaded copy
                @unlink($upload['server_path'].'/'.$upload['server_name']);
            } elseif (!@rename($upload['server_path'].'/'.$upload['server_name'], $upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                @unlink($upload['server_path'].'/'.$upload['server_name']);
                return ['http_error' => 500, 'reason' => 'Failed to move file to final destination'];
            }
            #Add to the database
            Config::$dbController::query(
                'INSERT IGNORE INTO `sys__files`(`fileid`, `userid`, `name`, `extension`, `mime`, `size`) VALUES (:hash, :userid, :filename, :extension, :mime, :size);',
                [
                    ':hash' => $upload['hash'],
                    ':userid' => $_SESSION['userid'] ?? Config::userIDs['System user'],
                    ':filename' => $upload['user_name'],
                    ':extension' => $upload['extension'],
                    ':mime' => $upload['type'],
                    ':size' => [$upload['size'], 'int'],
                ]
            );
            return $upload;
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to upload file'];
        }
    }
}
