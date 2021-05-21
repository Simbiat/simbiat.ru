<?php
declare(strict_types=1);

#To run before upload to prod, but after the images are downloaded from prod
#set_time_limit(3000000);
#foreach (new FilesystemIterator('C:\Users\simbi\OneDrive\Documents\!Personal\Coding\WebServer\htdocs\lib\FFTracker\src\Images\merged-crests') as $fileInfo) {
#    $filename = $fileInfo->getFilename();
#    $fullname = $fileInfo->getPathname();
#    $dir = $fileInfo->getPath().'/'.substr($filename, 0, 2).'/'.substr($filename, 2, 2);
#    if (!is_dir($dir)) {
#        mkdir($dir, recursive: true);
#    }
#    if ($fullname !== $dir.'/'.$filename) {
#        if (!is_file($dir.'/'.$filename)) {
#            rename($fullname, $dir.'/'.$filename);
#        } else {
#            unlink($fullname);
#        }
#    }
#    echo $fullname . "<br>\n";
#}
#exit;


#Load composer libraries
require __DIR__. '/composer/vendor/autoload.php';

#Get config file
require_once __DIR__. '/config.php';

#################
#Load old classes. To be removed in future
#################
#set_include_path(get_include_path().PATH_SEPARATOR.__DIR__ . '/backend');
#spl_autoload_register(function ($class_name) {include str_replace('\\', '/', $class_name) . '.php';});

#Determine if test server to enable or disable deisplay_errors
if (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'local.simbiat.ru' && $_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR']) {
    $HomePage = new \Simbiat\HomePage(false);
} else {
    $HomePage = new \Simbiat\HomePage(true);
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
    (new \Simbiat\Cron)->process(50);
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
                #Process API request
                (new \Simbiat\HomeApi)->uriParse(array_slice($uri, 1));
                #Ensure we no matter what happens in API gateway
                exit;
            } else {
                #Send links
                $HomePage->commonLinks();
                #Connect to DB
                if ($HomePage->dbConnect(true)) {
                    $vars = match(strtolower($uri[0])) {
                        #Forum/Articles
                        #use https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Language to specify audience for russian posts
                        'forum' => (new \Simbiat\colloquium\Show)->forum($uri),
                        'thread' => (new \Simbiat\colloquium\Show)->thread($uri),
                        #FFTracker
                        'fftracker' => (new \Simbiat\HomeRouter)->fftracker(array_slice($uri, 1)),
                        #BIC Tracker
                        'bictracker' => (new \Simbiat\HomeRouter)->bictracker(array_slice($uri, 1)),
                        #User control
                        'uc' => (new \Simbiat\HomeRouter)->usercontrol(array_slice($uri, 1)),
                        #Silversteam
                        'ssc', 'silversteam', 'darksteam' => [
                            'service_name' => 'silversteam',
                        ],
                        #Feeds
                        'sitemap', 'rss', 'atom' => (new \Simbiat\HomeFeeds)->uriParse($uri),
                        #Page not found
                        default => [
                            'http_error' => 404,
                        ],
                    };
                } else {
                    $vars = [];
                }
                #Generate page
                $output = $HomePage->twigProc($vars, (empty($vars['http_error']) ? NULL : $vars['http_error']));
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
                'service_name' => 'landing',
                'notice' => (new \Simbiat\Database\Controller)->selectAll('SELECT `text` FROM `forum__thread` ORDER BY `date` DESC LIMIT 1')[0]['text'],
            ];
        } else {
            $vars = [];
        }
        #Generate page
        $output = $HomePage->twigProc($vars);
    }
}
exit;

###################
#Test lodestonephp#
###################
#$Lodestone = (new \Simbiat\Lodestone)->setLanguage('eu')->setUseragent('Simbiat Software');
#$data = $Lodestone->searchLinkshell()->getResult();
#$data = $Lodestone->searchDatabase('duty', 2)->getResult();
#$data = $Lodestone->searchDatabase('achievement', 0, 0, 'hit the floor')->getResult();
#$data = $Lodestone->getCharacter('6691027')->getResult();
#$data = $Lodestone->getCharacterAchievements('6691027', false, 39, true, false)->getResult();
#$data = $Lodestone->getCharacter('11164476')->getResult();
#$data = $Lodestone->getCharacter('26370203')->getResult();
#$data = $Lodestone->getLinkshellMembers('22517998136956039')->getResult();
#$data = $Lodestone->getLinkshellMembers('22517998136982037')->getResult();
#$data = $Lodestone->getLinkshellMembers('6e125c2e2f7b9e3d4dbfb2ab7190c32c36b930c5')->getResult();
#$data = $Lodestone->getPvPTeam('86886213d32985bcb85a157ccaa1fa8d6e7c37c0')->getResult();
#$data = $Lodestone->getPvPTeam('c036a9c564d1818cde15fc40db01be8fc4a88612')->getResult();
#$data = $Lodestone->getFreeCompanyMembers('9229001536389081054')->getResult();
#$data = $Lodestone->getFreeCompany('9234631035923213559')->getResult();
#$data = $Lodestone->getFreeCompany('9233364398528123167')->getResult();
#$data = $Lodestone->getFreeCompany('9233786610993180104')->getResult();
#$data = $Lodestone->getFreeCompany('9232097761132923243')->getFreeCompanyMembers('9232097761132923243')->getResult();
#$data = $Lodestone->getFreeCompanyMembers('9234631035923213559')->getResult();
#$data = $Lodestone->getFreeCompanyMembers('9228016373970503975')->getResult();
#$data = $Lodestone->getWorldStatus(true)->getResult();
#$data = $Lodestone->getDeepDungeon(2, '', '', '')->getResult();
#$data = $Lodestone->getLinkshellMembers('1', 1)->getResult();
#$data = $Lodestone->getLinkshellMembers('c4fac4f1cd085af406cc0b0bfe05f4d51e4a2f90')->getResult();
#echo count($data['database']['achievement']).'<br>';
#echo '<pre>'.var_export($data, true).'</pre>';
#new \Simbiat\LodestoneTest;
#exit;


#FFTracker test
#(new Simbiat\FFTracker)->UpdateAchievements();
#$data = (new Simbiat\FFTracker)->TrackerGrab('6691027', 'character');
#$data = (new Simbiat\FFTracker)->Update('6691027', 'character');
#$data = (new Simbiat\FFTracker)->Update('9229001536389081054', 'freecompany');
#$data = (new Simbiat\FFTracker)->Update('0d4e67e9e86fe7ccabc2324d93babb69d506ba61', 'pvpteam');
#$data = (new Simbiat\FFTracker)->Update('22517998136982037', 'linkshell');
#$data = (new Simbiat\FFTracker)->Update('6e125c2e2f7b9e3d4dbfb2ab7190c32c36b930c5', 'crossworld_linkshell');
#$data = (new Simbiat\FFTracker)->Update('86886213d32985bcb85a157ccaa1fa8d6e7c37c0', 'pvpteam');
#$data = (new Simbiat\FFTracker)->Update('0a28be21f240edfc2f73f97c8f03c4997b360bdb', 'pvpteam');
#(new Simbiat\FFTracker)->CronProcess();
#echo '<pre>'.var_export($data, true).'</pre>';
#exit;

#Actions only if no error
if ($twigparameters['error'] !== true) {

    #Device detection and customization
    $twigparameters = devicedetector($twigparameters);
    
    if ((empty(@$uri[1]) || (!empty(@$uri[1]) && @$uri[1] !== 'atom'))) {
        $twigparameters['XCsrftoken'] = (new \Simbiat\Common\Security)->csrftoken(@$uri);
        $twigparameters = (new \Simbiat\UserSystem\UserDetails)->sessiontwig($twigparameters);
        if (!empty($twigparameters['timezone'])) {
            $twig->getExtension('Twig_Extension_Core')->setTimezone($twigparameters['timezone']);
        }
    }
    
    #Meta description
    if (in_array(@$uri[0], ['admin', 'user'])) {
        $twigparameters['h1'] = match(@$uri[0]) {
            'admin' => 'Admin Panel',
            'user' => 'User Page',
        };
        $twigparameters['title'] = match(@$uri[0]) {
            'admin' => 'Admin Panel'.' on '.$twigparameters['title'],
            'user' => 'User Page'.' on '.$twigparameters['title'],
        };
        $twigparameters['ogdesc'] = match(@$uri[0]) {
            'admin' => 'Admin Panel'.' by '.$twigparameters['site_name'],
            'user' => 'User Page'.' by '.$twigparameters['site_name'],
        };
    }
    #Determine what page to load
    switch (@$uri[0]) {
        case 'admin':
            $twigparameters = (new \Simbiat\Services\Admin())->PageGenerate(@$uri, $twigparameters);
            break;
        case 'user':
            $twigparameters = (new \Simbiat\UserSystem\UserCP())->PageGenerate(@$uri, $twigparameters);
            break;
        case 'posttest':
            $twigparameters['h1'] = 'WYSIWIG test';
            $twigparameters['content'] = $twig->render('wysiwyg.html', $twigparameters);
            break;
        case 'logout':
            (new \Simbiat\UserSystem\Api)->apiparse(array('','','logout'));
            header('Location: ' . 'https://' . $_SERVER['HTTP_HOST'], true, 302);
            exit;
    }
}


#Output the page
if (@$uri[0] != 'api') {
    #Admincp and Usercp
    if (!empty($_SESSION['userid'])) {
        $twigparameters['usercp'] = $_SESSION['userid'];
    }
    if (!empty($_SESSION['usergroups'])) {
        if (in_array(1, array_column($_SESSION['usergroups'], 'groupid'))) {
            $twigparameters['admincp'] = true;
        }
    }
}
exit;
?>