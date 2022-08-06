<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\HTML;

class Router_bck
{
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
            $headers->redirect(Common::$baseUrl.($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/search', true, true, false);
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
                    $headers->redirect(Common::$baseUrl.($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/fftracker/statistics/genetics', true, true, false);
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
