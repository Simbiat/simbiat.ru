<?php
declare(strict_types = 1);

namespace Simbiat\Website;

#Class for cUrl related functions. Needed more for settings' uniformity
use Simbiat\Database\Query;
use Simbiat\http20\Common;
use Simbiat\http20\Sharing;

use function in_array;

/**
 * Common Curl-related functions
 */
class Curl
{
    #cURL options
    protected array $curl_options = [
        \CURLOPT_POST => false,
        \CURLOPT_HEADER => true,
        \CURLOPT_RETURNTRANSFER => true,
        #Allow caching and reuse of already open connections
        \CURLOPT_FRESH_CONNECT => false,
        \CURLOPT_FORBID_REUSE => false,
        #Let cURL determine appropriate HTTP version
        \CURLOPT_HTTP_VERSION => \CURL_HTTP_VERSION_NONE,
        \CURLOPT_CONNECTTIMEOUT => 10,
        \CURLOPT_TIMEOUT => 30,
        \CURLOPT_FOLLOWLOCATION => true,
        \CURLOPT_MAXREDIRS => 3,
        \CURLOPT_HTTPHEADER => [],
        \CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 Edg/115.0.1901.200',
        \CURLOPT_ENCODING => '',
        \CURLOPT_SSL_VERIFYPEER => true,
        \CURLOPT_SSLVERSION => \CURL_SSLVERSION_TLSv1_2 | \CURL_SSLVERSION_MAX_TLSv1_3,
        \CURLOPT_DEFAULT_PROTOCOL => 'https',
        \CURLOPT_PROTOCOLS => \CURLPROTO_HTTPS,
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
    public static \CurlHandle|null|false $curl_handle = null;
    #Allowed MIME types
    public const array ALLOWED_MIME = [
        #For now only images
        'image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'
    ];
    
    final public function __construct()
    {
        #Check if cURL handle already created and create it if not
        if (!self::$curl_handle instanceof \CurlHandle) {
            self::$curl_handle = \curl_init();
            if (self::$curl_handle !== false && !\curl_setopt_array(self::$curl_handle, $this->curl_options) && !\curl_setopt(self::$curl_handle, \CURLOPT_HTTPHEADER, self::$headers)) {
                #Set default headers
                self::$curl_handle = false;
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
        if (!self::$curl_handle instanceof \CurlHandle) {
            return false;
        }
        #Get page contents
        \curl_setopt(self::$curl_handle, \CURLOPT_HEADER, true);
        #Directing output to a temporary file, instead of STDOUT, because I've witnessed edge cases, where due to an error (or even a notice) the output is sent to the browser directly even with CURLOPT_RETURNTRANSFER set to true
        \curl_setopt(self::$curl_handle, \CURLOPT_FILE, \tmpfile());
        #For some reason, if we set the CURLOPT_FILE, CURLOPT_RETURNTRANSFER gets reset to false
        \curl_setopt(self::$curl_handle, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt(self::$curl_handle, \CURLOPT_URL, $link);
        #Get a response
        $response = \curl_exec(self::$curl_handle);
        $http_code = \curl_getinfo(self::$curl_handle, \CURLINFO_HTTP_CODE);
        if ($response === false) {
            return false;
        }
        if ($http_code !== 200) {
            return $http_code;
        }
        return mb_substr($response, \curl_getinfo(self::$curl_handle, \CURLINFO_HEADER_SIZE), encoding: 'UTF-8');
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
        $filepath = \tempnam(\sys_get_temp_dir(), 'download');
        if (!self::$curl_handle instanceof \CurlHandle) {
            return false;
        }
        #Get a file
        \curl_setopt(self::$curl_handle, \CURLOPT_URL, $link);
        $fp = \fopen($filepath, 'wb');
        if ($fp === false) {
            return false;
        }
        \curl_setopt(self::$curl_handle, \CURLOPT_HEADER, false);
        \curl_setopt(self::$curl_handle, \CURLOPT_FILE, $fp);
        #Get a response
        $response = \curl_exec(self::$curl_handle);
        $http_code = \curl_getinfo(self::$curl_handle, \CURLINFO_HTTP_CODE);
        #Close file
        @\fclose($fp);
        if ($response === false || $http_code !== 200) {
            return false;
        }
        #Rename the file to give it a proper extension
        $mime = \mime_content_type($filepath);
        $new_name = \pathinfo($filepath, \PATHINFO_FILENAME).'.'.(Common::getExtensionFromMime($mime) ?? \preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/u', '$2', $link));
        \rename($filepath, \sys_get_temp_dir().'/'.$new_name);
        $filepath = \sys_get_temp_dir().'/'.$new_name;
        return [
            'server_name' => $new_name,
            'server_path' => \sys_get_temp_dir(),
            'user_name' => \preg_replace('/(.+)(\.[^?#\s]+)([?#].+)?$/u', '$1$2', \basename($link)),
            'size' => \filesize($filepath),
            'type' => $mime,
            'hash' => \hash_file('sha3-512', $filepath),
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
        if (!self::$curl_handle instanceof \CurlHandle) {
            return false;
        }
        \curl_setopt(self::$curl_handle, \CURLOPT_POSTFIELDS, $payload);
        \curl_setopt(self::$curl_handle, \CURLOPT_URL, $link);
        #Get a response
        $response = \curl_exec(self::$curl_handle);
        $http_code = \curl_getinfo(self::$curl_handle, \CURLINFO_HTTP_CODE);
        return !($response === false || !in_array($http_code, [200, 201, 202, 203, 204, 205, 206, 207, 208, 226], true));
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
        if (!self::$curl_handle instanceof \CurlHandle) {
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
        if (!in_array(mb_strtolower($header, 'UTF-8'), \array_map('\strtolower', self::$headers), true)) {
            #Add it, if not
            self::$headers[] = $header;
            \curl_setopt(self::$curl_handle, \CURLOPT_HTTPHEADER, self::$headers);
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
        $key = \array_search(mb_strtolower($header, 'UTF-8'), \array_map('\strtolower', self::$headers), true);
        if ($key !== false) {
            #Remove it, if yes
            unset(self::$headers[$key]);
            \curl_setopt(self::$curl_handle, \CURLOPT_HTTPHEADER, self::$headers);
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
        \curl_setopt(self::$curl_handle, $option, $value);
        return $this;
    }
    
    /**
     * Check if a remote file exists
     * @param $remote_file
     *
     * @return bool
     */
    public function ifExists($remote_file): bool
    {
        if (!self::$curl_handle instanceof \CurlHandle) {
            return false;
        }
        #Initialize cUrl
        \curl_setopt(self::$curl_handle, \CURLOPT_NOBODY, true);
        \curl_setopt(self::$curl_handle, \CURLOPT_URL, $remote_file);
        \curl_exec(self::$curl_handle);
        $http_code = \curl_getinfo(self::$curl_handle, \CURLINFO_HTTP_CODE);
        \curl_close(self::$curl_handle);
        #Check code
        return $http_code === 200;
    }
    
    /**
     * Function to process file uploads either through POST/PUT or by using a provided link
     * @param string $link        URL to process if we are to download a remote file
     * @param bool   $only_images Flag to indicate that only images are allowed
     * @param bool   $to_webp     Whether to convert images to WEBP (if possible)
     *
     * @return array
     */
    public function upload(string $link = '', bool $only_images = false, bool $to_webp = true): array
    {
        try {
            #Check DB
            if (Query::$dbh === null) {
                return ['http_error' => 503, 'reason' => 'Database unavailable'];
            }
            Security::log('File upload', 'Attempted to upload file', ['$_FILES' => $_FILES, 'link' => $link], $_SESSION['user_id'] ?? Config::USER_IDS['System user']);
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
                            @\unlink($file['server_path'].'/'.$file['server_name']);
                        }
                    }
                }
                $upload = $upload[0];
            }
            #Check if a file is one of the allowed types
            if (!in_array($upload['type'], self::ALLOWED_MIME, true)) {
                @\unlink($upload['server_path'].'/'.$upload['server_name']);
                return ['http_error' => 400, 'reason' => 'Unsupported file type provided'];
            }
            #Check if we have an image
            if (\preg_match('/^image\/.+/ui', $upload['type']) === 1) {
                #Convert to webp if it's a supported format, unless we chose not to
                if ($to_webp) {
                    $converted = Images::toWebP($upload['server_path'].'/'.$upload['server_name']);
                } else {
                    $converted = false;
                }
                if ($converted) {
                    $upload['hash'] = \hash_file('sha3-512', $converted);
                    $upload['size'] = \filesize($converted);
                    $upload['server_name'] = \preg_replace('/(.+)(\..+$)/u', '$1.webp', $upload['server_name']);
                    $upload['new_name'] = $upload['hash'].'.webp';
                    $upload['user_name'] = \preg_replace('/(.+)(\..+$)/u', '$1.webp', $upload['user_name']);
                    $upload['type'] = 'image/webp';
                } else {
                    $upload['new_name'] = $upload['server_name'];
                }
                $upload['new_path'] = Config::$uploaded_img;
                $upload['location'] = '/assets/images/uploaded/';
            } else {
                if ($only_images) {
                    #We do not accept non-images
                    return ['http_error' => 415, 'reason' => 'File is not an image'];
                }
                $upload['new_name'] = $upload['server_name'];
                $upload['new_path'] = Config::$uploaded;
                $upload['location'] = '/data/uploaded/';
            }
            #Get extension
            $upload['extension'] = \pathinfo($upload['server_path'].'/'.$upload['server_name'], \PATHINFO_EXTENSION);
            #Get a path for hash-tree structure
            $upload['hash_tree'] = mb_substr($upload['hash'], 0, 2, 'UTF-8').'/'.mb_substr($upload['hash'], 2, 2, 'UTF-8').'/'.mb_substr($upload['hash'], 4, 2, 'UTF-8').'/';
            if (!\is_dir($upload['new_path'].'/'.$upload['hash_tree']) && !\mkdir($upload['new_path'].'/'.$upload['hash_tree'], recursive: true) && !\is_dir($upload['new_path'].'/'.$upload['hash_tree'])) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $upload['new_path'].'/'.$upload['hash_tree']));
            }
            #Set the file location to return in output
            $upload['location'] .= $upload['hash_tree'].$upload['new_name'];
            #Move to the hash-tree directory only if a file is not already present
            if (\is_file($upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                #Remove a newly downloaded copy
                @\unlink($upload['server_path'].'/'.$upload['server_name']);
            } elseif (!@\rename($upload['server_path'].'/'.$upload['server_name'], $upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                @\unlink($upload['server_path'].'/'.$upload['server_name']);
                return ['http_error' => 500, 'reason' => 'Failed to move file to final destination'];
            }
            #Add to the database
            Query::query(
                'INSERT IGNORE INTO `sys__files`(`file_id`, `user_id`, `name`, `extension`, `mime`, `size`) VALUES (:hash, :user_id, :filename, :extension, :mime, :size);',
                [
                    ':hash' => $upload['hash'],
                    ':user_id' => $_SESSION['user_id'] ?? Config::USER_IDS['System user'],
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
