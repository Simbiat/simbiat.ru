<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\HomePage;

abstract class Api
{
    #Supported edges
    protected array $subRoutes = [];
    #Description of the nodes (need to be in same order)
    protected array $routesDesc = [];
    #Flag to indicate, that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = false;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => ''];
    #Flag to indicate, that no database is required for this node
    protected bool $static = false;
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 0;

    #This is general routing check for supported node
    public final function route(array $path): array
    {
        if ($this->topLevel) {
            #Send headers
            @header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
            @header('Allow: GET, HEAD, OPTIONS');
            @header('Content-Type: application/json; charset=utf-8');
        }
        if (empty($path[0]) || (!$this->finalNode && !in_array($path[0], $this->subRoutes))) {
            $data = ['http_error' => 400, 'endpoints' => array_combine($this->subRoutes, $this->routesDesc)];
        } else {
            $data = $this->getData($path);
        }
        if ($this->topLevel) {
            #Override template
            $result['template_override'] = 'common/pages/api.twig';
            #Prepare JSON output
            $result['json_ready'] = ['status' => 200];
            if (!empty($data['cacheAge'])) {
                $result['cacheAge'] = $data['cacheAge'];
            }
            if (!empty($data['http_error'])) {
                if (!empty($data['reason'])) {
                    $result['json_ready']['reason'] = $data['reason'];
                }
                if (in_array($data['http_error'], ['database', 'maintenance'])) {
                    $result['json_ready']['status'] = 503;
                    $result['json_ready']['reason'] = 'Database required, but unavailable';
                } else {
                    $result['json_ready']['status'] = $data['http_error'];
                }
                HomePage::$headers->clientReturn(strval($result['json_ready']['status']), false);
                if (!empty($data['endpoints'])) {
                    $result['json_ready']['reason'] = 'Unsupported endpoint';
                    $result['json_ready']['endpoints'] = $data['endpoints'];
                } elseif (!empty($data['allowed_methods'])) {
                    $result['json_ready']['reason'] = 'Unsupported method used';
                    $result['json_ready']['allowed_methods'] = $data['allowed_methods'];
                }
            } else {
                $result['json_ready']['data'] = $data['response'] ?? NULL;
                if (!empty($data['alt_links'])) {
                    $result['json_ready']['links'] = $data['alt_links'];
                }
                if (!empty($data['edges'])) {
                    $result['json_ready']['edges'] = $data['edges'];
                }
                if (!empty($data['endpoints'])) {
                    $result['json_ready']['endpoints'] = $data['endpoints'];
                }
                if (!empty($data['allowed_methods'])) {
                    $result['json_ready']['allowed_methods'] = $data['allowed_methods'];
                }
            }
            $result['json_ready'] = json_encode($result['json_ready'], JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
            return $result;
        } else {
            return $data;
        }
    }

    #Check that method used is allowed
    protected final function methodCheck(): bool
    {
        #Generate list of allowed methods
        $allowedMethods = array_keys(array_merge(['HEAD' => '', 'OPTIONS' => '', 'GET' => ''], $this->methods));
        #Send headers
        @header('Access-Control-Allow-Methods: '.implode(', ', $allowedMethods));
        @header('Allow: '.implode(', ', $allowedMethods));
        #Check if allowed method is used
        if (in_array(HomePage::$method, $allowedMethods)) {
            return true;
        } else {
            return false;
        }
    }

    #This is a wrapper to allow some common checks
    protected function getData(array $path): array
    {
        if ($this->finalNode && !isset($path[0])) {
            $path[0] = '';
        }
        #If this is a final node, "convert" methods to GET "actions", if such mapping is set. Required for consistency
        if ($this->finalNode) {
            if (!$this->methodCheck()) {
                return ['http_error' => 405, 'allowed_methods' => array_keys($this->methods)];
            }
            if (!in_array(HomePage::$method, ['HEAD', 'OPTIONS', 'GET']) && !empty($this->methods[HomePage::$method])) {
                $path[1] = $this->methods[HomePage::$method];
            }
            if (!empty(HomePage::$http_error) && !$this->static) {
                return HomePage::$http_error;
            }
        }
        $result = $this->genData($path);
        if ($this->finalNode && empty($result['cacheAge'])) {
            $result['cacheAge'] = $this->cacheAge;
        }
        return $result;
    }

    #This is actual page generation based on further details of the $path
    abstract protected function genData(array $path): array;
}
