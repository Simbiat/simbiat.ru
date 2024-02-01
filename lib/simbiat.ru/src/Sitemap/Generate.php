<?php
declare(strict_types=1);
namespace Simbiat\Sitemap;

use Simbiat\Config\Common;
use Simbiat\Config\Twig;
use Simbiat\Curl;
use Simbiat\Errors;
use Simbiat\HomePage;

class Generate
{
    private array $index_files = ['index', 'fftracker'];
    
    #Generate sitemap files (XML) with CRON
    public function generate(): bool
    {
        try {
            #Set method, since router requires it
            HomePage::$method = 'GET';
            #Cache router
            $router = (new Router);
            #Generate index files
            $index = [];
            $links = [];
            foreach ($this->index_files as $index_file) {
                $twigVars = $router->route([$index_file]);
                $index[$index_file] = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                $DomDocument = new \DOMDocument();
                $DomDocument->preserveWhiteSpace = false;
                $DomDocument->loadXML($index[$index_file]);
                $DomNodeList = $DomDocument->getElementsByTagName('loc');
                foreach($DomNodeList as $url) {
                    $links[] = $url->nodeValue;
                }
            }
            $xmlFiles = array_merge(glob( Common::$sitemap.'*.xml'), glob( Common::$sitemap.'*/*.xml'), glob( Common::$sitemap.'*/*/*.xml'));
            #Remove XML files, which are no longer exist as per new index
            foreach ($xmlFiles as $file) {
                if (!in_array(basename($file, '.xml'), $this->index_files, true) && !in_array(str_replace('.xml', '',Common::$baseUrl.'/sitemap/'.str_replace(Common::$sitemap, '', $file)), $links, true)) {
                    @unlink($file);
                }
            }
            $curl = (new Curl);
            #Generate sitemaps for each link in index
            foreach ($links as $link) {
                if (!empty($link)) {
                    #Get path to use for router function (without format)
                    $path = explode('/', trim(str_replace(Common::$baseUrl.'/sitemap/', '', $link), '/'));
                    #Strip trailing .xml extension
                    $path[array_key_last($path)] = str_replace('.xml', '', $path[array_key_last($path)]);
                    #Get filepath and filename
                    $filePath = rtrim(Common::$sitemap.implode('/', array_slice($path, 0, -1)), '/');
                    $fileName = implode('/', array_slice($path, -1));
                    #Create folder if it does not exist
                    if (!@mkdir($filePath) && !is_dir($filePath)) {
                        throw new \RuntimeException(sprintf('Directory `%s` was not created', $filePath));
                    }
                    #Generate and write file
                    $mapVars = $router->route(array_merge($path));
                    $content = Twig::getTwig()->render($mapVars['template_override'] ?? 'index.twig', $mapVars);
                    #Check if file already exists and if its contents are the same
                    if (!is_file($filePath.'/'.$fileName) || $content !== file_get_contents($filePath.'/'.$fileName) || filemtime($filePath.'/'.$fileName) < strtotime('-2 weeks')) {
                        #Save the file
                        file_put_contents($filePath.'/'.$fileName.'.xml', $content);
                        #Ping Google about file update
                        try {
                            $curl->getPage('https://www.google.com/ping?sitemap='.urlencode($link));
                        } catch (\Throwable) {
                            #Do nothing, it's not critical
                        }
                    }
                }
            }
            #Write XML index
            foreach ($this->index_files as $index_file) {
                #Check if index file already exists and if its contents are the same
                if (!is_file(Common::$sitemap.$index_file.'.xml') || $index[$index_file] !== file_get_contents(Common::$sitemap.$index_file.'.xml') || filemtime(Common::$sitemap.$index_file.'.xml') < strtotime('-2 weeks')) {
                    file_put_contents(Common::$sitemap.$index_file.'.xml', $index[$index_file]);
                    #Ping Google about index update
                    try {
                        $curl->getPage('https://www.google.com/ping?sitemap='.urlencode(Common::$baseUrl.'/sitemap/'.$index_file.'.xml'));
                    } catch (\Throwable) {
                        #Do nothing, it's not critical
                    }
                }
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
}
