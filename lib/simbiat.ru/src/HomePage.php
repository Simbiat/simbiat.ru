<?php
declare(strict_types=1);
namespace Simbiat;

use DateTimeInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Simbiat\Database\Config;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;
use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Headers;
use Simbiat\usercontrol\Bans;
use Simbiat\usercontrol\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomePage
{
    #Static value indicating whether this is a live version of the site. It's static in case a function wil be called after initial object was destroyed
    public static bool $PROD = false;
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
    #Twig
    public static ?Environment $twig = null;
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
        #Determine if test server. Currently, effects only HTML caching.
        if (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'local.simbiat.ru' && $_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR']) {
            self::$PROD = false;
        } else {
            self::$PROD = true;
        }
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
        if (is_null(self::$twig)) {
            #Initiate Twig
            self::$twig = new Environment(new FilesystemLoader($GLOBALS['siteconfig']['templatesdir']), ['cache' => $GLOBALS['siteconfig']['templatesdir'] . '/cache', 'auto_reload' => !HomePage::$PROD,]);
            self::$twig->addExtension(new TwigExtension);
        }
        #Get all POST and GET keys to lower case
        $_POST = array_change_key_case($_POST, CASE_LOWER);
        $_GET = array_change_key_case($_GET, CASE_LOWER);
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
                        #Show that client is unsupported
                        if (!empty($_SESSION['UA']['client']) && preg_match('/^(Internet Explorer|Opera Mini|Baidu|UC Browser|QQ Browser|KaiOS Browser).*/i', $_SESSION['UA']['client']) === 1) {
                            self::$http_error = ['unsupported' => true, 'client' => $_SESSION['UA']['client'], 'http_error' => 418];
                        #Show error page if DB is down
                        } elseif (!self::$dbup) {
                            self::$http_error = ['http_error' => 'database'];
                        #Show error page if maintenance is running
                        } elseif (self::$dbUpdate) {
                            self::$http_error = ['http_error' => 'maintenance'];
                        #Check if banned by IP
                        } elseif ((new Bans)->bannedIP() === true) {
                            self::$http_error = ['http_error' => 403];
                        }
                        self::$headers->links($GLOBALS['siteconfig']['links']);
                        if ($uri[0] !== 'api') {
                            @header('SourceMap: /js/' . filemtime($GLOBALS['siteconfig']['jsdir'] . 'min.js') . '.js.map', false);
                            @header('SourceMap: /css/' . filemtime($GLOBALS['siteconfig']['cssdir'] . 'min.css') . '.css.map', false);
                        }
                        #Check if we have cached the results already
                        HomePage::$staleReturn = $this->twigProc(self::$dataCache->read(), true);
                        if (self::$method === 'POST') {
                            #Process POST data if any
                            (new MainRouter)->postProcess();
                        }
                        $vars = (new MainRouter)->route($uri);
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
                self::$headers->clientReturn('403', true);
            }
        }
        #Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = strtolower(rawurldecode(trim(trim(trim(preg_replace('/(.*)(\?.*$)/iu','$1', $_SERVER['REQUEST_URI'] ?? '')), '/'))));
        #Remove bad UTF
        self::$canonical = mb_convert_encoding(self::$canonical, 'UTF-8', 'UTF-8');
        #Remove "friendly" portion of the links, but exclude API
        self::$canonical = preg_replace('/(^(?!api).*)(\/(bic|character|freecompany|pvpteam|linkshell|crossworldlinkshell|crossworld_linkshell|achievement)\/)([a-zA-Z0-9]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        #Force _ in crossworldlinkshell
        self::$canonical = preg_replace('/crossworldlinkshell/iu', 'crossworld_linkshell', self::$canonical);
        #Update REQUEST_URI to avoid potentially to ensure the data returned will be consistent
        $_SERVER['REQUEST_URI'] = self::$canonical;
        #For canonical, though, we need to ensure, that it does have a trailing slash
        if (preg_match('/\/\?/iu', self::$canonical) !== 1) {
            self::$canonical = preg_replace('/([^\/])$/iu', '$1/', self::$canonical);
        }
        #And also return page query, if present
        if (isset($_GET['page'])) {
            self::$canonical .= '?page='.$_GET['page'];
        }
        #Set canonical link, that may be used in the future
        self::$canonical = 'https://'.(preg_match('/^[a-z0-9\-_~]+\.[a-z0-9\-_~]+$/iu', $_SERVER['HTTP_HOST']) === 1 ? 'www.' : '').$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        #Update list with dynamic values
        $GLOBALS['siteconfig']['links'] = array_merge($GLOBALS['siteconfig']['links'], [
            ['rel' => 'canonical', 'href' => self::$canonical],
            ['rel' => 'stylesheet preload', 'href' => '/css/'.filemtime($GLOBALS['siteconfig']['cssdir'].'min.css').'.css', 'as' => 'style'],
            ['rel' => 'preload', 'href' => '/js/'.filemtime($GLOBALS['siteconfig']['jsdir'].'min.js').'.js', 'as' => 'script'],
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
                (new Pool)->openConnection((new Config)->setUser($GLOBALS['siteconfig']['database']['user'])->setPassword($GLOBALS['siteconfig']['database']['password'])->setDB($GLOBALS['siteconfig']['database']['dbname'])->setOption(\PDO::MYSQL_ATTR_FOUND_ROWS, true)->setOption(\PDO::MYSQL_ATTR_INIT_COMMAND, $GLOBALS['siteconfig']['database']['settings'])->setOption(\PDO::ATTR_TIMEOUT, 1));
                self::$dbup = true;
                #Cache controller
                self::$dbController = (new Controller);
                #Check for maintenance
                self::$dbUpdate = boolval(self::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\''));
                self::$dbController->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
                #Try to start session if it's not started yet, and we are not serving stale content
                if (!self::$CLI && session_status() === PHP_SESSION_NONE && !self::$staleReturn) {
                    session_set_save_handler(new Session, true);
                    session_start();
                }
            } catch (\Throwable $error) {
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
                    $twigVars = array_merge($twigVars, self::$http_error);
                    ob_end_clean();
                    ignore_user_abort(true);
                    ob_start();
                    @header('Connection: close');
                    $output = self::$twig->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
                    #Output data
                    (new Common)->zEcho($output, exit: false);
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
                $output = self::$twig->render($twigVars['template_override'] ?? 'index.twig', $twigVars);
            } catch (\Throwable) {
                $output = 'Twig failure';
            }
            #Close session
            if (session_status() === PHP_SESSION_ACTIVE && !self::$staleReturn) {
                session_write_close();
            }
            #Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (self::$PROD && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge']) && empty($twigVars['http_error']) && self::$method === 'GET') {
                self::$dataCache->write($twigVars, age: intval($twigVars['cacheAge']));
            }
            if (self::$staleReturn === true) {
                @ob_end_clean();
            } else {
                #Output data
                (new Common)->zEcho($output, exit: true);
            }
            exit;
        }
    }

    #Helper function to send mails
    public static function sendMail(string $to, string $subject, string $body, bool $debug = false): bool
    {
        $mail = new PHPMailer(true);
        try {
            #Server settings
            #Enable verbose debug output
            if ($debug) {
                $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
                $mail->Debugoutput = 'html';
            } else {
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->Debugoutput = 'error_log';
            }
            #Send using SMTP
            $mail->isSMTP();
            #Set the SMTP server to send through
            $mail->Host = $GLOBALS['siteconfig']['smtp']['host'];
            #Enable SMTP authentication
            $mail->SMTPAuth = true;
            $mail->SMTPAutoTLS = true;
            $mail->AuthType = 'LOGIN';
            #SMTP username
            $mail->Username = $GLOBALS['siteconfig']['smtp']['user'];
            #SMTP password
            $mail->Password = $GLOBALS['siteconfig']['smtp']['password'];
            #Enable implicit TLS encryption
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            #Recipients
            $mail->setFrom($GLOBALS['siteconfig']['smtp']['from'], $GLOBALS['siteconfig']['site_name'], false);
            $mail->addAddress($to);
            $mail->addReplyTo($GLOBALS['siteconfig']['adminmail'], $GLOBALS['siteconfig']['site_name']);

            #DKIM
            $mail->DKIM_domain = $mail->Host;
            $mail->DKIM_private = $GLOBALS['siteconfig']['DKIM']['key'];
            $mail->DKIM_selector = 'DKIM';
            $mail->DKIM_passphrase = '';
            $mail->DKIM_identity = $mail->From;

            #Content
            #Set email format to HTML
            $mail->isHTML(true);
            #Use UTF8
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
}
