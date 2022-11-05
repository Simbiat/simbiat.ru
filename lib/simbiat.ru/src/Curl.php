<?php
declare(strict_types=1);
namespace Simbiat;

#Class for cUrl related functions. Needed more for settings' uniformity
use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Sharing;

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
    #Allowed MIME types
    public const allowedMime = [
        #For now only images
        'image/avif','image/bmp','image/gif','image/jpeg','image/png','image/webp','image/svg+xml'
    ];

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
        #Directing output to a temporary file, instead of STDOUT, because I've witnessed edge cases, where due to an error (or even a notice) the output is sent to browser directly even with CURLOPT_RETURNTRANSFER set to true
        curl_setopt(self::$curlHandle, CURLOPT_FILE, tmpfile());
        #For some reason, if we set the CURLOPT_FILE, CURLOPT_RETURNTRANSFER gets reset to false
        curl_setopt(self::$curlHandle, CURLOPT_RETURNTRANSFER, true);
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
        #Set temp filepath
        $filepath = tempnam(sys_get_temp_dir(), 'download');
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
    
    #Function to process file uploads either through POST/PUT or by using a provided link
    public function upload(string $link = '', bool $toWebp = true): false|array
    {
        try {
            #Check DB
            if (empty(HomePage::$dbController)) {
                return ['http_error' => 503, 'reason' => 'Database unavailable'];
            }
            Security::log('File upload', 'Attempted to upload file', ['$_FILES' => $_FILES, 'link' => $link]);
            if (!empty($link)) {
                $upload = $this->getFile($link);
                if ($upload === false) {
                    return false;
                }
            } else {
                $upload = Sharing::upload(Config\Common::$uploaded, exit: false);
                if (!is_array($upload) || empty($upload[0]['server_name'])) {
                    return ['http_error' => 500, 'reason' => 'Failed to upload the file'];
                } else {
                    #If $upload had more than 1 file - remove all except 1st one
                    if (count($upload) > 1) {
                        foreach ($upload as $key => $file) {
                            if ($key !== 0) {
                                @unlink($file['server_path'].'/'.$file['server_name']);
                            }
                        }
                    }
                    $upload = $upload[0];
                }
            }
            #Check if file is one of the allowed types
            if (!in_array($upload['type'], self::allowedMime)) {
                @unlink($upload['server_path'].'/'.$upload['server_name']);
                return ['http_error' => 400, 'reason' => 'Unsupported file type provided'];
            }
            #Check if we have an image
            if (preg_match('/^image\/.+/ui', $upload['type']) === 1) {
                #Convert to webp, if it's a supported format, unless we chose not to
                if ($toWebp) {
                    $converted = Images::toWebP($upload['server_path'].'/'.$upload['server_name']);
                } else {
                    $converted = false;
                }
                if ($converted) {
                    $upload['hash'] = hash_file('sha3-512', $converted);
                    $upload['size'] = filesize($converted);
                    $upload['server_name'] = preg_replace('/(.+)(\..+$)/ui', '$1.webp', $upload['server_name']);
                    $upload['new_name'] = $upload['hash'].'.webp';
                    $upload['user_name'] = preg_replace('/(.+)(\..+$)/ui', '$1.webp', $upload['user_name']);
                    $upload['type'] = 'image/webp';
                } else {
                    $upload['new_name'] = $upload['server_name'];
                }
                $upload['new_path'] = Config\Common::$uploadedImg;
                $upload['location'] = '/img/uploaded/';
            } else {
                $upload['new_name'] = $upload['server_name'];
                $upload['new_path'] = Config\Common::$uploaded;
                $upload['location'] = '/data/uploaded/';
            }
            #Get extension
            $upload['extension'] = pathinfo($upload['server_path'].'/'.$upload['server_name'], PATHINFO_EXTENSION);
            #Get path for hash-tree structure
            $upload['hash_tree'] = substr($upload['hash'], 0, 2).'/'.substr($upload['hash'], 2, 2).'/'.substr($upload['hash'], 4, 2).'/';
            if (!is_dir($upload['new_path'].'/'.$upload['hash_tree'])) {
                mkdir($upload['new_path'].'/'.$upload['hash_tree'], recursive: true);
            }
            #Set file location to return in output
            $upload['location'] .= $upload['hash_tree'].$upload['new_name'];
            #Move to hash-tree directory, only if file is not already present
            if (!is_file($upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                if (rename($upload['server_path'].'/'.$upload['server_name'], $upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                    #Add to database
                    HomePage::$dbController->query(
                        'INSERT IGNORE INTO `sys__files`(`fileid`, `userid`, `name`, `extension`, `mime`, `size`) VALUES (:hash, :userid, :filename, :extension, :mime, :size);',
                        [
                            ':hash' => $upload['hash'],
                            ':userid' => $_SESSION['userid'],
                            ':filename' => $upload['user_name'],
                            ':extension' => $upload['extension'],
                            ':mime' => $upload['type'],
                            ':size' => [$upload['size'], 'int'],
                        ]
                    );
                } else {
                    @unlink($upload['server_path'].'/'.$upload['server_name']);
                    return false;
                }
            } else {
                #Remove newly downloaded copy
                @unlink($upload['server_path'].'/'.$upload['server_name']);
            }
            return $upload;
        } catch (\Throwable) {
            return false;
        }
    }
}
