<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\fftracker\Entities\Achievement;
use Simbiat\fftracker\Entities\Character;
use Simbiat\fftracker\Entities\CrossworldLinkshell;
use Simbiat\fftracker\Entities\FreeCompany;
use Simbiat\fftracker\Entities\Linkshell;
use Simbiat\fftracker\Entities\PvPTeam;
use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Headers;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Api
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
        if (!isset($data)) {
            $this->apiEcho(httpCode: '204');
        } else {
            $this->apiEcho($data);
        }
        return [];
    }

    #Process FFTracker

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
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
        if (!in_array($uri[0], ['achievement', 'character', 'freecompany', 'linkshell', 'crossworldlinkshell', 'crossworld_linkshell', 'pvpteam'])) {
            $this->apiEcho(httpCode: '404');
        }
        #Check that data was provided
        if (empty($uri[1])) {
            $this->apiEcho(httpCode: '400');
        }
        if (!empty($uri[2])) {
            if (in_array(strtolower($uri[2]), ['register', 'update', 'get', 'lodestone'])){
                $uri[2] = strtolower($uri[2]);
            }
        } else {
            $uri[2] = '';
        }
        #Connect to DB
        if ((new HomePage)->dbConnect() === false) {
            $this->apiEcho(httpCode: '503');
        }
        $data = null;
        try {
            if ($uri[2] === 'update') {
                $data = match ($uri[0]) {
                    'character' => (new Character)->setId($uri[1])->update(),
                    'freecompany' => (new FreeCompany)->setId($uri[1])->update(),
                    'pvpteam' => (new PvPTeam)->setId($uri[1])->update(),
                    'linkshell' => (new Linkshell)->setId($uri[1])->update(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($uri[1])->update(),
                    'achievement' => (new Achievement)->setId($uri[1])->update(),
                };
            } else if ($uri[2] === 'register') {
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                $data = match ($uri[0]) {
                    'character' => (new Character)->setId($uri[1])->register(),
                    'freecompany' => (new FreeCompany)->setId($uri[1])->register(),
                    'pvpteam' => (new PvPTeam)->setId($uri[1])->register(),
                    'linkshell' => (new Linkshell)->setId($uri[1])->register(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($uri[1])->register(),
                    'achievement' => $this->apiEcho(httpCode: '400'),
                };
                if ($data === '404') {
                    $this->apiEcho(httpCode: '404');
                }
            } else if ($uri[2] === 'lodestone') {
                $data = match ($uri[0]) {
                    'character' => (new Character)->setId($uri[1])->getFromLodestone(),
                    'freecompany' => (new FreeCompany)->setId($uri[1])->getFromLodestone(),
                    'pvpteam' => (new PvPTeam)->setId($uri[1])->getFromLodestone(),
                    'linkshell' => (new Linkshell)->setId($uri[1])->getFromLodestone(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($uri[1])->getFromLodestone(),
                    'achievement' => (new Achievement)->setId($uri[1])->getFromLodestone(),
                };
                if (!empty($data['404']) && $data['404'] === true) {
                    $this->apiEcho(httpCode: '404');
                }
            } else {
                $data = match ($uri[0]) {
                    'character' => (new Character)->setId($uri[1])->getArray(),
                    'freecompany' => (new FreeCompany)->setId($uri[1])->getArray(),
                    'pvpteam' => (new PvPTeam)->setId($uri[1])->getArray(),
                    'linkshell' => (new Linkshell)->setId($uri[1])->getArray(),
                    'crossworldlinkshell', 'crossworld_linkshell' => (new CrossworldLinkshell)->setId($uri[1])->getArray(),
                    'achievement' => (new Achievement)->setId($uri[1])->getArray(),
                };
                if (empty($data['id'])) {
                    $this->apiEcho(httpCode: '404');
                }
            }
        } catch(\Throwable $e) {
            if (preg_match('/^ID .* has incorrect format\.$/i', $e->getMessage()) === 1) {
                $this->apiEcho(httpCode: '400');
            }
        }
        #Check if empty
        if (empty($data)) {
            $this->apiEcho(httpCode: '404');
        } else {
            #Send additional headers
            $headers = HomePage::$headers;
            if (isset($data['updated'])) {
                if (!HomePage::$staleReturn) {
                    $headers->lastModified($data['updated'], true);
                }
            }
            if (!HomePage::$staleReturn) {
                $headers->links([['rel' => 'alternate', 'type' => 'text/html', 'title' => 'HTML representation', 'href' => '/fftracker/' . $uri[0] . '/' . $uri[1]]]);
            }
        }
        #Send data
        return $data;
    }

    #Process BICTracker

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    private function bicTracker(array $uri): array|bool|int
    {
        #Check that next value is appropriate
        if (empty($uri[0])) {
            $this->apiEcho(httpCode: '400');
        }
        $uri[0] = strtolower($uri[0]);
        #Check if supported
        if (!in_array($uri[0], ['bic', 'keying', 'dbupdate'])) {
            $this->apiEcho(httpCode: '404');
        }
        #Check that data was provided
        if (($uri[0] === 'bic' && empty($uri[1])) || ($uri[0] === 'keying' && (empty($uri[1]) || empty($uri[2])))) {
            $this->apiEcho(httpCode: '400');
        }
        if ($uri[0] === 'bic') {
            #Connect to DB
            if ((new HomePage)->dbConnect()) {
                $data = null;
                #Get data
                try {
                    $data = (new bictracker\Bic)->setId($uri[1])->getArray();
                } catch(\Throwable $e) {
                    if (preg_match('/^ID .* has incorrect format\.$/i', $e->getMessage()) === 1) {
                        $this->apiEcho(httpCode: '400');
                    }
                }
                #Check if empty
                if (empty($data['id'])) {
                    $this->apiEcho(httpCode: '404');
                } else {
                    #Send additional headers
                    if (!HomePage::$staleReturn) {
                        $headers = HomePage::$headers;
                        $headers->lastModified(strtotime($data['Updated']), true);
                        $headers->links([['rel' => 'alternate', 'type' => 'text/html', 'title' => 'HTML representation', 'href' => '/bictracker/bic/' . $uri[1]]]);
                    }
                }
                #Send data
                return $data;
            } else {
                $this->apiEcho(httpCode: '503');
            }
        } elseif ($uri[0] === 'keying') {
            return (new AccountKeying)->accCheck($uri[1], $uri[2]);
        } elseif ($uri[0] === 'dbupdate') {
            if ((new HomePage)->dbConnect()) {
                if ((new bictracker\Library)->update(true) === true) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->apiEcho(httpCode: '503');
            }
        }
        return [];
    }

    #Function to send the data to client in JSON format with appropriate HTTP status code
    private function apiEcho(mixed $data = NULL, string $httpCode = '200'): void
    {
        #Convert data to JSON
        $data = json_encode($data, JSON_PRETTY_PRINT|JSON_INVALID_UTF8_SUBSTITUTE|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        #Send HTTP response code to client
        if (!HomePage::$staleReturn) {
            HomePage::$headers->clientReturn($httpCode, false);
        }
        #Send content-type
        header('Content-Type: application/json; charset=utf-8');
        #Send data
        (new Common)->zEcho($data);
        exit;
    }
}
