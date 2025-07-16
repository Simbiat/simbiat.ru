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
    protected int $cache_age = 1440;
    #Directories relative to working dir
    #Expected format: ['URL_path_name' => ['path' => 'path', 'name' => 'name to use in UI', 'depth' => 0]]
    #Depth, when set and more than 0 will mean that, until folders up to this depth, will be scanned only for other folders and as such allow folder traversal
    protected array $dirs = [];
    #Items to display per page for lists
    public int $list_items = 100;
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
        $output_array = [];
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
                    $output_array['files'][$key] = $this->getDirs(Config::$work_dir.$dir['path'], true);
                } else {
                    $output_array['files'][$key] = $this->getFiles(Config::$work_dir.$dir['path'], true);
                }
                $output_array['files'][$key]['name'] = $dir['name'];
            }
        } else {
            if (!array_key_exists($path[0], $this->dirs)) {
                return ['http_error' => 404, 'reason' => 'Unsupported path.', 'suggested_link' => $this->getLastCrumb()];
            }
            if (empty($this->dirs[$path[0]]['depth'])) {
                $output_array = $this->listFiles($path[0]);
            } else {
                if (!is_dir(Config::$work_dir.$this->dirs[$path[0]]['path'])) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs
                $this->attachCrumb($path[0], $this->dirs[$path[0]]['name']);
                $sub_dir = $this->getRealSubDirPath(Config::$work_dir.$this->dirs[$path[0]]['path'], array_slice($path, 1));
                if (!$sub_dir) {
                    return ['http_error' => 404, 'reason' => 'Directory `'.$this->dirs[$path[0]]['path'].'/'.implode('/', array_slice($path, 1)).'` does not exist.', 'suggested_link' => $this->getLastCrumb()];
                }
                if (count($path) - 1 > $this->dirs[$path[0]]['depth']) {
                    return ['http_error' => 400, 'reason' => 'You\'ve gone too deep.', 'suggested_link' => $this->getLastCrumb()];
                }
                #Update breadcrumbs with subfolders
                foreach (array_slice($path, 1) as $sub_path) {
                    $this->attachCrumb($sub_path, $sub_path);
                }
                if (count($path) - 1 === $this->dirs[$path[0]]['depth']) {
                    #Get files, since we are on the last allowed level
                    $output_array = $this->listFiles($path[0], $sub_dir);
                } else {
                    #Get the list of directories
                    $output_array['files'][$path[0]] = $this->getDirs(Config::$work_dir.$this->dirs[$path[0]]['path'].$sub_dir);
                }
                $output_array['files'][$path[0]]['name'] = $this->dirs[$path[0]]['name'];
                $output_array['files'][$path[0]]['parent'] = array_slice($this->breadcrumb, -2, 1)[0];
            }
        }
        $output_array['path'] = $path[0] ?? null;
        return $output_array;
    }
    
    /**
     * Try to get the actual subdirectory path, based on an actual case of the name, and not the lower case as was passed in URL
     *
     * @param string $path    Base folder
     * @param array  $sub_dir Array of subdirectories to check
     *
     * @return string|false
     */
    private function getRealSubDirPath(string $path, array $sub_dir): string|false
    {
        $full_sub_dir = '/';
        foreach ($sub_dir as $sub_path) {
            $match = false;
            foreach (scandir($path.$full_sub_dir, SCANDIR_SORT_NONE) as $path_entry) {
                if (strcasecmp($path_entry, $sub_path) === 0) {
                    $match = true;
                    $full_sub_dir .= $path_entry.'/';
                    break;
                }
            }
            if (!$match) {
                return false;
            }
        }
        return $full_sub_dir;
    }
    
    /**
     * Generate a list of files
     *
     * @param string $path    Base path
     * @param string $sub_dir Subdirectory path, if any
     *
     * @return array
     */
    private function listFiles(string $path, string $sub_dir = ''): array
    {
        $output_array['files'][$path] = $this->getFiles(Config::$work_dir.$this->dirs[$path]['path'].$sub_dir);
        #Process pagination
        $total_pages = (int)ceil($output_array['files'][$path]['count'] / $this->list_items);
        if ($total_pages > 0 && $this->page > $total_pages) {
            #Redirect to last page
            Headers::redirect(Config::$base_url.($_SERVER['SERVER_PORT'] !== 443 ? ':'.$_SERVER['SERVER_PORT'] : '').$this->getLastCrumb().'/'.(!empty($this->search_for) ? '?search='.rawurlencode($this->search_for).'&page='.$total_pages : '?page='.$total_pages), false);
        }
        if ($output_array['files'][$path]['count'] > $this->list_items) {
            #Generate pagination data
            $output_array['pagination'] = ['current' => $this->page, 'total' => $total_pages, 'prefix' => '?page=', 'per' => $this->list_items];
            #Update the list of files by slicing
            $output_array['files'][$path]['files'] = array_slice($output_array['files'][$path]['files'], ($this->page - 1) * $this->list_items, $this->list_items);
        }
        #Get the freshest date
        if (!empty($output_array['files'][$path]['files'])) {
            $date = max(array_column($output_array['files'][$path]['files'], 'time'));
            #Attempt to exit a bit earlier with Last Modified header
            if (!empty($date)) {
                $this->lastModified($date);
            }
        }
        $output_array['files'][$path]['name'] = $this->dirs[$path]['name'];
        if (empty($sub_dir)) {
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
        return $output_array;
    }
    
    /**
     * Get a list of directories
     *
     * @param string $path       Base path
     * @param bool   $count_only Whether we just count or generate a full list with details
     *
     * @return array
     */
    private function getDirs(string $path, bool $count_only = false): array
    {
        #Order results
        /* @noinspection IteratorToArrayKeysCollisionInspection */
        $iterator = iterator_to_array(new \CallbackFilterIterator(new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), function ($cur) {
            return $cur->isDir();
        }));
        ksort($iterator, SORT_NATURAL);
        #Prepare the array
        $result = [];
        $result['dirs'] = [];
        if ($count_only) {
            $result['count'] = iterator_count($iterator);
        } else {
            foreach ($iterator as $key => $file) {
                if (!in_array($key, $this->exclude, true)) {
                    $file_details = [
                        'dirname' => $file->getFilename(),
                        #Path relative to the working directory
                        'path' => str_replace(Config::$work_dir, '', $file->getPath()),
                        'time' => $file->getMTime(),
                    ];
                    $result['dirs'][] = $file_details;
                }
            }
            $result['count'] = count($result['dirs']);
        }
        return $result;
    }
    
    /**
     * Get a list of files
     *
     * @param string $path       Base path
     * @param bool   $count_only Whether we just count or generate a full list with details
     *
     * @return array
     */
    protected function getFiles(string $path, bool $count_only = false): array
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
        /* @noinspection IteratorToArrayKeysCollisionInspection */
        $iterator = iterator_to_array($iterator);
        ksort($iterator, SORT_NATURAL);
        #Prepare the array
        $result = [];
        $result['files'] = [];
        if ($count_only) {
            $result['count'] = iterator_count($iterator);
        } else {
            $id = 1;
            foreach ($iterator as $key => $file) {
                if (!in_array($key, $this->exclude, true) && (empty($this->search_for) || mb_stripos($key, $this->search_for, 0, 'UTF-8') !== false)) {
                    if ($id >= (($this->page - 1) * $this->list_items + 1) && $id <= ($this->page * $this->list_items)) {
                        $file_details = [
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
                        $this->extra($file_details);
                    } else {
                        $file_details = [
                            'key' => $id++,
                        ];
                    }
                    $result['files'][] = $file_details;
                }
            }
            $result['count'] = count($result['files']);
        }
        return $result;
    }
    
    /**
     * Override the function to do some extra processing over the array
     *
     * @param array $file_details
     *
     * @return void
     */
    protected function extra(array &$file_details): void {}
}
