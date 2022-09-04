<?php
declare(strict_types=1);
namespace Simbiat\Abstracts\Pages;

use Simbiat\Config\Common;

class FileListing extends StaticPage
{
    #Flag to indicate this is a static page
    protected bool $static = true;
    #Cache age set to for a day
    protected int $cacheAge = 1440;
    #Cache strategy: aggressive, private, live, month, week, day, hour
    protected string $cacheStrat = 'week';
    #Directories relative to working dir
    #Expected format: ['URL_path_name' => ['dir' => 'path', 'name' => 'name to use in UI']]
    protected array $dirs = [];
    #Items to display per page for lists
    protected int $listItems = 100;
    #Flag whether to go recursive or not
    protected bool $recursive = false;
    #List of files that should be excluded
    protected array $exclude = [];

    #Static pages have all the data in Twig templates, thus we just return empty array
    protected function generate(array $path): array
    {
        $outputArray = [];
        if (empty($this->dirs)) {
            return ['http_error' => 503, 'reason' => 'No directories are setup for this endpoint'];
        }
        if (empty($path[0])) {
            #Get files' counts
            foreach ($this->dirs as $key=>$dir) {
                $outputArray['files'][$key] = $this->getFiles(Common::$workDir.$dir['dir'], true);
                $outputArray['files'][$key]['name'] = $dir['name'];
            }
        } else {
            if (!in_array($path[0], array_keys($this->dirs))) {
                return ['http_error' => 404, 'suggested_link' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href']];
            }
            $outputArray['files'][$path[0]] = $this->getFiles(Common::$workDir.$this->dirs[$path[0]]['dir']);
            #Process pagination
            $total = count($outputArray['files'][$path[0]]['files']);
            if ($total > $this->listItems) {
                #Set page number
                $page = intval($_GET['page'] ?? 1);
                if ($page < 1) {
                    $page = 1;
                }
                $totalPages = intval(ceil($total/$this->listItems));
                #Generate pagination data
                $outputArray['pagination'] = ['current' => $page, 'total' => $totalPages, 'prefix' => '?page='];
                #Update list of files by slicing
                $outputArray['files'][$path[0]]['files'] = array_slice($outputArray['files'][$path[0]]['files'], ($page - 1)*$this->listItems, $this->listItems);
            }
            #Get the freshest date
            $date = max(array_column($outputArray['files'][$path[0]]['files'], 'time'));
            #Attempt to exit a bit earlier with Last Modified header
            if (!empty($date)) {
                $this->lastModified($date);
            }
            $outputArray['files'][$path[0]]['name'] = $this->dirs[$path[0]]['name'];
            #Add path to breadcrumbs
            $this->breadCrumb[] = [
                'href' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href'].'/'.$path[0],
                'name' => $this->dirs[$path[0]]['name'],
            ];
            #Update title and H1
            $this->title = $this->dirs[$path[0]]['name'].' from '.$this->title;
            $this->h1 = $this->dirs[$path[0]]['name'];
        }
        $outputArray['path'] = $path[0] ?? null;
        return $outputArray;
    }
    
    private function getFiles(string $path, bool $countOnly = false): array
    {
        if ($this->recursive) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        } else {
            $iterator = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS);
        }
        #Prepare array
        $result = [];
        $result['files'] = [];
        if (!$countOnly) {
            $id = 1;
            foreach ($iterator as $key=>$file) {
                if (!in_array($key, $this->exclude)) {
                    $fileDetails = [
                        'filename' => $key,
                        'basename' => $file->getBasename('.'.$file->getExtension()),
                        #Path relative to working directory
                        'path' => str_replace(Common::$workDir, '', $file->getPath()),
                        'mime' => mime_content_type($file->getPathname()),
                        'size' => $file->getSize(),
                        'time' => $file->getMTime(),
                        'key' => $id++,
                    ];
                    #Extra processing, if any is required
                    $this->extra($fileDetails);
                    $result['files'][] = $fileDetails;
                }
            }
        }
        if (count($result['files']) > 0) {
            #In case we excluded some files
            $result['count'] = min(iterator_count($iterator), count($result['files']));
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
