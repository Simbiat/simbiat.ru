<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\HTTP20\Headers;
use Simbiat\Security;

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
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag to indicate, that no database is required for this node
    protected bool $static = false;
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 0;
    #Description of the node
    protected array $description = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = false;
    #List of allowed origins, if we want to limit them
    protected array $allowedOrigins = [];

    #This is general routing check for supported node
    public final function route(array $path): array
    {
        #Close session early, if we know, that its data will not be changed (default)
        if (!$this->sessionChange && session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if ($this->topLevel) {
            #Send headers
            @header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
            @header('Allow: GET, HEAD, OPTIONS');
            @header('Content-Type: application/json; charset=utf-8');
        }
        #Check if proper endpoint
        if (!empty($this->subRoutes) && (empty($path[0]) || (!$this->finalNode && !in_array($path[0], $this->subRoutes)))) {
            $data = ['http_error' => 400, 'endpoints' => array_combine($this->subRoutes, $this->routesDesc)];
        #Check that user is authenticated
        } elseif ($this->authenticationNeeded && empty($_SESSION['userid'])) {
            $data = ['http_error' => 403, 'reason' => 'Authentication required'];
        } elseif ($this->CSRF && !$this->antiCSRF($this->allowedOrigins)) {
            $data = ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
        } else {
            try {
                $data = $this->getData($path);
                #Close session if it's still open. Normally at this point all manipulations have been done.
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
            } catch (\Throwable $exception) {
                if (preg_match('/(ID `.*` for entity `.*` has incorrect format\.)|(ID can\'t be empty\.)/ui', $exception->getMessage()) === 1) {
                    $data = ['http_error' => 400, 'reason' => $exception->getMessage()];
                } else {
                    Errors::error_log($exception);
                    $data = ['http_error' => 500, 'reason' => 'Unknown error occurred'];
                }
            }
        }
        if ($this->topLevel) {
            #Override template
            $result['template_override'] = 'common/pages/api.twig';
            #Prepare JSON output
            $result['json_ready'] = ['status' => 200];
            if (!empty($data['cacheAge']) && $this->static === false) {
                $result['cacheAge'] = $data['cacheAge'];
            }
            if (!empty($data['http_error'])) {
                #Location is for returning a link for the already existing resource, if we tried to create a new one
                if (!empty($data['location'])) {
                    $result['json_ready']['location'] = $data['location'];
                }
                if (!empty($data['reason'])) {
                    $result['json_ready']['reason'] = $data['reason'];
                }
                if (in_array($data['http_error'], ['database', 'maintenance'])) {
                    $result['json_ready']['status'] = 503;
                    $result['json_ready']['reason'] = 'Database required, but unavailable';
                } else {
                    $result['json_ready']['status'] = $data['http_error'];
                }
                if (!empty($data['endpoints'])) {
                    $result['json_ready']['reason'] = 'Unsupported endpoint';
                    $result['json_ready']['endpoints'] = $data['endpoints'];
                } elseif ($data['http_error'] === 405) {
                    $result['json_ready']['reason'] = $data['reason'] ?? 'Unsupported HTTP method used';
                }
            } else {
                $result['json_ready']['data'] = $data['response'] ?? null;
                #Filter out results if data is an array
                if (is_array($result['json_ready']['data'])) {
                    #Suppressed due to https://youtrack.jetbrains.com/issue/WI-65237/Wrong-array-element-type-is-inferred-on-assignment
                    /** @noinspection PhpParamsInspection */
                    $this->fieldFilter($result['json_ready']['data']);
                }
                #Location is for returning a link for the new resource
                if (!empty($data['location'])) {
                    $result['json_ready']['location'] = $data['location'];
                }
                if (!empty($data['alt_links'])) {
                    $result['json_ready']['links'] = $data['alt_links'];
                }
                if (!empty($data['edges'])) {
                    $result['json_ready']['edges'] = $data['edges'];
                }
                if (!empty($data['endpoints'])) {
                    $result['json_ready']['endpoints'] = $data['endpoints'];
                }
                if (!empty($data['status'])) {
                    $result['json_ready']['status'] = $data['status'];
                }
            }
            if ($result['json_ready']['status'] !== 200) {
                Headers::clientReturn(strval($result['json_ready']['status']), false);
            }
            if (!empty($data['about'])) {
                $result['json_ready']['about'] = $data['about'];
            }
            $result['json_ready'] = json_encode($result['json_ready'], JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
            return $result;
        } else {
            return $data;
        }
    }

    #Method to filter output fields
    protected final function fieldFilter(array &$array): void
    {
        $fields = $_GET['fields'] ?? $_POST['fields'] ?? null;
        if (!empty($fields)) {
            $filter = explode(',', $fields);
            if (!empty($filter)) {
                foreach ($array as $field => $value) {
                    if (!in_array($field, $filter)) {
                        unset($array[$field]);
                    }
                }
            }
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

    #Function to help protect against CSRF. Suggested using for forms or APIs. Needs to be used before any writes to $_SESSION
    protected final function antiCSRF(array $allowOrigins = []): bool
    {
        #Get CSRF token
        $token = $_POST['X-CSRF-Token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null;
        #Get origin
        #In some cases Origin can be empty. In case of forms we can try checking Referer instead.
        #In case of proxy is being used we should try taking the data from X-Forwarded-Host.
        $origin = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? NULL;
        #Check if token is provided
        if (!empty($token)) {
            #Check if CSRF token is present in session data
            if (!empty($_SESSION['CSRF'])) {
                #Check if they match. hash_equals helps mitigate timing attacks
                if (hash_equals($_SESSION['CSRF'], $token) === true) {
                    #Check if HTTP Origin is among allowed ones, if we want to restrict them.
                    #Note that this will be applied to forms or APIs you want to restrict. For global restriction use \Simbiat\HTTP20\headers->security()
                    if (empty($allowOrigins) ||
                        #If origins are limited
                        (
                            #Check if origin is present
                            (!empty($origin) &&
                                #Check if it's a valid origin and is allowed
                                (preg_match('/'. Headers::originRegex.'/i', $origin) === 1 || in_array($origin, $allowOrigins))
                            )
                        )
                    ) {
                        #All checks passed
                        return true;
                    } else {
                        $reason = 'Bad origin';
                    }
                } else {
                    $reason = 'Different hashes';
                }
            } else {
                $reason = 'No token in session';
            }
        } else {
            $reason = 'No token from client';
        }
        #Log attack details. Suppressing errors, so that values will be turned into NULLs if they are not set
        Security::log('CSRF', 'CSRF attack detected', [
            'reason' => $reason,
            'page' => @$_SERVER['REQUEST_URI'],
            'forwarded' => @$_SERVER['HTTP_X_FORWARDED_HOST'],
            'origin' => @$_SERVER['HTTP_ORIGIN'],
            'referer' => @$_SERVER['HTTP_REFERER'],
        ]);
        #Send 403 error code in header, with option to force close connection
        if (!HomePage::$staleReturn) {
            Headers::clientReturn('403', false);
        }
        return false;
    }

    #This is a wrapper to allow some common checks
    protected function getData(array $path): array
    {
        if ($this->finalNode && !isset($path[0])) {
            $path[0] = '';
        }
        $result = [];
        #If this is a final node, "convert" methods to GET "actions", if such mapping is set. Required for consistency
        if ($this->finalNode) {
            #Add description
            if (!empty($this->description)) {
                $result['about'] = $this->description;
                $result['about']['verbs'] = $this->verbs;
                $result['about']['methods'] = $this->methods;
            }
            if (!$this->methodCheck()) {
                return array_merge($result, ['http_error' => 405, 'reason' => 'Unsupported HTTP method used']);
            }
            #Convert only if method is not HEAD, OPTIONS or GET and if respective method has a verb set for it
            if (!in_array(HomePage::$method, ['HEAD', 'OPTIONS', 'GET']) && !empty($this->methods[HomePage::$method])) {
                $path[1] = $this->methods[HomePage::$method];
            }
            if (!empty($path[1]) && !in_array($path[1], array_keys($this->verbs))) {
                return array_merge($result, ['http_error' => 405, 'reason' => 'Unsupported API verb used']);
            }
            if (!empty(HomePage::$http_error) && !$this->static) {
                return array_merge($result, HomePage::$http_error);
            }
        }
        $result = array_merge($result, $this->genData($path));
        #Add extra data if final node
        if ($this->finalNode) {
            #Add cache age if set
            if (empty($result['cacheAge']) && $this->static === false) {
                $result['cacheAge'] = $this->cacheAge;
            }
        }
        return $result;
    }

    #This is actual page generation based on further details of the $path
    abstract protected function genData(array $path): array;
}
