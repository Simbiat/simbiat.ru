<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Api;

use Simbiat\Abstracts\Api;
use Simbiat\HomePage;

abstract class General extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => '', 'PUT' => 'update', 'POST' => 'register'];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['update' => 'Attempt updating entity', 'register' => 'Attempt to register entity to tracker', 'lodestone' => 'Show data grabbed directly from Lodestone'];
    #Entity class name
    protected string $entityClass = '';
    #Name to show in errors
    protected string $nameForErrors = '';
    #Name for links
    protected string $nameForLinks = '';

    protected function genData(array $path): array
    {
        #Reset verb for consistency, if it's not set
        if (empty($path[1])) {
            $path[1] = '';
        }
        try {
            if ($this->nameForLinks === 'achievement') {
                $data = match ($path[1]) {
                    'update' => (new \Simbiat\fftracker\Entities\Achievement())->setId($path[0])->update(),
                    'lodestone' => (new \Simbiat\fftracker\Entities\Achievement)->setId($path[0])->getFromLodestone(),
                    default => (new \Simbiat\fftracker\Entities\Achievement)->setId($path[0])->getArray(),
                };
            } else {
                $data = match ($path[1]) {
                    'update' => (new $this->entityClass)->setId($path[0])->update(),
                    'register' => (new $this->entityClass)->setId($path[0])->register(),
                    'lodestone' => (new $this->entityClass)->setId($path[0])->getFromLodestone(),
                    default => (new $this->entityClass)->setId($path[0])->getArray(),
                };
            }
        } catch (\UnexpectedValueException) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        } catch (\Throwable) {
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        #Check if 404
        if ($data === 404 || (!empty($data['404']) && $data['404'] === true)) {
            return ['http_error' => 404, 'reason' => $this->nameForErrors.' with ID `'.$path[0].'` is not found on Lodestone'];
        } elseif ($data === 400) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        } elseif ($data === 500 || $data === false) {
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        } elseif ($data === 403) {
            return ['http_error' => 403, 'reason' => 'ID `'.$path[0].'` is already registered'];
        } elseif ($data !== true && empty($data['id'])) {
            return ['http_error' => 404, 'reason' => $this->nameForErrors.' with ID `'.$path[0].'` is not found on Tracker'];
        }
        if (!empty($data['dates']['updated'])) {
            HomePage::$headers->lastModified($data['dates']['updated'], true);
        }
        $result = ['response' => $data];
        #Return 201 if we were registering an entity
        if ($path[1] === 'register' && $data === true) {
            $result['status'] = 201;
        }
        #Link header/tag for API
        $result['alt_links'] = [
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/fftracker/'.$this->nameForLinks.'/' . $path[0]],
        ];
        if (empty($data['dates']['deleted'])) {
            if ($path[1] !== 'lodestone') {
                $result['alt_links'][] = ['type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.$this->nameForLinks.'/' . $path[0] . '/lodestone'];
            }
            if ($path[1] === 'update' || $path[1] === 'register') {
                $result['alt_links'][] = ['type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/'.$this->nameForLinks.'/' . $path[0]];
            }
            if ($this->nameForLinks === 'achievement') {
                if (!empty($data['dbid'])) {
                    $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/' .$data['dbid']];
                }
                if (!empty($data['rewards']['item']['id'])) {
                    $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page of the reward item', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/' .$data['rewards']['item']['id']];
                }
            } else {
                $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/' . $this->nameForLinks . '/' . $path[0]];
            }
        }
        if ($path[1] === 'lodestone') {
            $result['cacheAge'] = 1440;
        }
        return $result;
    }
}
