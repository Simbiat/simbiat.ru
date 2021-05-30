<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Headers;

class HomeApi
{
    /**
     * @throws \Exception
     */
    public function uriParse(array $uri): array
    {
        #Check if uri is empty
        if (empty($uri)) {
            $this->apiEcho(httpCode: '400');
        }
        $uri[0] = strtolower($uri[0]);
        switch ($uri[0]){
            case 'bictracker':
                $data = $this->bicTracker(array_slice($uri, 1));
                break;
            case 'fftracker':
                $data = $this->ffTracker(array_slice($uri, 1));
                break;
            case 'cron':
                (new HomePage)->dbConnect();
                (new Cron)->process();
                exit;
            default:
                #Not supported (yet)
                $this->apiEcho(httpCode: '404');
                break;
        }
        #Send data
        if (empty($data)) {
            $this->apiEcho(httpCode: '204');
        } else {
            $this->apiEcho($data);
        }
        return [];
    }

    #Process FFTracker

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Exception
     */
    private function ffTracker(array $uri): string|array|bool
    {
        #Check that next value is appropriate
        if (empty($uri[0])) {
            $this->apiEcho(httpCode: '400');
        }
        $uri[0] = strtolower($uri[0]);
        #Check if supported
        if (!in_array($uri[0], ['register', 'achievement', 'character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam'])) {
            $this->apiEcho(httpCode: '404');
        }
        #Check that data was provided
        if (empty($uri[1])) {
            $this->apiEcho(httpCode: '400');
        }
        #Connect to DB
        if ((new HomePage)->dbConnect() === false) {
            $this->apiEcho(httpCode: '503');
        }
        $fftracker = (new FFTracker);
        if ($uri[0] === 'register') {
            $data = $fftracker->Update(rawurldecode($uri[1]), '');
        } else {
            #Handle force update
            if (in_array($uri[0], ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam'])) {
                #Check if force update was requested
                if (!empty($uri[2]) && in_array(strtolower($uri[2]), ['force', 'update'])) {
                    #Update the entity
                    $data = $fftracker->Update(rawurldecode($uri[1]), $uri[0]);
                    #If $data value is not a supported one, means that the entity does not exist, so we can exist earlier
                    if (!in_array($data, ['character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'pvpteam'])) {
                        $this->apiEcho(httpCode: '404');
                    }
                }
            }
            #Get data
            $data = $fftracker->TrackerGrab($uri[0], rawurldecode($uri[1]));
            #Check if empty
            if (empty($data)) {
                $this->apiEcho(httpCode: '404');
            } else {
                #Send additional headers
                $headers = (new Headers);
                $headers->lastModified($data['updated'], true);
                $headers->links([['rel' => 'alternate', 'type' => 'text/html', 'title' => 'HTML representation', 'href' => '/fftracker/'.$uri[0].'/'.$uri[1]]]);
            }
        }
        #Send data
        return $data;
    }

    #Process BICTracker
    private function bicTracker(array $uri): array|bool|int
    {
        #Check that next value is appropriate
        if (empty($uri[0])) {
            $this->apiEcho(httpCode: '400');
        }
        $uri[0] = strtolower($uri[0]);
        #Check if supported
        if (!in_array($uri[0], ['bic', 'accheck'])) {
            $this->apiEcho(httpCode: '404');
        }
        #Check that data was provided
        if (($uri[0] === 'bic' && empty($uri[1])) || ($uri[0] === 'accheck' && (empty($uri[1]) || empty($uri[2])))) {
            $this->apiEcho(httpCode: '400');
        }
        if ($uri[0] === 'bic') {
            #Connect to DB
            if ((new HomePage)->dbConnect()) {
                #Get data
                $data = (new bicXML)->getCurrent(rawurldecode($uri[1]));
                #Check if empty
                if (empty($data)) {
                    $this->apiEcho(httpCode: '404');
                } else {
                    #Send additional headers
                    $headers = (new Headers);
                    $headers->lastModified(strtotime($data['DT_IZM']), true);
                    $headers->links([['rel' => 'alternate', 'type' => 'text/html', 'title' => 'HTML representation', 'href' => '/bictracker/bic/'.$uri[1]]]);
                }
                #Send data
                return $data;
            } else {
                $this->apiEcho(httpCode: '503');
            }
        } elseif ($uri[0] === 'accheck') {
            return (new AccountKeying)->accCheck($uri[1], $uri[2]);
        }
        return [];
    }

    #Function to send the data to client in JSON format with appropriate HTTP status code
    private function apiEcho(mixed $data = NULL, string $httpCode = '200'): void
    {
        #Convert data to JSON
        $data = json_encode($data, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        #Send HTTP code to client
        (new Headers)->clientReturn($httpCode, false);
        #Send content-type
        header('Content-Type: application/json; charset=utf-8');
        #Send data
        (new Common)->zEcho($data);
        exit;
    }
}
