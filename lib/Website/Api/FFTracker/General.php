<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

use Simbiat\http20\Headers;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Errors;

abstract class General extends Api
{
    #Flag to indicate that this is the lowest level
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
        #Reset verb for consistency if it's not set
        if (empty($path[1])) {
            $path[1] = '';
        }
        try {
            if ($this->nameForLinks === 'achievement') {
                $data = match ($path[1]) {
                    'update' => new \Simbiat\FFXIV\Achievement($path[0])->updateFromApi(),
                    'lodestone' => new \Simbiat\FFXIV\Achievement($path[0])->getFromLodestone(),
                    default => new \Simbiat\FFXIV\Achievement($path[0])->getArray(),
                };
            } else {
                $data = match ($path[1]) {
                    'update' => new $this->entityClass()->setId($path[0])->updateFromApi(),
                    'register' => new $this->entityClass()->setId($path[0])->register(),
                    'lodestone' => new $this->entityClass()->setId($path[0])->getFromLodestone(),
                    default => new $this->entityClass()->setId($path[0])->getArray(),
                };
            }
        } catch (\UnexpectedValueException) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        #Check for errors
        if (!empty($data['http_error'])) {
            return $data;
        }
        if ($data === 404 || (!empty($data['404']) && $data['404'] === true)) {
            return ['http_error' => 404, 'reason' => $data['reason'] ?? ($this->nameForErrors.' with ID `'.$path[0].'` is not found on Lodestone')];
        }
        if ($data === 400) {
            return ['http_error' => 400, 'reason' => 'ID `'.$path[0].'` has unsupported format'];
        }
        if ($data === 500 || $data === false) {
            return ['http_error' => 500, 'reason' => 'Unknown error during request processing'];
        }
        if ($this->nameForLinks === 'character' && isset($data['private']) && $data['private'] === true) {
            return ['http_error' => 403, 'reason' => 'Character marked as private on Lodestone'];
        }
        if ($data === 403 && \in_array($this->nameForLinks, ['linkshell', 'crossworld_linkshell', 'crossworldlinkshell'])) {
            return ['http_error' => 403, 'reason' => $this->nameForErrors.' has empty page on Lodestone'];
        }
        if ($data === 409) {
            return ['http_error' => 409, 'reason' => 'ID `'.$path[0].'` is already registered', 'location' => '/fftracker/'.($this->nameForLinks === 'freecompany' ? 'freecompanies' : $this->nameForLinks.'s').'/'.$path[0]];
        }
        if ($data !== true && empty($data['id'])) {
            if ($path[1] === 'lodestone') {
                return ['http_error' => 500, 'reason' => 'Failed to get '.mb_strtolower($this->nameForErrors, 'UTF-8').' with ID `'.$path[0].'` from Lodestone'];
            }
            return ['http_error' => 404, 'reason' => $this->nameForErrors.' with ID `'.$path[0].'` is not found on Tracker'];
        }
        if (!empty($data['dates']['updated'])) {
            Headers::lastModified($data['dates']['updated'], true);
        }
        $result = ['response' => $data];
        #Return 201 if we were registering an entity
        if ($path[1] === 'register' && $data === true) {
            $result['location'] = '/fftracker/'.($this->nameForLinks === 'freecompany' ? 'freecompanies' : $this->nameForLinks.'s').'/'.$path[0];
            $result['status'] = 201;
        }
        #Link header/tag for API
        $result['alt_links'] = [
            ['type' => 'text/html', 'title' => 'Main page on Tracker', 'href' => '/fftracker/'.($this->nameForLinks === 'freecompany' ? 'freecompanies' : $this->nameForLinks.'s').'/'.$path[0]],
        ];
        if (empty($data['dates']['deleted'])) {
            if ($path[1] !== 'lodestone') {
                $result['alt_links'][] = ['type' => 'application/json', 'title' => 'JSON representation of Lodestone data', 'href' => '/api/fftracker/'.($this->nameForLinks === 'freecompany' ? 'freecompanies' : $this->nameForLinks.'s').'/'.$path[0].'/lodestone'];
            }
            if ($path[1] === 'update' || $path[1] === 'register') {
                $result['alt_links'][] = ['type' => 'application/json', 'title' => 'JSON representation of Tracker data', 'href' => '/api/fftracker/'.($this->nameForLinks === 'freecompany' ? 'freecompanies' : $this->nameForLinks.'s').'/'.$path[0]];
            }
            if ($this->nameForLinks === 'achievement') {
                if (!empty($data['db_id'])) {
                    $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/achievement/'.$data['db_id']];
                }
                if (!empty($data['rewards']['item']['id'])) {
                    $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page of the reward item', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/playguide/db/item/'.$data['rewards']['item']['id']];
                }
            } else {
                $result['alt_links'][] = ['type' => 'text/html', 'title' => 'Lodestone EU page', 'href' => 'https://eu.finalfantasyxiv.com/lodestone/'.$this->nameForLinks.'/'.$path[0]];
            }
        }
        if ($path[1] === 'lodestone') {
            $result['cacheAge'] = 1440;
        }
        return $result;
    }
}