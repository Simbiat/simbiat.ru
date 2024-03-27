<?php
declare(strict_types=1);
namespace Simbiat;

use DateTimeInterface;
use Simbiat\Abstracts\Api;
use Simbiat\Config\Database;
use Simbiat\Config\Twig;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;
use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Headers;
use Simbiat\Routing\MainRouter;
use Simbiat\usercontrol\Session;

class HomePage
{
    #Allow access to canonical value of the host
    public static string $canonical = '';
    #Track if DB connection is up
    public static bool $dbup = false;
    #Maintenance flag
    public static bool $dbUpdate = false;
    #Database controller object
    public static ?Controller $dbController = NULL;
    #Cache object
    public static ?Caching $dataCache = null;
    #HTTP headers object
    public static ?Headers $headers = NULL;
    #Flag indicating that cached view has been served already
    public static bool $staleReturn = false;
    #Flag indicating whether we are in CLI
    public static bool $CLI = false;
    #HTTP method being used
    public static ?string $method = null;
    #Array that can contain variables indicating common HTTP errors
    public static ?array $http_error = [];

    public function __construct()
    {
        #Cache headers object
        self::$headers = new Headers;
        #Check if we are in CLI
        if (preg_match('/^cli(-server)?$/i', PHP_SAPI) === 1) {
            self::$CLI = true;
        } else {
            self::$CLI = false;
        }
        self::$dataCache ??= new Caching();
        #Get all POST and GET keys to lower case
        $_POST = array_change_key_case($_POST);
        Sanitization::carefulArraySanitization($_POST);
        $_GET = array_change_key_case($_GET);
        Sanitization::carefulArraySanitization($_GET);
        $this->init();
    }

    #Initial routing logic
    private function init(): void
    {
        #If not CLI - do redirects and other HTTP-related stuff
        try {
            if (self::$CLI) {
                #Process Cron
                $this->dbConnect();
                $healthCheck = (new Maintenance);
                #Check if DB is down
                $healthCheck->dbDown();
                #Check space availability
                $healthCheck->noSpace();
                #Run cron
                (new Cron)->process(50);
                #Ensure we exit no matter what happens with CRON
                exit;
            }
            #Set method
            self::$method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? null;
            #Parse multipart/form-data for PUT/DELETE/PATCH methods (if any)
            Headers::multiPartFormParse();
            if (in_array(self::$method, ['PUT', 'DELETE', 'PATCH'])) {
                $_POST = array_change_key_case(self::$headers::$_PUT ?: self::$headers::$_DELETE ?: self::$headers::$_PATCH ?: []);
                Sanitization::carefulArraySanitization($_POST);
            }
            #Set canonical URL
            $this->canonical();
            #Redirect if page number is set and is less than 1
            if (isset($_GET['page']) && (int)$_GET['page'] < 1) {
                #Remove page (since we ignore page=1 in canonical)
                Headers::redirect(preg_replace('/\\?page=-?\d+/ui', '', self::$canonical));
            }
            #Process requests to file or cache
            $fileResult = $this->filesRequests($_SERVER['REQUEST_URI']);
            if ($fileResult === 200) {
                exit;
            }
            #Exploding further processing
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            try {
                #Connect to DB
                $this->dbConnect();
                #Show error page if DB is down
                if (!self::$dbup) {
                    self::$http_error = ['http_error' => 503, 'reason' => 'Failed to connect to database'];
                #Show error page if maintenance is running
                } elseif (self::$dbUpdate) {
                    self::$http_error = ['http_error' => 503, 'reason' => 'Site is under maintenance and temporary unavailable'];
                }
                if ($uri[0] !== 'api') {
                    Config\Common::$links = array_merge(Config\Common::$links, [
                        ['rel' => 'stylesheet preload', 'href' => '/css/' . filemtime(Config\Common::$cssDir.'/min.css') . '.css', 'as' => 'style'],
                        ['rel' => 'preload', 'href' => '/js/main.min.' . filemtime(Config\Common::$jsDir.'/main.min.js') . '.js', 'as' => 'script'],
                    ]);
                }
                Headers::links(Config\Common::$links);
                if ($uri[0] !== 'api') {
                    @header('SourceMap: /js/main.min.' . filemtime(Config\Common::$jsDir.'/main.min.js').'.js.map', false);
                    @header('SourceMap: /css/' . filemtime(Config\Common::$cssDir.'/min.css').'.css.map', false);
                }
                #Try to start session if it's not started yet and DB is up
                if (self::$dbup && !self::$staleReturn && session_status() === PHP_SESSION_NONE) {
                    session_set_save_handler(new Session, true);
                    session_start();
                    #Update CSRF token
                    if ($uri[0] !== 'api') {
                        $_SESSION['CSRF'] = Security::genToken();
                    }
                    #Show that client is unsupported
                    if (isset($_SESSION['UA']['unsupported']) && $_SESSION['UA']['unsupported'] === true) {
                        self::$http_error = ['client' => $_SESSION['UA']['client'] ?? 'unknown', 'http_error' => 418, 'reason' => 'Teapot'];
                        #Check if banned IP
                    } elseif (!empty($_SESSION['bannedIP'])) {
                        self::$http_error = ['http_error' => 403, 'reason' => 'Banned IP'];
                    }
                    #Handle Sec-Fetch. Use strict mode if request is not from known bot and is from a known browser (bots and non-browser applications like libraries may not have Sec-Fetch headers)
                    Headers::secFetch(strict: (empty($_SESSION['UA']['bot']) && $_SESSION['UA']['browser']));
                } else {
                    $ua = Security::getUA();
                    #Show that client is unsupported
                    if ($ua['unsupported'] === true) {
                        self::$http_error = ['client' => $ua['client'], 'http_error' => 418, 'reason' => 'Teapot'];
                    }
                    #Handle Sec-Fetch. Use strict mode if request is not from known bot and is from a known browser (bots and non-browser applications like libraries may not have Sec-Fetch headers)
                    Headers::secFetch(strict: (empty($ua['bot']) && $ua['browser']));
                }
                #Check if we have cached the results already
                self::$staleReturn = $this->twigProc(self::$dataCache->read(), true);
                #Do not do processing if we already encountered a problem
                if (empty(self::$http_error)) {
                    #Check if there was an internal redirect to custom error page
                    if (!empty($_SERVER['REDIRECT_URL']) && preg_match('/(^|\/)(http)?error(s)?\/\d{3}(\/|$)/ui', $_SERVER['REDIRECT_URL']) === 1 && trim($_SERVER['REDIRECT_URL'], '/') !== trim($_SERVER['REQUEST_URI'], '/')) {
                        $uri = explode('/', trim($_SERVER['REDIRECT_URL'], '/'));
                        $vars = (new MainRouter)->route($uri);
                        #Add a note to error pages
                        $vars['internal_litespeed_redirect'] = true;
                    } else {
                        $vars = (new MainRouter)->route($uri);
                    }
                } else {
                    $vars = self::$http_error;
                    if ($uri[0] === 'api') {
                        Api::headers();
                    }
                }
            } catch (\Throwable $e) {
                Errors::error_log($e);
                $vars = ['http_error' => 500];
                if ($uri[0] === 'api') {
                    Api::headers();
                }
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

    public function canonical(): void
    {
        #May be client is using HTTP1.0 and there is not much to worry about, but maybe there is.
        if (empty($_SERVER['HTTP_HOST']) && !self::$staleReturn) {
            Headers::clientReturn(403);
        }
        #Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = strtolower(rawurldecode(trim(trim(trim(preg_replace('/(.*)(\?.*$)/u','$1', $_SERVER['REQUEST_URI'] ?? '')), '/'))));
        #Remove bad UTF
        self::$canonical = mb_convert_encoding(self::$canonical, 'UTF-8', 'UTF-8');
        #Remove "friendly" portion of the links, but exclude API
        self::$canonical = preg_replace('/(^(?!api).*)(\/(bic|characters|freecompanies|pvpteams|linkshells|crossworldlinkshells|crossworld_linkshells|achievements|sections|threads|users)\/)([a-zA-Z\d]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        #Force _ in crossworldlinkshell
        self::$canonical = str_ireplace("crossworldlinkshells", 'crossworld_linkshells', self::$canonical);
        #Update REQUEST_URI to ensure the data returned will be consistent
        $_SERVER['REQUEST_URI'] = self::$canonical;
        #For canonical, though, we need to ensure, that it does have a trailing slash
        if (preg_match('/\/\?/u', self::$canonical) !== 1) {
            self::$canonical = preg_replace('/([^\/])$/u', '$1/', self::$canonical);
        }
        #And also return page or search query, if present
        #Do not add 1st page as query (since it is excessive)
        if (empty($_GET['page']) || $_GET['page'] === '1') {
            $_GET['page'] = null;
        }
        self::$canonical .= '?'.http_build_query([
            'page' => $_GET['page'],
            'search' => $_GET['search'] ?? null,
        ], encoding_type: PHP_QUERY_RFC3986);
        #Trim the excessive question mark, in case no query was attached
        self::$canonical = rtrim(self::$canonical, '?');
        #Trim trailing slashes if any
        self::$canonical = rtrim(self::$canonical, '/');
        #Set canonical link, that may be used in the future
        self::$canonical = 'https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config\Common::$http_host) === 1 ? 'www.' : '').Config\Common::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        #Update list with dynamic values
        Config\Common::$links = array_merge(Config\Common::$links, [
            ['rel' => 'canonical', 'href' => self::$canonical],
        ]);
    }

    #Function to process some special files

    public function filesRequests(string $request): int
    {
        #Remove query string, if present (that is everything after ?)
        $request = preg_replace('/^(.*)(\?.*)?$/', '$1', $request);
        if (preg_match('/^\.well-known\/security\.txt$/i', $request) === 1) {
            #Send headers, that will identify this as actual file
            @header('Content-Type: text/plain; charset=utf-8');
            @header('Content-Disposition: inline; filename="security.txt"');
            $this->twigProc(['template_override' => 'about/security.txt.twig', 'expires' => date(DateTimeInterface::RFC3339_EXTENDED, strtotime('last monday of next month midnight'))]);
            return 200;
        }
        #Return 0, since we did not hit anything
        return 0;
    }

    #Database connection
    public function dbConnect(): bool
    {
        #Check in case we accidentally call this for 2nd time
        if (self::$dbup === false) {
            try {
                Pool::openConnection(Database::getConfig(), maxTries: 5);
                self::$dbup = true;
                #Cache controller
                self::$dbController = (new Controller);
                #Check for maintenance
                self::$dbUpdate = (bool)self::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\'');
                self::$dbController->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            } catch (\Throwable) {
                self::$dbup = false;
                return false;
            }
        }
        return true;
    }

    #Twig processing of the generated page
    final public function twigProc(array $twigVars = [], bool $cache = false): bool
    {
        if ($cache) {
            if (empty($twigVars) || self::$method !== 'GET' || isset($_GET['cachereset']) || isset($_POST['cachereset'])) {
                return false;
            }
            try {
                $twigVars = array_merge($twigVars, self::$http_error, ['session_data' => $_SESSION ?? null]);
                if (isset($twigVars['http_error'])) {
                    Headers::clientReturn($twigVars['http_error'], false);
                }
                ob_end_clean();
                ignore_user_abort(true);
                ob_start();
                @header('Connection: close');
                $output = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'day', false);
                @ob_end_flush();
                @ob_flush();
                flush();
                if (!empty($twigVars['cache_expires_at']) && ($twigVars['cache_expires_at'] - time()) > 0) {
                    exit;
                }
                return true;
            } catch (\Throwable) {
                return false;
            }
        } else {
            ob_start();
            try {
                $twigVars = array_merge($twigVars, self::$http_error, ['session_data' => $_SESSION ?? null]);
                if (isset($twigVars['http_error'])) {
                    Headers::clientReturn($twigVars['http_error'], false);
                }
                $output = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            } catch (\Throwable $exception) {
                Errors::error_log($exception);
                Headers::clientReturn(500, false);
                try {
                    $output = Twig::getTwig()->render('index.twig', array_merge(['http_error' => 500, 'reason' => 'Twig failure'], ['session_data' => $_SESSION ?? NULL]));
                } catch (\Throwable) {
                    $output = 'Complete twig failure';
                }
            }
            #Close session
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            #Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (Config\Common::$PROD && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge']) && empty($twigVars['http_error']) && self::$method === 'GET') {
                self::$dataCache->write($twigVars, age: (int)$twigVars['cacheAge']);
            }
            if (self::$staleReturn === true) {
                @ob_end_clean();
            } else {
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'day');
            }
            exit;
        }
    }
}
