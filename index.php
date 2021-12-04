<?php
#Suppressing unhandled exceptions, since they are meant to be handled inside respective functions
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

require __DIR__. '/composer/vendor/autoload.php';

#PSR-4 Autoloader for own libraries
spl_autoload_register(function ($class) {
    #Project's prefix
    $prefix = 'Simbiat\\';
    #Generate list of folders to search in
    $libraries = glob(__DIR__.'/lib/*', GLOB_ONLYDIR);
    #Check if class uses the prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        #No, move to the next registered autoloader
        return;
    }
    #Get the relative class name
    $relative_class = substr($class, $len);
    #Itterate over libraries
    foreach ($libraries as $dir) {
        #Set file path
        $file = $dir.'/src/'.str_replace('\\', '/', $relative_class) . '.php';
        #Check if file exists
        if (file_exists($file)) {
            #Require file
            require $file;
        }
    }
});

#Load composer libraries
use Simbiat\Cron;
use Simbiat\Database\Controller;
use Simbiat\Api;
use Simbiat\HomePage;
use Simbiat\MainRouter;

#Get config file
require_once __DIR__. '/config.php';

#################
#Load old classes. To be removed in future
#################
#set_include_path(get_include_path().PATH_SEPARATOR.__DIR__ . '/backend');
#spl_autoload_register(function ($class_name) {include str_replace('\\', '/', $class_name) . '.php';});

#Determine if test server to enable or disable display_errors
if (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'local.simbiat.ru' && $_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR']) {
    $HomePage = new HomePage(false);
} else {
    $HomePage = new HomePage(true);
}

#Check if we are in CLI
if (preg_match('/^cli(-server)?$/i', php_sapi_name()) === 1) {
    $CLI = true;
} else {
    $CLI = false;
}

#If not CLI - do redirects and other HTTP-related stuff
if ($CLI) {
    #Process Cron
    $HomePage->dbConnect(false);
    (new Cron)->process(50);
    #Ensure we exit no matter what happens with CRON
    exit;
} else {
    $HomePage->canonical();
    #Send common headers
    $HomePage->commonHeaders();
    #Process requests to files
    if (!empty($_SERVER['REQUEST_URI'])) {
        $fileResult = $HomePage->filesRequests($_SERVER['REQUEST_URI']);
        if ($fileResult === 200) {
            exit;
        } else {
            #Send HTML specific headers
            $HomePage->htmlHeaders();
            #Exploding further processing
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            #Check if API
            if (strcasecmp($uri[0], 'api') === 0) {
                try {
                    #Process API request
                    (new Api)->uriParse(array_slice($uri, 1));
                } catch (Throwable $throwable) {
                    error_log('Failed on API call `'.$_SERVER['REQUEST_URI'].'`'."\r\n".$throwable->getMessage()."\r\n".$throwable->getTraceAsString());
                }
                #Ensure we exit no matter what happens in API gateway
                exit;
            } else {
                try {
                    #Send links
                    $HomePage->commonLinks();
                    #Connect to DB
                    if ($HomePage->dbConnect(true) || preg_match($GLOBALS['siteconfig']['static_pages'], $_SERVER['REQUEST_URI']) === 1) {
                        $vars = (new MainRouter)->route($uri);
                    } else {
                        $vars = [];
                    }
                } catch (Throwable $throwable) {
                    error_log('Failed to generate page `'.$_SERVER['REQUEST_URI'].'`'."\r\n".$throwable->getMessage()."\r\n".$throwable->getTraceAsString());
                    $vars = ['http_error' => 500];
                }
                #Generate page
                $HomePage->twigProc($vars, (empty($vars['http_error']) ? null : $vars['http_error']));
            }
        }
    } else {
        #Send HTML specific headers
        $HomePage->htmlHeaders();
        #Send links
        $HomePage->commonLinks();
        #Connect to DB
        if ($HomePage->dbConnect(true)) {
            $vars = [
                'h1' => 'Home',
                'serviceName' => 'landing',
                'notice' => (new Controller)->selectAll('SELECT `text` FROM `forum__thread` ORDER BY `date` DESC LIMIT 1')[0]['text'],
            ];
        } else {
            $vars = [];
        }
        #Generate page
        $HomePage->twigProc($vars);
    }
}
exit;
