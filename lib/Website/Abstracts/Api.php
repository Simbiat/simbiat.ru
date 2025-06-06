<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\HomePage;
use Simbiat\http20\Headers;
use Simbiat\Website\Security;

use function in_array, is_array;

/**
 * Abstract class to handle API stuff
 */
abstract class Api
{
    #Supported edges
    protected array $subRoutes = [];
    #Description of the nodes (need to be in the same order)
    protected array $routesDesc = [];
    #Flag to indicate that this is a top level node (false by default)
    protected bool $topLevel = false;
    #Flag to indicate that this is the lowest level
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
    #List of permissions, from which at least 1 is required to have access the node
    protected array $requiredPermission = [];
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = false;
    #List of allowed origins, if we want to limit them
    protected array $allowedOrigins = [];
    
    /**
     * Send API headers
     * @return void
     */
    public static function headers(): void
    {
        #Send headers
        if (!headers_sent()) {
            header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
            header('Allow: GET, HEAD, OPTIONS');
            header('Content-Type: application/json; charset=utf-8');
        }
    }
    
    /**
     * This is a general routing check for supported node
     * @param array $path
     *
     * @return array
     */
    final public function route(array $path): array
    {
        if ($this->topLevel) {
            self::headers();
        }
        #Check if proper endpoint
        if (!empty($this->subRoutes) && (empty($path[0]) || (!$this->finalNode && !in_array($path[0], $this->subRoutes, true)))) {
            $data = ['http_error' => 400, 'reason' => 'Unsupported endpoint', 'endpoints' => array_combine($this->subRoutes, $this->routesDesc)];
        } elseif ($this->authenticationNeeded && $_SESSION['userid'] === 1) {
            #User is not authenticated
            $data = ['http_error' => 403, 'reason' => 'Authentication required'];
        } elseif ($this->CSRF && !$this->antiCSRF($this->allowedOrigins)) {
            $data = ['http_error' => 403, 'reason' => 'CSRF validation failed, possibly due to expired session. Please, try to reload the page.'];
        } else {
            try {
                if (!empty($this->requiredPermission) && empty(array_intersect($this->requiredPermission, $_SESSION['permissions']))) {
                    $data = ['http_error' => 403, 'reason' => 'No `'.implode('` or `', $this->requiredPermission).'` permission'];
                } else {
                    $data = $this->getData($path);
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
            if (!empty($data['cacheAge']) && !$this->static) {
                $result['cacheAge'] = $data['cacheAge'];
            }
            if (!empty($data['http_error'])) {
                #Location is for returning a link for the already existing resource if we tried to create a new one or for links to which we should redirect after an action
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
                Headers::clientReturn($result['json_ready']['status'], false);
            }
            if (!empty($data['about'])) {
                $result['json_ready']['about'] = $data['about'];
            }
            try {
                $result['json_ready'] = json_encode($result['json_ready'], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
            } catch (\JsonException) {
                $result['json_ready'] = '{
    "status": 500,
    "reason": "Failed to generate JSON output"
}';
            }
            return $result;
        }
        return $data;
    }
    
    /**
     * Method to filter output fields
     * @param array $array
     *
     * @return void
     */
    final protected function fieldFilter(array &$array): void
    {
        $fields = $_GET['fields'] ?? $_POST['fields'] ?? null;
        if (!empty($fields)) {
            $filter = explode(',', $fields);
            if (!empty($filter)) {
                foreach ($array as $field => $value) {
                    if (!in_array($field, $filter, true)) {
                        unset($array[$field]);
                    }
                }
            }
        }
    }
    
    /**
     * Check that method used is allowed
     * @return bool
     */
    final protected function methodCheck(): bool
    {
        #Generate a list of allowed methods
        $allowedMethods = array_keys(array_merge(['HEAD' => '', 'OPTIONS' => '', 'GET' => ''], $this->methods));
        #Send headers
        if (!headers_sent()) {
            header('Access-Control-Allow-Methods: '.implode(', ', $allowedMethods));
            header('Allow: '.implode(', ', $allowedMethods));
        }
        #Check if allowed method is used. EA incorrectly suggests use of `array_key_exists`, which does not fit here, due to how $allowedMethods is used in the whole method
        /** @noinspection InArrayMissUseInspection */
        return in_array(HomePage::$method, $allowedMethods, true);
    }
    
    /**
     * Function to help protect against CSRF. Suggested using for forms or APIs. Needs to be used before writing anything to `$_SESSION`
     * @param array $allowOrigins
     *
     * @return bool
     */
    final protected function antiCSRF(array $allowOrigins = []): bool
    {
        #By default, allow only our own origin
        if (empty($allowOrigins)) {
            $allowOrigins = [Config::$baseUrl];
        }
        #Get CSRF token
        $token = $_POST['X-CSRF-Token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null;
        #Get origin
        #In some cases Origin can be empty. In case of forms, we can try checking Referer instead.
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? NULL;
        #Check if a token is provided
        if (!empty($token)) {
            #Check if CSRF token is present in session data
            if (!empty($_SESSION['CSRF'])) {
                #Check if they match. `hash_equals` helps mitigate timing attacks
                if (hash_equals($_SESSION['CSRF'], $token)) {
                    #Check if HTTP Origin is among allowed ones if we want to restrict them.
                    #Note that this will be applied to forms or APIs you want to restrict. For global restriction use \Simbiat\http20\headers->security()
                    if (empty($allowOrigins) ||
                        #If origins are limited, check if origin is present
                        (!empty($origin) &&
                            #Check if it's a valid origin and is allowed
                            (preg_match('/'.Headers::originRegex.'/i', $origin) === 1 || in_array($origin, $allowOrigins, true))
                        )
                    ) {
                        #All checks passed
                        $_SESSION['CSRF'] = Security::genToken();
                        return true;
                    }
                    $reason = 'Bad origin';
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
            'page' => $_SERVER['REQUEST_URI'] ?? null,
            'origin' => $_SERVER['HTTP_ORIGIN'] ?? null,
            'referer' => $_SERVER['HTTP_REFERER'] ?? null,
        ]);
        #Send `403` error code in header, with an option to force close connection
        if (!HomePage::$staleReturn) {
            Headers::clientReturn(403, false);
        }
        $_SESSION['CSRF'] = Security::genToken();
        return false;
    }
    
    /**
     * This is a wrapper to allow some common checks
     * @param array $path
     *
     * @return array
     */
    protected function getData(array $path): array
    {
        if ($this->finalNode && !isset($path[0])) {
            $path[0] = '';
        }
        $result = [];
        #If this is a final node, "convert" methods to GET "actions" if such mapping is set. Required for consistency
        if ($this->finalNode) {
            #Close session early, if we know, that its data will not be changed (default)
            if (!$this->sessionChange && session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            #Add description
            if (!empty($this->description)) {
                $result['about'] = $this->description;
                $result['about']['verbs'] = $this->verbs;
                $result['about']['methods'] = $this->methods;
            }
            if (!$this->methodCheck()) {
                return array_merge($result, ['http_error' => 405, 'reason' => 'Unsupported HTTP method used']);
            }
            #Override $path[1] with `verb` from POST, if it was provided
            $path[1] = $_POST['verb'] ?? $path[1] ?? '';
            #Override based on method only if method is not HEAD, OPTIONS or GET and if a respective method has a verb set for it
            if (!empty($this->methods[HomePage::$method]) && !in_array(HomePage::$method, ['HEAD', 'OPTIONS', 'GET'])) {
                if (\is_string($this->methods[HomePage::$method])) {
                    $path[1] = $this->methods[HomePage::$method];
                    #If we have an array of possible verbs for method, check that proper verb is provided
                } elseif (is_array($this->methods[HomePage::$method])) {
                    if (empty($path[1])) {
                        return array_merge($result, ['http_error' => 405, 'reason' => '`'.HomePage::$method.'` method supports multiple AIP verbs, none provided']);
                    }
                    if (!in_array($path[1], $this->methods[HomePage::$method], true)) {
                        return array_merge($result, ['http_error' => 405, 'reason' => '`'.HomePage::$method.'` method does not support `'.$path[1].'` API verb']);
                    }
                }
            }
            if (!empty($path[1]) && !\array_key_exists($path[1], $this->verbs)) {
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
            if (empty($result['cacheAge']) && !$this->static) {
                $result['cacheAge'] = $this->cacheAge;
            }
            #Close session if it's still open. Normally at this point all manipulations have been done.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
        }
        return $result;
    }
    
    /**
     * This is an actual API response generation based on further details of the $path
     * @param array $path
     *
     * @return array
     */
    abstract protected function genData(array $path): array;
}
