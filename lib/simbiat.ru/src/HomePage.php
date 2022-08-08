<?php
declare(strict_types=1);
namespace Simbiat;

use DateTimeInterface;
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
        if (preg_match('/^cli(-server)?$/i', php_sapi_name()) === 1) {
            self::$CLI = true;
        } else {
            self::$CLI = false;
        }
        if (is_null(self::$dataCache)) {
            self::$dataCache = new Caching();
        }
        #Get all POST and GET keys to lower case
        $_POST = array_change_key_case($_POST);
        $_GET = array_change_key_case($_GET);
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
                #Do some maintenance stuff
                (new Cron)->process(50);
                #Ensure we exit no matter what happens with CRON
                exit;
            } else {
                #Set method
                self::$method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? null;
                #Parse multipart/form-data for PUT/DELETE/PATCH methods (if any)
                $this::$headers->multiPartFormParse();
                if (in_array(self::$method, ['PUT', 'DELETE', 'PATCH'])) {
                    $_POST = $this::$headers::$_PUT ?: $this::$headers::$_DELETE ?: $this::$headers::$_PATCH ?: [];
                }
                #Set canonical URL
                $this->canonical();
                #Send common headers
                $this::$headers->secFetch();
                #Process requests to file or cache
                $fileResult = $this->filesRequests($_SERVER['REQUEST_URI']);
                if ($fileResult === 200) {
                    exit;
                } else {
                    #Exploding further processing
                    $uri = explode('/', $_SERVER['REQUEST_URI']);
                    try {
                        #Connect to DB
                        $this->dbConnect();
                        #Show error page if DB is down
                        if (!self::$dbup) {
                            self::$http_error = ['http_error' => 'database'];
                        #Show error page if maintenance is running
                        } elseif (self::$dbUpdate) {
                            self::$http_error = ['http_error' => 'maintenance'];
                        }
                        if ($uri[0] !== 'api') {
                            Config\Common::$links = array_merge(Config\Common::$links, [
                                ['rel' => 'stylesheet preload', 'href' => '/css/' . filemtime(Config\Common::$cssDir.'min.css') . '.css', 'as' => 'style'],
                                ['rel' => 'preload', 'href' => '/js/main.min.' . filemtime(Config\Common::$jsDir.'main.min.js') . '.js', 'as' => 'script'],
                            ]);
                        }
                        self::$headers->links(Config\Common::$links);
                        if ($uri[0] !== 'api') {
                            @header('SourceMap: /js/main.min.' . filemtime(Config\Common::$jsDir.'main.min.js').'.js.map', false);
                            @header('SourceMap: /css/' . filemtime(Config\Common::$cssDir.'min.css').'.css.map', false);
                        }
                        #Try to start session if it's not started yet and DB is up
                        if (self::$dbup && session_status() === PHP_SESSION_NONE && !self::$staleReturn) {
                            session_set_save_handler(new Session, true);
                            session_start();
                            #Show that client is unsupported
                            if (!empty($_SESSION['UA']['client']) && preg_match('/^(Internet Explorer|Opera Mini|Baidu|UC Browser|QQ Browser|KaiOS Browser).*/i', $_SESSION['UA']['client']) === 1) {
                                self::$http_error = ['unsupported' => true, 'client' => $_SESSION['UA']['client'], 'http_error' => 418];
                            #Check if banned user
                            } elseif (!empty($_SESSION['banned'])) {
                                self::$http_error = ['http_error' => 403, 'reason' => 'Banned user'];
                            #Check if deleted user
                            } elseif (!empty($_SESSION['deleted'])) {
                                self::$http_error = ['http_error' => 403, 'reason' => 'Deleted user'];
                            #Check if banned IP
                            } elseif (!empty($_SESSION['bannedIP'])) {
                                self::$http_error = ['http_error' => 403, 'reason' => 'Banned IP'];
                            }
                        }
                        #Check if we have cached the results already
                        HomePage::$staleReturn = $this->twigProc(self::$dataCache->read(), true);
                        #Do not do processing if we already encountered a problem
                        if (empty(self::$http_error)) {
                            $vars = (new MainRouter)->route($uri);
                        } else {
                            $vars = self::$http_error;
                        }
                    } catch (\Throwable $e) {
                        Errors::error_log($e);
                        $vars = ['http_error' => 500];
                    }
                    #Generate page
                    $this->twigProc($vars);
                }
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
        }
    }

    public function canonical(): void
    {
        if (empty($_SERVER['HTTP_HOST'])) {
            #May be client is using HTTP1.0 and there is not much to worry about, but maybe there is.
            if (!HomePage::$staleReturn) {
                self::$headers->clientReturn('403');
            }
        }
        #Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = strtolower(rawurldecode(trim(trim(trim(preg_replace('/(.*)(\?.*$)/iu','$1', $_SERVER['REQUEST_URI'] ?? '')), '/'))));
        #Remove bad UTF
        self::$canonical = mb_convert_encoding(self::$canonical, 'UTF-8', 'UTF-8');
        #Remove "friendly" portion of the links, but exclude API
        self::$canonical = preg_replace('/(^(?!api).*)(\/(bic|character|freecompany|pvpteam|linkshell|crossworldlinkshell|crossworld_linkshell|achievement)\/)([a-zA-Z\d]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        #Force _ in crossworldlinkshell
        self::$canonical = preg_replace('/crossworldlinkshell/iu', 'crossworld_linkshell', self::$canonical);
        #Update REQUEST_URI to ensure the data returned will be consistent
        $_SERVER['REQUEST_URI'] = self::$canonical;
        #For canonical, though, we need to ensure, that it does have a trailing slash
        if (preg_match('/\/\?/u', self::$canonical) !== 1) {
            self::$canonical = preg_replace('/([^\/])$/iu', '$1/', self::$canonical);
        }
        #And also return page query, if present
        if (isset($_GET['page'])) {
            self::$canonical .= '?page='.$_GET['page'];
        }
        #Set canonical link, that may be used in the future
        self::$canonical = 'https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config\Common::$http_host) === 1 ? 'www.' : '').Config\Common::$http_host.($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
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
        } else {
            #Return 0, since we did not hit anything
            return 0;
        }
    }

    #Database connection
    public function dbConnect(): bool
    {
        #Check in case we accidentally call this for 2nd time
        if (self::$dbup === false) {
            try {
                (new Pool)->openConnection(Database::getConfig(), maxTries: 5);
                self::$dbup = true;
                #Cache controller
                self::$dbController = (new Controller);
                #Check for maintenance
                self::$dbUpdate = boolval(self::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\''));
                self::$dbController->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            } catch (\Throwable) {
                self::$dbup = false;
                return false;
            }
        }
        return true;
    }

    #Twig processing of the generated page
    public final function twigProc(array $twigVars = [], bool $cache = false): bool
    {
        if ($cache) {
            if (empty($twigVars) || self::$method !== 'GET' || isset($_GET['cachereset']) || isset($_POST['cachereset'])) {
                return false;
            } else {
                try {
                    $twigVars = array_merge($twigVars, self::$http_error, ['XCSRFToken' => $this->csrfUpdate($twigVars['template_override'] ?? 'index.twig')], ['session_data' => $_SESSION ?? null]);
                    ob_end_clean();
                    ignore_user_abort(true);
                    ob_start();
                    @header('Connection: close');
                    $output = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                    #Output data
                    (new Common)->zEcho($output, $twigVars['cacheStrat'] ?? 'day', false);
                    @ob_end_flush();
                    @ob_flush();
                    flush();
                    if (!empty($twigVars['cache_expires_at']) && ($twigVars['cache_expires_at'] - time()) > 0) {
                        exit;
                    } else {
                        return true;
                    }
                } catch (\Throwable) {
                    return false;
                }
            }
        } else {
            ob_start();
            try {
                $output = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', array_merge($twigVars, self::$http_error, ['XCSRFToken' => $this->csrfUpdate($twigVars['template_override'] ?? 'index.twig')], ['session_data' => $_SESSION ?? null]));
            } catch (\Throwable $exception) {
                (new Errors)->error_log($exception);
                self::$headers->clientReturn('503', false);
                try {
                    $output = Twig::getTwig()->render($twigVars['template_override'] ?? 'index.twig', array_merge(['http_error' => 'twig'], ['XCSRFToken' => $this->csrfUpdate($twigVars['template_override'] ?? 'index.twig')], ['session_data' => $_SESSION ?? NULL]));
                } catch (\Throwable) {
                    $output = 'Twig failure';
                }
            }
            #Close session
            if (session_status() === PHP_SESSION_ACTIVE && !self::$staleReturn) {
                session_write_close();
            }
            #Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (Config\Common::$PROD && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge']) && empty($twigVars['http_error']) && self::$method === 'GET') {
                self::$dataCache->write($twigVars, age: intval($twigVars['cacheAge']));
            }
            if (self::$staleReturn === true) {
                @ob_end_clean();
            } else {
                #Output data
                (new Common)->zEcho($output, $twigVars['cacheStrat'] ?? 'day');
            }
            exit;
        }
    }

    #Helper function to update CSRF token for HTML pages only (so that POST API calls from pages will still succeed)
    private function csrfUpdate(string $template): string
    {
        if ($template === 'index.twig') {
            #Update CSRF only if HTML pages are used
            $XCSRFToken = Security::genToken();
            $_SESSION['CSRF'] = $XCSRFToken;
        } else {
            $XCSRFToken = $_SESSION['CSRF'] ?? Security::genToken();
        }
        return $XCSRFToken;
    }
}
