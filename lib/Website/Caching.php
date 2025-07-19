<?php
declare(strict_types = 1);

namespace Simbiat\Website;

/**
 * Class to handle page caching
 */
class Caching
{
    /**
     * @param string $cache_dir Directory to use for cache files
     */
    public function __construct(private string $cache_dir = '')
    {
        if (\preg_match('/^\s*$/u', $this->cache_dir) === 1) {
            $this->cache_dir = Config::$html_cache;
        }
        if (!\is_dir($this->cache_dir) && !\mkdir($this->cache_dir, recursive: true) && !\is_dir($this->cache_dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $this->cache_dir));
        }
        if (\preg_match('/\/$/', $this->cache_dir) !== 1) {
            $this->cache_dir .= '/';
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
            $sub_dir = mb_substr($key, 0, 2, 'UTF-8').'/'.mb_substr($key, 2, 2, 'UTF-8').'/'.mb_substr($key, 4, 2, 'UTF-8').'/';
            #Create folder if missing
            if (!\is_dir($this->cache_dir.$sub_dir) && !\mkdir($this->cache_dir.$sub_dir, recursive: true) && !\is_dir($this->cache_dir.$sub_dir)) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $this->cache_dir.$sub_dir));
            }
            #Write the file. We do not care much if it fails
            if (@file_put_contents($this->cache_dir.$sub_dir.$key.'.json', $data)) {
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
        $file = $this->cache_dir.mb_substr($key, 0, 2, 'UTF-8').'/'.mb_substr($key, 2, 2, 'UTF-8').'/'.mb_substr($key, 4, 2, 'UTF-8').'/'.$key.'.json';
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
     * Gets JSON decoded array from a file
     * @param string $cache_path
     *
     * @return array
     */
    public function getArrayFromFile(string $cache_path): array
    {
        #Check if the cache file exists
        if (is_file($cache_path)) {
            #Read the cache
            $json = file_get_contents($cache_path);
            if ($json !== false && $json !== '') {
                try {
                    $json = json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
                } catch (\Throwable) {
                    $json = [];
                }
                if ($json !== NULL) {
                    if (!is_array($json)) {
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
