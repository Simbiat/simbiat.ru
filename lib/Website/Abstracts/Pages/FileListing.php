<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts\Pages;

use Simbiat\Website\Config;
use Simbiat\http20\Headers;
use Simbiat\SafeFileName;

use function array_slice, count, in_array;

/**
 * Class to generate directory/file listing pages
 */
class FileListing extends StaticPage
{
    #Cache age set to for a day
    protected int $cacheAge = 1440;
    #Directories relative to working dir
    #Expected format: ['URL_path_name' => ['path' => 'path', 'name' => 'name to use in UI', 'depth' => 0]]
    #Depth, when set and more than 0 will mean that, until folders up to this depth, will be scanned only for other folders and as such allow folder traversal
    protected array $dirs = [];
    #Items to display per page for lists
    public int $listItems = 100;
    #Flag whether to go recursive or not
    protected bool $recursive = false;
    #List of files that should be excluded
    protected array $exclude = [];
    #String to search for in file names
    protected string $search_for = '';
    #Page number
    protected int $page = 1;
    
    /**
     * Generate the page
     * @param array $path
     *
     * @return array
     */
    protected function generate(array $path): array
    {
        $outputArray = [];
        #Set the page number
        $this->page = (int)($_GET['page'] ?? 1);
        $this->search_for = SafeFileName::sanitize($_GET['search'] ?? '', true, true);
        if (empty($this->dirs)) {
            return ['http_error' => 503, 'reason' => 'No directories are setup for this endpoint'];
        }
        if (empty($path[0])) {
            #Get files' counts
            foreach ($this->dirs as $key => $dir) {
                #Check if a directory exists
                if (!is_dir(Config::$work_dir.$dir['path'])) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$dir['path'].'` does not exist.'];
                }
                if (!empty($dir['depth']) && $dir['depth'] >= 1) {
                    $outputArray['files'][$key] = $this->getDirs(Config::$work_dir.$dir['path'], true);
                } else {
                    $outputArray['files'][$key] = $this->getFiles(Config::$work_dir.$dir['path'], true);
                }
                $outputArray['files'][$key]['name'] = $dir['name'];
            }
        } else {
            if (!\array_key_exists($path[0], $this->dirs)) {
                return ['http_error' => 404, 'reason' => 'Unsupported path.', 'suggested_link' => $this->getLastCrumb()];
            }
            if (empty($this->dirs[$path[0]]['depth'])) {
                $outputArray = $this->listFiles($path[0]);
            } else {
                if (!is_dir(Config::$work_dir.$this->dirs[$path[0]]['path'])) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs
                $this->attachCrumb($path[0], $this->dirs[$path[0]]['name']);
                $subDir = $this->getRealSubDirPath(Config::$work_dir.$this->dirs[$path[0]]['path'], array_slice($path, 1));
                if (!$subDir) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].'/'.implode('/', array_slice($path, 1)).'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                if (count($path) - 1 > $this->dirs[$path[0]]['depth']) {
                    return ['http_error' => 400, 'reason' => 'You\'ve gone too deep.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs with subfolders
                foreach (array_slice($path, 1) as $subPath) {
                    $this->attachCrumb($subPath, $subPath);
                }
                if (count($path) - 1 === $this->dirs[$path[0]]['depth']) {
                    #Get files, since we are on the last allowed level
                    $outputArray = $this->listFiles($path[0], $subDir);
                } else {
                    #Get the list of directories
                    $outputArray['files'][$path[0]] = $this->getDirs(Config::$work_dir.$this->dirs[$path[0]]['path'].$subDir);
                }
                $outputArray['files'][$path[0]]['name'] = $this->dirs[$path[0]]['name'];
                $outputArray['files'][$path[0]]['parent'] = array_slice($this->breadCrumb, -2, 1)[0];
            }
        }
        $outputArray['path'] = $path[0] ?? null;
        return $outputArray;
    }
    
    /**
     * Try to get the actual subdirectory path, based on an actual case of the name, and not the lower case as was passed in URL
     *
     * @param string $path   Base folder
     * @param array  $subDir Array of subdirectories to check
     *
     * @return string|false
     */
    private function getRealSubDirPath(string $path, array $subDir): string|false
    {
        $fullSubDir = '/';
        foreach ($subDir as $subPath) {
            $match = false;
            foreach (scandir($path.$fullSubDir, SCANDIR_SORT_NONE) as $pathEntry) {
                if (strcasecmp($pathEntry, $subPath) === 0) {
                    $match = true;
                    $fullSubDir .= $pathEntry.'/';
                    break;
                }
            }
            if (!$match) {
                return false;
            }
        }
        return $fullSubDir;
    }
    
    /**
     * Generate a list of files
     * @param string $path   Base path
     * @param string $subDir Subdirectory path, if any
     *
     * @return array
     */
    private function listFiles(string $path, string $subDir = ''): array
    {
        $outputArray['files'][$path] = $this->getFiles(Config::$work_dir.$this->dirs[$path]['path'].$subDir);
        #Process pagination
        $totalPages = (int)ceil($outputArray['files'][$path]['count'] / $this->listItems);
        if ($totalPages > 0 && $this->page > $totalPages) {
            #Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').$this->getLastCrumb().'/'.(!empty($this->search_for) ? '?search='.rawurlencode($this->search_for).'&page='.$totalPages : '?page='.$totalPages), false);
        }
        if ($outputArray['files'][$path]['count'] > $this->listItems) {
            #Generate pagination data
            $outputArray['pagination'] = ['current' => $this->page, 'total' => $totalPages, 'prefix' => '?page=', 'per' => $this->listItems];
            #Update the list of files by slicing
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
            #Add the path to breadcrumbs
            $this->attachCrumb($path, $this->dirs[$path]['name']);
        }
        if (empty($this->search_for)) {
            if ($this->page > 1) {
                #Add the path to breadcrumbs
                $this->attachCrumb('?page='.$this->page, 'Page '.$this->page);
            }
        } else {
            $this->attachCrumb('?search='.rawurlencode($this->search_for), 'Search for `'.$this->search_for.'`');
            if ($this->page > 1) {
                #Add the path to breadcrumbs
                $this->attachCrumb('page='.$this->page, 'Page '.$this->page, true);
            }
        }
        #Update title and H1
        $this->title = $this->dirs[$path]['name'].' from '.$this->title;
        $this->h1 = $this->dirs[$path]['name'];
        return $outputArray;
    }
    
    /**
     * Get a list of directories
     * @param string $path      Base path
     * @param bool   $countOnly Whether we just count or generate a full list with details
     *
     * @return array
     */
    private function getDirs(string $path, bool $countOnly = false): array
    {
        #Order results
        $iterator = iterator_to_array(new \CallbackFilterIterator(new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), function ($cur) {
            return $cur->isDir();
        }));
        ksort($iterator, SORT_NATURAL);
        #Prepare the array
        $result = [];
        $result['dirs'] = [];
        if ($countOnly) {
            $result['count'] = iterator_count($iterator);
        } else {
            foreach ($iterator as $key => $file) {
                if (!in_array($key, $this->exclude, true)) {
                    $fileDetails = [
                        'dirname' => $file->getFilename(),
                        #Path relative to the working directory
                        'path' => str_replace(Config::$work_dir, '', $file->getPath()),
                        'time' => $file->getMTime(),
                    ];
                    $result['dirs'][] = $fileDetails;
                }
            }
            $result['count'] = count($result['dirs']);
        }
        return $result;
    }
    
    /**
     * Get a list of files
     *
     * @param string $path      Base path
     * @param bool   $countOnly Whether we just count or generate a full list with details
     *
     * @return array
     */
    protected function getFiles(string $path, bool $countOnly = false): array
    {
        if ($this->recursive) {
            $iterator = new \CallbackFilterIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST), function ($cur) {
                return $cur->isFile();
            });
        } else {
            $iterator = new \CallbackFilterIterator(new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), function ($cur) {
                return $cur->isFile();
            });
        }
        #Order results
        $iterator = iterator_to_array($iterator);
        ksort($iterator, SORT_NATURAL);
        #Prepare the array
        $result = [];
        $result['files'] = [];
        if ($countOnly) {
            $result['count'] = iterator_count($iterator);
        } else {
            $id = 1;
            foreach ($iterator as $key => $file) {
                if (!in_array($key, $this->exclude, true) && (empty($this->search_for) || mb_stripos($key, $this->search_for, 0, 'UTF-8') !== false)) {
                    if ($id >= (($this->page - 1) * $this->listItems + 1) && $id <= ($this->page * $this->listItems)) {
                        $fileDetails = [
                            'filename' => $file->getFilename(),
                            'basename' => $file->getBasename('.'.$file->getExtension()),
                            #Path relative to the working directory
                            'path' => str_replace(Config::$work_dir, '', $file->getPath()),
                            'mime' => mime_content_type($file->getPathname()),
                            'size' => $file->getSize(),
                            'time' => $file->getMTime(),
                            'key' => $id++,
                        ];
                        #Extra processing, if required
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
        }
        return $result;
    }
    
    /**
     * Override the function to do some extra processing over the array
     * @param array $fileDetails
     *
     * @return void
     */
    protected function extra(array &$fileDetails): void {}
}
