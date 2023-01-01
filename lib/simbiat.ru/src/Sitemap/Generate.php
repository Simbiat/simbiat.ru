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
            #Remove old files
            array_map('unlink', glob( Common::$sitemap.'txt/*.txt'));
            array_map('unlink', glob( Common::$sitemap.'txt/*/*.txt'));
            array_map('unlink', glob( Common::$sitemap.'xml/*.xml'));
            array_map('unlink', glob( Common::$sitemap.'xml/*/*.xml'));
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
            #Write XML index
            $twigVars = $router->route(['xml']);
            $index = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            file_put_contents(Common::$sitemap.'xml/index.xml', $index);
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
                        file_put_contents($filePath.'/'.$fileName, Twig::getTwig()->render($mapVars['template_override'] ?? 'index.twig', $mapVars));
                    }
                }
            }
            #Ping Google about update
            try {
                (new Curl)->getPage('https://www.google.com/ping?sitemap='.urlencode(Common::$baseUrl.'/sitemap/xml/index.xml'));
            } catch (\Throwable) {
                #Do nothing, it's not critical
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
}
