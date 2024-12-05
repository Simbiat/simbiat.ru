<?php
declare(strict_types=1);
namespace Simbiat\Website;

use DateTimeInterface;
use Simbiat\Cron;
use Simbiat\Website;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Abstracts\Page;
use Simbiat\Database\Config;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;
use Simbiat\http20\Common;
use Simbiat\http20\Headers;
use Simbiat\http20\Links;
use Simbiat\Website\Routing\MainRouter;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Simbiat\Website\usercontrol\Session;

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
                (new Cron\Agent())->process(200);
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
                    Website\Config::$links = array_merge(Website\Config::$links, [
                        ['rel' => 'stylesheet preload', 'href' => '/assets/styles/' . filemtime(Website\Config::$cssDir.'/app.css') . '.css', 'as' => 'style'],
                        ['rel' => 'preload', 'href' => '/assets/app.' . filemtime(Website\Config::$jsDir.'/app.js') . '.js', 'as' => 'script'],
                    ]);
                }
                Links::links(Website\Config::$links);
                #Send standard headers
                if ($uri[0] === 'api') {
                    Api::headers();
                } else {
                    Page::headers();
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
                #Check if there was an internal redirect to custom error page
                if (!empty($_SERVER['CADDY_HTTP_ERROR'])) {
                    self::$http_error = ['http_error' => $_SERVER['CADDY_HTTP_ERROR'], 'reason' => $_SERVER['CADDY_HTTP_ERROR_MSG'] ?? ''];
                }
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
        self::$canonical = mb_strtolower(rawurldecode(trim(trim(trim(preg_replace('/(.*)(\?.*$)/u', '$1', $_SERVER['REQUEST_URI'] ?? '')), '/'))), 'UTF-8');
        #Remove bad UTF
        self::$canonical = mb_scrub(self::$canonical, 'UTF-8');
        #Remove "friendly" portion of the links, but exclude API
        self::$canonical = preg_replace('/(^(?!api).*)(\/(bic|characters|freecompanies|pvpteams|linkshells|crossworldlinkshells|crossworld_linkshells|achievements|sections|threads|users)\/)([a-zA-Z\d]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        #Update REQUEST_URI to ensure the data returned will be consistent
        $_SERVER['REQUEST_URI'] = self::$canonical;
        #For canonical, though, we need to ensure, that it does have a trailing slash
        if (preg_match('/\/\?/u', self::$canonical) !== 1) {
            self::$canonical = preg_replace('/([^\/])$/u', '$1/', self::$canonical);
        }
        #And also return page or search query, if present
        self::$canonical .= '?'.http_build_query([
            #Do not add 1st page as query (since it is excessive)
            'page' => empty($_GET['page']) || $_GET['page'] === '1' ? null : $_GET['page'],
            'search' => $_GET['search'] ?? null,
        ], encoding_type: PHP_QUERY_RFC3986);
        #Trim the excessive question mark, in case no query was attached
        self::$canonical = rtrim(self::$canonical, '?');
        #Trim trailing slashes if any
        self::$canonical = rtrim(self::$canonical, '/');
        #Set canonical link, that may be used in the future
        self::$canonical = 'https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Website\Config::$http_host) === 1 ? 'www.' : '').Website\Config::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        #Update list with dynamic values
        Website\Config::$links = array_merge(Website\Config::$links, [
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
                Pool::openConnection(
                    (new Config)
                        ->setHost($_ENV['DATABASE_HOST'], (int)$_ENV['MARIADB_PORT'])
                        ->setUser($_ENV['DATABASE_USER'])
                        ->setPassword($_ENV['DATABASE_PASSWORD'])
                        ->setDB($_ENV['DATABASE_NAME'])
                        ->setOption(\PDO::MYSQL_ATTR_FOUND_ROWS, true)
                        ->setOption(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET SESSION character_set_client = \'utf8mb4\',
                                                                                    SESSION collation_connection = \'utf8mb4_uca1400_nopad_as_cs\',
                                                                                    SESSION character_set_connection = \'utf8mb4\',
                                                                                    SESSION character_set_database = \'utf8mb4\',
                                                                                    SESSION character_set_results = \'utf8mb4\',
                                                                                    SESSION character_set_server = \'utf8mb4\',
                                                                                    SESSION time_zone=\'+00:00\';')
                        ->setOption(\PDO::ATTR_TIMEOUT, 1)
                        ->setOption(\PDO::MYSQL_ATTR_SSL_CA, $_ENV['DATABASE_TLS_CA'])
                        ->setOption(\PDO::MYSQL_ATTR_SSL_CERT, $_ENV['DATABASE_TLS_CRT'])
                        ->setOption(\PDO::MYSQL_ATTR_SSL_KEY, $_ENV['DATABASE_TLS_KEY'])
                        ->setOption(\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT, true), maxTries: 5);
                self::$dbup = true;
                #Cache controller
                self::$dbController = (new Controller);
                #Check for maintenance
                self::$dbUpdate = (bool)self::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\'');
                self::$dbController->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            } catch (\Throwable $exception) {
                #2002 error code means server is not listening on port
                #2006 error code means server has gone away
                #This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
                if (preg_match('/HY000.*\[(2002|2006)]/u', $exception->getMessage()) !== 1) {
                    Errors::error_log($exception);
                }
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
                $output = EnvironmentGenerator::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'hour', false);
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
                $output = EnvironmentGenerator::getTwig()->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            } catch (\Throwable $exception) {
                Errors::error_log($exception);
                Headers::clientReturn(500, false);
                try {
                    $output = EnvironmentGenerator::getTwig()->render('index.twig', array_merge(['http_error' => 500, 'reason' => 'Twig failure'], ['session_data' => $_SESSION ?? NULL]));
                } catch (\Throwable) {
                    $output = 'Complete twig failure';
                }
            }
            #Close session
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            #Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (Website\Config::$PROD && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge']) && empty($twigVars['http_error']) && self::$method === 'GET') {
                self::$dataCache->write($twigVars, age: (int)$twigVars['cacheAge']);
            }
            if (self::$staleReturn === true) {
                @ob_end_clean();
            } else {
                #Output data
                Common::zEcho($output, $twigVars['cacheStrat'] ?? 'hour', false);
            }
            exit;
        }
    }
}