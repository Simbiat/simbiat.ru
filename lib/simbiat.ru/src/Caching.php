<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;

class Caching
{
    private string $cacheDir = '';

    public function __construct(string $cacheDir = '')
    {
        if (empty($cacheDir)) {
            $this->cacheDir = Common::$htmlCache;
        } else {
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, recursive: true);
                if (preg_match('/.*\/$/i', $cacheDir) !== 1) {
                    $this->cacheDir = $cacheDir.'/';
                } else {
                    $this->cacheDir = $cacheDir;
                }
            }
        }
    }

    #Write cache to file
    public function write(array $data, string $key = '', int $age = 0): bool
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
            $subDir = substr($key, 0, 2).'/'.substr($key, 2, 2).'/';
            #Create folder if missing
            if (!is_dir($this->cacheDir.$subDir)) {
                @mkdir($this->cacheDir.$subDir, recursive: true);
            }
            #Write the file. We do not care much if it fails
            if (@file_put_contents($this->cacheDir.$subDir.$key. '.json', $data)) {
                @header('X-Server-Cached: true');
                @header('X-Server-Cache-Hit: false');
                return true;
            }
        }
        @header('X-Server-Cached: false');
        @header('X-Server-Cache-Hit: false');
        return true;
    }

    #Read from cache
    public function read(string $key = ''): array
    {
        #Generate key
        $key = $this->key($key);
        #Generate file name
        $file = $this->cacheDir.substr($key, 0, 2).'/'.substr($key, 2, 2).'/'.$key. '.json';
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

    #Genereate key
    public function key(string $key): string
    {
        if (empty($key)) {
            $key = hash('sha3-512', HomePage::$canonical);
        } else {
            $key = hash('sha3-512', $key);
        }
        return $key;
    }

    #Gets JSON decoded array from file
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
