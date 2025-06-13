<?php
declare(strict_types = 1);

namespace Simbiat\Website;

/**
 * Class to handle page caching
 */
class Caching
{
    /**
     * @param string $cacheDir Directory to use for cache files
     */
    public function __construct(private string $cacheDir = '')
    {
        if (empty($this->cacheDir)) {
            $this->cacheDir = Config::$html_cache;
        } elseif (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, recursive: true);
            if (preg_match('/\/$/', $this->cacheDir) !== 1) {
                $this->cacheDir .= '/';
            }
        }
    }
    
    /**
     * Write cache to file
     * @param array|string $data Data to write
     * @param string       $key  Key to write to
     * @param int          $age  How long to store in seconds
     *
     * @return bool
     */
    public function write(array|string $data, string $key = '', int $age = 0): bool
    {
        #JSON encode the value
        try {
            if (!empty($data)) {
                #Ensure we do not save CSRF
                unset($data['X-CSRF-Token']);
                #Add headers data
                $data['http_headers'] = headers_list();
                #Set expiration date
                if ($age > 0) {
                    $data['cache_expires_at'] = time() + $age;
                }
                #JSON encode the data
                $data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
            }
        } catch (\Throwable) {
            $data = '';
        }
        if (!empty($data)) {
            #Generate key
            $key = $this->key($key);
            #Generate subdirectory name
            $subDir = mb_substr($key, 0, 2, 'UTF-8').'/'.mb_substr($key, 2, 2, 'UTF-8').'/'.mb_substr($key, 4, 2, 'UTF-8').'/';
            #Create folder if missing
            if (!is_dir($this->cacheDir.$subDir)) {
                @mkdir($this->cacheDir.$subDir, recursive: true);
            }
            #Write the file. We do not care much if it fails
            if (@file_put_contents($this->cacheDir.$subDir.$key.'.json', $data)) {
                @header('X-Server-Cached: true');
                @header('X-Server-Cache-Hit: false');
                return true;
            }
        }
        @header('X-Server-Cached: false');
        @header('X-Server-Cache-Hit: false');
        return true;
    }
    
    /**
     * Read from cache
     * @param string $key
     *
     * @return array
     */
    public function read(string $key = ''): array
    {
        #Generate key
        $key = $this->key($key);
        #Generate file name
        $file = $this->cacheDir.mb_substr($key, 0, 2, 'UTF-8').'/'.mb_substr($key, 2, 2, 'UTF-8').'/'.mb_substr($key, 4, 2, 'UTF-8').'/'.$key.'.json';
        $data = $this->getArrayFromFile($file);
        if (empty($data)) {
            @header('X-Server-Cached: false');
            @header('X-Server-Cache-Hit: false');
        } else {
            #Enforce cached page flag
            $data['cached_page'] = true;
            if (!empty($data['http_headers'])) {
                #Send headers
                array_map('header', $data['http_headers']);
            }
            #Send header indicating that cached response was sent
            @header('X-Server-Cached: true');
            @header('X-Server-Cache-Hit: true');
        }
        #Ensure we use fresh CSRF
        unset($data['X-CSRF-Token']);
        return $data;
    }
    
    /**
     * Generate key
     * @param string $key
     *
     * @return string
     */
    public function key(string $key): string
    {
        if (empty($key)) {
            $key = hash('sha3-512', Config::$canonical);
        } else {
            $key = hash('sha3-512', $key);
        }
        return $key;
    }
    
    /**
     * Gets JSON decoded array from file
     * @param string $cachePath
     *
     * @return array
     */
    public function getArrayFromFile(string $cachePath): array
    {
        #Check if cache file exists
        if (is_file($cachePath)) {
            #Read the cache
            $json = file_get_contents($cachePath);
            if ($json !== false && $json !== '') {
                try {
                    $json = json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
                } catch (\Throwable) {
                    $json = [];
                }
                if ($json !== NULL) {
                    if (!\is_array($json)) {
                        return [];
                    }
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } else {
            return [];
        }
        return $json;
    }
}
