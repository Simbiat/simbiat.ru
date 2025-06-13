<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use DateTimeInterface;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Abstracts\Page;
use Simbiat\http20\Common;
use Simbiat\http20\Headers;
use Simbiat\http20\Links;
use Simbiat\Website\Routing\MainRouter;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Simbiat\Website\usercontrol\Session;

/**
 * Class to generate pages. "HomePage" is a legacy name
 */
class HomePage
{
    #Cache object
    public static ?Caching $dataCache = null;
    #HTTP headers object
    public static ?Headers $headers = NULL;
    #Flag indicating that cached view has been served already
    public static bool $staleReturn = false;
    #HTTP method being used
    public static ?string $method = null;
    #Array that can contain variables indicating common HTTP errors
    public static ?array $http_error = [];
    
    public function __construct()
    {
        #Cache headers object
        self::$headers = new Headers();
        self::$dataCache ??= new Caching();
        #Set method
        self::$method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? null;
        #Parse multipart/form-data for PUT/DELETE/PATCH methods (if any)
        Headers::multiPartFormParse();
        if (\in_array(self::$method, ['PUT', 'DELETE', 'PATCH'])) {
            $_POST = array_change_key_case(Headers::$_PUT ?: Headers::$_DELETE ?: Headers::$_PATCH ?: []);
            $_FILES = Headers::$_FILES;
        }
        #Get all POST and GET keys to the lower case
        $_POST = array_change_key_case($_POST);
        Sanitization::carefulArraySanitization($_POST);
        $_GET = array_change_key_case($_GET);
        Sanitization::carefulArraySanitization($_GET);
        $this->init();
    }
    
    /**
     * Initial routing logic
     * @return void
     */
    private function init(): void
    {
        try {
            #Maybe a client is using HTTP1.0, and there is little to worry about, but maybe there is.
            if (empty($_SERVER['HTTP_HOST'])) {
                Headers::clientReturn(403);
            }
            #Redirect if the page number is set and is less than 1
            if (isset($_GET['page']) && (int)$_GET['page'] < 1) {
                #Remove page (since we ignore page=1 in canonical)
                Headers::redirect(preg_replace('/\\?page=-?\d+/ui', '', Config::$canonical));
            }
            #Process requests to file or cache
            $fileResult = $this->filesRequests($_SERVER['REQUEST_URI']);
            if ($fileResult === 200) {
                exit(0);
            }
            #Exploding further processing
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            try {
                #Connect to DB
                Config::dbConnect();
                #Show an error page if DB is down
                if (!Config::$dbup) {
                    self::$http_error = ['http_error' => 503, 'reason' => 'Failed to connect to database'];
                } elseif (Config::$db_update) {
                    #Show an error page if maintenance is running
                    self::$http_error = ['http_error' => 503, 'reason' => 'Site is under maintenance and temporary unavailable'];
                }
                #Supress inspection, since we only need headers to be sent
                /** @noinspection UnusedFunctionResultInspection */
                Links::links(Config::$links);
                #Send standard headers
                if ($uri[0] === 'api') {
                    Api::headers();
                } else {
                    Page::headers();
                }
                #Try to start a session if it's not started yet and DB is up
                if (Config::$dbup && !Config::$db_update && !self::$staleReturn && session_status() === PHP_SESSION_NONE) {
                    session_set_save_handler(new Session(), true);
                    session_start();
                    #Show that the client is unsupported
                    if (isset($_SESSION['UA']['unsupported']) && $_SESSION['UA']['unsupported'] === true) {
                        self::$http_error = ['client' => $_SESSION['UA']['client'] ?? 'unknown', 'http_error' => 418, 'reason' => 'Teapot'];
                        #Check if banned IP
                    } elseif (!empty($_SESSION['bannedIP'])) {
                        self::$http_error = ['http_error' => 403, 'reason' => 'Banned IP'];
                    }
                    #Handle Sec-Fetch. Use strict mode if a request is not from a known bot and is from a known browser (bots and non-browser applications like libraries may not have Sec-Fetch headers)
                    Headers::secFetch(strict: (empty($_SESSION['UA']['bot']) && $_SESSION['UA']['browser']));
                } else {
                    $ua = Security::getUA();
                    #Show that the client is unsupported
                    if ($ua['unsupported'] === true) {
                        self::$http_error = ['client' => $ua['client'] ?? 'Teapot', 'http_error' => 418, 'reason' => 'Teapot'];
                    }
                    #Handle Sec-Fetch. Use strict mode if the request is not from a known bot and is from a known browser (bots and non-browser applications like libraries may not have Sec-Fetch headers)
                    Headers::secFetch(strict: (empty($ua['bot']) && $ua['browser']));
                }
                #Check if we have cached the results already
                self::$staleReturn = $this->twigProc(self::$dataCache->read(), true);
                #Check if there was an internal redirect to a custom error page
                if (!empty($_SERVER['CADDY_HTTP_ERROR'])) {
                    if (preg_match('/\d{3}/', $_SERVER['CADDY_HTTP_ERROR']) === 1) {
                        self::$http_error = ['http_error' => $_SERVER['CADDY_HTTP_ERROR'], 'reason' => $_SERVER['CADDY_HTTP_ERROR_MSG'] ?? ''];
                    } else {
                        self::$http_error = ['http_error' => 500, 'reason' => 'Failed on Caddy level and could not retrieve the error message'];
                    }
                }
                #Do not do processing if we already encountered a problem
                if (empty(self::$http_error)) {
                    $vars = new MainRouter()->route($uri);
                } else {
                    $vars = self::$http_error;
                }
            } catch (\Throwable $e) {
                Errors::error_log($e);
                $vars = ['http_error' => 500];
            }
            if ($uri[0] === 'api' && empty($vars['template_override'])) {
                $vars['template_override'] = 'common/pages/api.twig';
            }
            #Generate page
            $this->twigProc($vars);
        } catch (\Throwable $e) {
            Errors::error_log($e);
        }
    }
    
    /**
     * Function to process some special files
     * @param string $request
     *
     * @return int
     */
    public function filesRequests(string $request): int
    {
        #Remove query string, if present (that is everything after ?)
        $request = preg_replace('/^(.*)(\?.*)?$/', '$1', $request);
        if (preg_match('/^\.well-known\/security\.txt$/i', $request) === 1) {
            #Send headers that will identify this as an actual file
            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=utf-8');
                header('Content-Disposition: inline; filename="security.txt"');
            }
            $this->twigProc(['template_override' => 'about/security.txt.twig', 'expires' => date(DateTimeInterface::RFC3339_EXTENDED, strtotime('last monday of next month midnight'))]);
            return 200;
        }
        #Return 0, since we did not hit anything
        return 0;
    }
    
    /**
     * Twig processing of the generated page
     * @param array $twigVars List of Twig variables
     * @param bool  $cache    Indicates if this is a cache pass
     *
     * @return bool
     */
    final public function twigProc(array $twigVars = [], bool $cache = false): bool
    {
        if ($cache) {
            if (empty($twigVars) || self::$method !== 'GET' || isset($_GET['cachereset']) || isset($_POST['cachereset'])) {
                return false;
            }
            try {
                #Update CSRF token
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['CSRF'] = Security::genToken();
                }
                $twigVars = array_merge($twigVars, self::$http_error, ['session_data' => $_SESSION ?? null]);
                if (isset($twigVars['http_error'])) {
                    Headers::clientReturn($twigVars['http_error'], false);
                }
                ob_end_clean();
                ignore_user_abort(true);
                ob_start();
                $output = EnvironmentGenerator::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'hour', false);
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @ob_end_flush();
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @ob_flush();
                flush();
                if (!empty($twigVars['cache_expires_at']) && ($twigVars['cache_expires_at'] - time()) > 0) {
                    exit(0);
                }
                return true;
            } catch (\Throwable) {
                return false;
            }
        } else {
            ob_start();
            try {
                #Update CSRF token
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['CSRF'] = Security::genToken();
                }
                $twigVars = array_merge($twigVars, self::$http_error, ['session_data' => $_SESSION ?? null]);
                if (isset($twigVars['http_error'])) {
                    Headers::clientReturn($twigVars['http_error'], false);
                }
                $output = EnvironmentGenerator::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            } catch (\Throwable $exception) {
                Errors::error_log($exception);
                Headers::clientReturn(500, false);
                try {
                    $output = EnvironmentGenerator::getTwig()->render('index.twig', ['http_error' => 500, 'reason' => 'Twig failure', 'session_data' => $_SESSION ?? NULL]);
                } catch (\Throwable) {
                    $output = 'Complete twig failure';
                }
            }
            #Close session
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            #Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (Config::$prod && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge']) && empty($twigVars['http_error']) && self::$method === 'GET') {
                self::$dataCache->write($twigVars, age: (int)$twigVars['cacheAge']);
            }
            if (self::$staleReturn) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @ob_end_clean();
            } else {
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'hour', false);
            }
            exit(0);
        }
    }
}