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
    #Generate sitemap files (TXT and XML) with CRON
    public function generate(): bool
    {
        try {
            #Remove old text files
            array_map('unlink', glob( Common::$sitemap.'txt/*.txt'));
            array_map('unlink', glob( Common::$sitemap.'txt/*/*.txt'));
            #Set method, since router requires it
            HomePage::$method = 'GET';
            #Cache router
            $router = (new Router);
            #Generate text index file
            $twigVars = $router->route(['txt']);
            $index = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            #Write text file
            file_put_contents(Common::$sitemap.'txt/index.txt', $index);
            $links = explode("\n", $index);
            $xmlFiles = array_merge(glob( Common::$sitemap.'xml/*.xml'), glob( Common::$sitemap.'xml/*/*.xml'));
            #Remove XML files, which are no longer exist as per new index
            foreach ($xmlFiles as $file) {
                if (preg_match('/^.*xml\/index\.xml$/ui', $file) === 0 && !in_array(Common::$baseUrl.'/sitemap/'.str_replace(Common::$sitemap, '', str_replace('xml', 'txt', $file)), $links)) {
                    @unlink($file);
                }
            }
            #Write XML index
            $twigVars = $router->route(['xml']);
            $index = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            $curl = (new Curl);
            #Generate sitemaps for each link in index
            foreach ($links as $link) {
                if (!empty($link)) {
                    #Get path to use for router function (without format)
                    $path = explode('/', trim(str_replace('.txt', '', str_replace(Common::$baseUrl.'/sitemap/txt/', '', $link)), '/'));
                    foreach (['xml', 'txt'] as $format) {
                        #Get filepath and filename
                        $filePath = rtrim(Common::$sitemap.$format.'/'.implode('/', array_slice($path, 0, -1)), '/');
                        $fileName = implode('/', array_slice($path, -1)).'.'.$format;
                        #Create folder if it does not exist
                        if (!is_dir($filePath)) {
                            @mkdir($filePath);
                        }
                        #Generate and write file
                        $mapVars = $router->route(array_merge([$format], $path));
                        $content = Twig::getTwig()->render($mapVars['template_override'] ?? 'index.twig', $mapVars);
                        if ($format === 'xml') {
                            #Check if file already exists and if its contents are the same
                            if (!is_file($filePath.'/'.$fileName) || $index === file_get_contents($filePath.'/'.$fileName)) {
                                #Save the file
                                file_put_contents($filePath.'/'.$fileName, $content);
                                #Ping Google about file update
                                try {
                                    $curl->getPage('https://www.google.com/ping?sitemap='.urlencode(str_replace('txt', 'xml', $link)));
                                } catch (\Throwable) {
                                    #Do nothing, it's not critical
                                }
                            }
                        } else {
                            #For text, we just save the file
                            file_put_contents($filePath.'/'.$fileName, $content);
                        }
                    }
                }
            }
            #Check if index file already exists and if its contents are the same
            if (!is_file(Common::$sitemap.'xml/index.xml') || $index === file_get_contents(Common::$sitemap.'xml/index.xml')) {
                file_put_contents(Common::$sitemap.'xml/index.xml', $index);
                #Ping Google about index update
                try {
                    $curl->getPage('https://www.google.com/ping?sitemap='.urlencode(Common::$baseUrl.'/sitemap.xml'));
                } catch (\Throwable) {
                    #Do nothing, it's not critical
                }
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
}
