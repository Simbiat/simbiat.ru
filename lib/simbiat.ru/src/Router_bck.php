<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;

class Router_bck
{
    #Function to prepare data for user control pages
    public function usercontrol(array $uri): array
    {
        $headers = (new Headers);
        $html = (new HTML);
        #Check if URI is empty
        if (empty($uri)) {
            $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/uc/registration', true, true, false);
        }
        #Prepare array
        $outputArray = [
            'serviceName' => 'usercontrol',
            'h1' => 'User Control',
            'title' => 'User Control',
            'ogdesc' => 'User\'s Control Panel',
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
        ];
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Process search page
            case 'registration':
            case 'register':
            case 'login':
            case 'signin':
            case 'signup':
            case 'join':
                if (empty($_SESSION['username'])) {
                    $outputArray['subServiceName'] = 'registration';
                    $outputArray['h1'] = $outputArray['title'] = 'User sign in/join';
                } else {
                    #Redirect to main page if user is already authenticated
                    $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : ''), false, true, false);
                }
                break;
            default:
                $outputArray['http_error'] = 404;
                break;
        }
        #Add breadcrumbs
        $outputArray['breadcrumbs'] = $html->breadcrumbs($breadArray);
        return $outputArray;
    }

    #Function to route tests
    public function tests(array $uri): array
    {
        $outputArray = [];
        #Forbid if on PROD
        if (HomePage::$PROD === true || empty($uri)) {
            $outputArray['http_error'] = 403;
            return $outputArray;
        }
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Lodestone tests
            case 'lodestone':
                if (empty($uri[1])) {
                    (new Tests)->ffTest(true);
                    exit;
                }
                $uri[1] = strtolower($uri[1]);
                switch ($uri[1]) {
                    case 'full':
                        (new Tests)->ffTest(true);
                        exit;
                    case 'freecompany':
                    case 'linkshell':
                    case 'pvpteam':
                    case 'character':
                        (new Tests)->ffTest(false, $uri[1], $uri[2] ?? '');
                        exit;
                }
                break;
            case 'optimize':
                (new Tests)->testDump((new optimizeTables)->setMaintenance("sys__settings","setting","maintenance","value")->setJsonPath('./data/tables.json')->optimize('simbiatr_simbiat', true));
                exit;
        }
        $outputArray['http_error'] = 400;
        return $outputArray;
    }

    #Function to prepare data for FFTracker depending on the URI
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function fftracker(array $uri): array
    {
        $fftracker = (new FFTracker);
        $html = (new HTML);
        $headers = (new Headers);
        #Check if URI is empty
        if (empty($uri)) {
            $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/search', true, true, false);
        }
        #Prepare array
        $outputArray = [
            'serviceName' => 'fftracker',
            'h1' => 'Final Fantasy XIV Tracker',
            'title' => 'Final Fantasy XIV Tracker',
            'ogdesc' => 'Tracker for Final Fantasy XIV entities and respective statistics',
        ];
        #Start breadcrumbs
        $breadArray = [
            ['href'=>'/', 'name'=>'Home page'],
        ];
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]) {
            #Process statistics
            case 'statistics':
                #Check if type is set
                if (empty($uri[1])) {
                    $headers->redirect('https://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/statistics/genetics', true, true, false);
                } else {
                    $uri[1] = strtolower($uri[1]);
                    if (in_array($uri[1], ['genetics', 'astrology', 'characters', 'freecompanies', 'cities', 'grandcompanies', 'servers', 'achievements', 'timelines', 'other', 'bugs'])) {
                        $outputArray['subServiceName'] = 'statistics';
                        #Set statistics type
                        $outputArray['ff_stat_type'] = $uri[1];
                        #Continue breadcrumb
                        $tempName = ucfirst(preg_replace('/s$/i', 's\'', preg_replace('/companies/i', ' Companies', $uri[1])).' statistics');
                        $breadArray[] = ['href'=>'/fftracker/statistics/'.$uri[1], 'name'=>$tempName];
                        #Get the data
                        $outputArray[$uri[0]] = $fftracker->Statistics($uri[1]);
                        #Update meta
                        $outputArray['h1'] .= ': Statistics';
                        $outputArray['title'] .= ': Statistics';
                        $outputArray['ogdesc'] = $tempName.' on '.$outputArray['ogdesc'];
                        $outputArray['cacheAge'] = 86400;
                    } else {
                        $outputArray['http_error'] = 404;
                    }
                }
                break;
            default:
                $outputArray['http_error'] = 404;
                break;
        }
        #If we have 404 - generate random entities as suggestions
        if (!empty($outputArray['http_error']) && $outputArray['http_error'] === 404) {
            $outputArray['ff_suggestions'] = $fftracker->GetRandomEntities($fftracker->maxlines);
            #Cache due to random entities
            $outputArray['cacheAge'] = 86400;
        }
        #Add breadcrumbs
        $outputArray['breadcrumbs'] = $html->breadcrumbs($breadArray);
        return $outputArray;
    }

}
