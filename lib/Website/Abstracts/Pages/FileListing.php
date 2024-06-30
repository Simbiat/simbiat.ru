<?php
declare(strict_types=1);
namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;
use Simbiat\SafeFileName;
use Simbiat\Website\Abstracts\Pages\StaticPage;

class FileListing extends StaticPage
{
    #Cache age set to for a day
    protected int $cacheAge = 1440;
    #Directories relative to working dir
    #Expected format: ['URL_path_name' => ['path' => 'path', 'name' => 'name to use in UI', 'depth' => 0]]
    #Depth, when set and more than 0 will mean, that, until folders up to this depth, will be scanned only for other folders and as such allow folder traversal
    protected array $dirs = [];
    #Items to display per page for lists
    public int $listItems = 100;
    #Flag whether to go recursive or not
    protected bool $recursive = false;
    #List of files that should be excluded
    protected array $exclude = [];
    #String to search for in file names
    protected string $searchFor = '';
    #Page number
    protected int $page = 1;

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Set page number
        $this->page = (int)($_GET['page'] ?? 1);
        $this->searchFor = SafeFileName::sanitize($_GET['search'] ?? '', true, true);
        if (empty($this->dirs)) {
            return ['http_error' => 503, 'reason' => 'No directories are setup for this endpoint'];
        }
        if (empty($path[0])) {
            #Get files' counts
            foreach ($this->dirs as $key=>$dir) {
                #Check if a directory exist
                if (!is_dir(Config::$workDir.$dir['path'])) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$dir['path'].'` does not exist.'];
                }
                if (!empty($dir['depth']) && $dir['depth'] >= 1) {
                    $outputArray['files'][$key] = $this->getDirs(Config::$workDir.$dir['path'], true);
                } else {
                    $outputArray['files'][$key] = $this->getFiles(Config::$workDir.$dir['path'], true);
                }
                $outputArray['files'][$key]['name'] = $dir['name'];
            }
        } else {
            if (!array_key_exists($path[0], $this->dirs)) {
                return ['http_error' => 404, 'reason' => 'Unsupported path.', 'suggested_link' => $this->getLastCrumb()];
            }
            if (empty($this->dirs[$path[0]]['depth'])) {
                $outputArray = $this->listFiles($path[0]);
            } else {
                $subDir =  '/'.implode('/', array_slice($path, 1));
                if (!is_dir(Config::$workDir.$this->dirs[$path[0]]['path'])) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs
                $this->attachCrumb($path[0], $this->dirs[$path[0]]['name'] );
                if (!is_dir(Config::$workDir.$this->dirs[$path[0]]['path'].$subDir)) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].$subDir.'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                if (count($path) - 1 > $this->dirs[$path[0]]['depth']) {
                    return ['http_error' => 400, 'reason' => 'You\'ve gone too deep.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs with sub-folders
                foreach (array_slice($path, 1) as $subPath) {
                    $this->attachCrumb($subPath, $subPath);
                }
                if (count($path) - 1 === $this->dirs[$path[0]]['depth']) {
                    #Get files, since we are on the last allow level
                    $outputArray = $this->listFiles($path[0], $subDir);
                } else {
                    #Get list of directories
                    $outputArray['files'][$path[0]] = $this->getDirs(Config::$workDir.$this->dirs[$path[0]]['path'].$subDir);
                }
                $outputArray['files'][$path[0]]['name'] = $this->dirs[$path[0]]['name'];
                $outputArray['files'][$path[0]]['parent'] = array_slice($this->breadCrumb, -2, 1)[0];
            }
        }
        $outputArray['path'] = $path[0] ?? null;
        return $outputArray;
    }
    
    private function listFiles(string $path, string $subDir = ''): array
    {
        $outputArray['files'][$path] = $this->getFiles(Config::$workDir.$this->dirs[$path]['path'].$subDir);
        #Process pagination
        $totalPages = (int)ceil($outputArray['files'][ $path ]['count'] / $this->listItems);
        if ($totalPages > 0 && $this->page > $totalPages) {
            #Redirect to last page
            Headers::redirect(Config::$baseUrl . ($_SERVER['SERVER_PORT'] !== 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . $this->getLastCrumb() . '/' . (!empty($this->searchFor) ? '?search='.rawurlencode($this->searchFor).'&page='.$totalPages : '?page='.$totalPages), false);
        }
        if ($outputArray['files'][$path]['count'] > $this->listItems) {
            #Generate pagination data
            $outputArray['pagination'] = ['current' => $this->page, 'total' => $totalPages, 'prefix' => '?page=', 'per' => $this->listItems];
            #Update list of files by slicing
            $outputArray['files'][$path]['files'] = array_slice($outputArray['files'][$path]['files'], ($this->page - 1) * $this->listItems, $this->listItems);
        }
        #Get the freshest date
        if (!empty($outputArray['files'][$path]['files'])) {
            $date = max(array_column($outputArray['files'][$path]['files'], 'time'));
            #Attempt to exit a bit earlier with Last Modified header
            if (!empty($date)) {
                $this->lastModified($date);
            }
        }
        $outputArray['files'][$path]['name'] = $this->dirs[$path]['name'];
        if (empty($subDir)) {
            #Add path to breadcrumbs
            $this->attachCrumb($path, $this->dirs[$path]['name']);
        }
        if (empty($this->searchFor)) {
            if ($this->page > 1) {
                #Add path to breadcrumbs
                $this->attachCrumb('?page='.$this->page, 'Page '.$this->page);
            }
        } else {
            $this->attachCrumb('?search='.rawurlencode($this->searchFor), 'Search for `'.$this->searchFor.'`');
            if ($this->page > 1) {
                #Add path to breadcrumbs
                $this->attachCrumb('page='.$this->page, 'Page '.$this->page, true);
            }
        }
        #Update title and H1
        $this->title = $this->dirs[$path]['name'].' from '.$this->title;
        $this->h1 = $this->dirs[$path]['name'];
        return $outputArray;
    }
    
    private function getDirs(string $path, bool $countOnly = false): array
    {
        $iterator = new \CallbackFilterIterator(new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), function($cur) {
            return $cur->isDir();
        });
        #Order results
        $iterator = iterator_to_array($iterator);
        ksort($iterator, SORT_NATURAL);
        #Prepare array
        $result = [];
        $result['dirs'] = [];
        if (!$countOnly) {
            foreach ($iterator as $key=>$file) {
                if (!in_array($key, $this->exclude, true)) {
                    $fileDetails = [
                        'dirname' => $key,
                        #Path relative to working directory
                        'path' => str_replace(Config::$workDir, '', $file->getPath()),
                        'time' => $file->getMTime(),
                    ];
                    $result['dirs'][] = $fileDetails;
                }
            }
            $result['count'] = count($result['dirs']);
        } else {
            $result['count'] = iterator_count($iterator);
        }
        return $result;
    }
    
    protected function getFiles(string $path, bool $countOnly = false): array
    {
        if ($this->recursive) {
            $iterator = new \CallbackFilterIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST), function($cur) {
                return $cur->isFile();
            });
        } else {
            $iterator = new \CallbackFilterIterator(new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), function($cur) {
                return $cur->isFile();
            });
        }
        #Order results
        $iterator = iterator_to_array($iterator);
        ksort($iterator, SORT_NATURAL);
        #Prepare array
        $result = [];
        $result['files'] = [];
        if (!$countOnly) {
            $id = 1;
            foreach ($iterator as $key=>$file) {
                if (!in_array($key, $this->exclude, true) && (empty($this->searchFor) || mb_stripos($key, $this->searchFor, 0, 'UTF-8') !== false)) {
                    if ($id >= (($this->page - 1) * $this->listItems + 1) && $id <= ($this->page  * $this->listItems)) {
                        $fileDetails = [
                            'filename' => $key,
                            'basename' => $file->getBasename('.'.$file->getExtension()),
                            #Path relative to working directory
                            'path' => str_replace(Config::$workDir, '', $file->getPath()),
                            'mime' => mime_content_type($file->getPathname()),
                            'size' => $file->getSize(),
                            'time' => $file->getMTime(),
                            'key' => $id++,
                        ];
                        #Extra processing, if any is required
                        $this->extra($fileDetails);
                    } else {
                        $fileDetails = [
                            'key' => $id++,
                        ];
                    }
                    $result['files'][] = $fileDetails;
                }
            }
            $result['count'] = count($result['files']);
        } else {
            $result['count'] = iterator_count($iterator);
        }
        return $result;
    }
    
    protected function extra(array &$fileDetails): void
    {
        #Override the function to do some extra processing over the array
    }
}
