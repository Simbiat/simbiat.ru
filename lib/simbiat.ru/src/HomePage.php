<?php
declare(strict_types=1);
namespace Simbiat;

use DateTimeInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Simbiat\Database\Config;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;
use Simbiat\HTTP20\Headers;
use Simbiat\usercontrol\Bans;
use Simbiat\usercontrol\Security;
use Simbiat\usercontrol\Session;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
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
    #HTMLCache object
    public static ?HTMLCache $HTMLCache = NULL;
    #HTTP headers object
    public static ?Headers $headers = NULL;
    #Flag indicating that cached view has been served already
    public static bool $staleReturn = false;
    #Flag indicating whether we are in CLI
    public static bool $CLI = false;

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
                (new Cron)->process(50);
                #Ensure we exit no matter what happens with CRON
                exit;
            } else {
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
                        #Check if API
                        if ($uri[0] === 'api') {
                            #Attempt to connect to DB
                            $this->dbConnect();
                        } else {
                            #Send links
                            $this->commonLinks();
                            #Attempt to connect to DB
                            $this->dbConnect(true);
                        }
                        if (self::$dbup || preg_match($GLOBALS['siteconfig']['static_pages'], $_SERVER['REQUEST_URI']) === 1) {
                            $vars = (new MainRouter)->route($uri);
                        } else {
                            $vars = [];
                        }
                    } catch (\Throwable $e) {
                        $this::error_log($e);
                        $vars = ['http_error' => 500];
                    }
                    #Generate page
                    $this->twigProc($vars, (empty($vars['http_error']) ? null : $vars['http_error']));
                }
            }
        } catch (\Throwable $e) {
            $this::error_log($e);
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
        #Remove "friendly" portion of the links
        self::$canonical = preg_replace('/(^.*)(\/(bic|character|freecompany|pvpteam|linkshell|crossworldlinkshell|crossworld_linkshell|achievement)\/)([a-zA-Z0-9]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
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
    }

    #Function to process some special files
    public function filesRequests(string $request): int
    {
        #Remove query string, if present (that is everything after ?)
        $request = preg_replace('/^(.*)(\?.*)?$/', '$1', $request);
        if (preg_match('/^\.well-known\/security\.txt$/i', $request) === 1) {
            #'2022-12-31T21:00:00.000Z'
            #Send headers, that will identify this as actual file
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: inline; filename="security.txt"');
            #Get content
            $content = str_replace('%expires%', date(DateTimeInterface::RFC3339_EXTENDED, strtotime('last monday of next month midnight')), file_get_contents($GLOBALS['siteconfig']['maindir'].'/static/.well-known/security.txt'));
            echo $content;
            exit;
        #Caching logic seems to be greatly affecting performance on PROD. Needs revising
        } else {
            #Create HTMLCache object to check for cache
            self::$HTMLCache = (new HTMLCache($GLOBALS['siteconfig']['cachedir'].'html/', true));
            #Attempt to use cache
            $output = self::$HTMLCache->get(self::$canonical, true, false, true);
            if (!empty($output) && !isset($_POST['cachereset']) && !isset($_GET['cachereset'])) {
                #Cache hit, we need to connect to DB and initiate session to write data about it
                try {
                    if ($this->dbConnect(true) === true) {
                        #Process POST data if any
                        (new MainRouter)->postProcess();
                        #Close session right after if it opened
                        if (session_status() === PHP_SESSION_ACTIVE) {
                            session_write_close();
                        }
                    }
                } catch (\Throwable) {
                    #Do nothing, consider cache failure
                } finally {
                    if ($output['stale']) {
                        #Cache is stale, but we output it still and then update it in background
                        header('X-Server-Cache-Stale: true');
                        self::$HTMLCache->cacheOutput($output, exit: false);
                        self::$staleReturn = true;
                    } else {
                        #Output cache regardless
                        self::$HTMLCache->cacheOutput($output);
                    }
                }
            }
        }
        #Return 0, since we did not hit anything
        return 0;
    }

    #Function to send common Link headers
    public function commonLinks(): void
    {
        #Update list with dynamic values
        $GLOBALS['siteconfig']['links'] = array_merge($GLOBALS['siteconfig']['links'], [
            ['rel' => 'canonical', 'href' => self::$canonical],
            ['rel' => 'stylesheet preload', 'href' => '/css/'.filemtime($GLOBALS['siteconfig']['cssdir'].'min.css').'.css', 'as' => 'style'],
            ['rel' => 'preload', 'href' => '/js/'.filemtime($GLOBALS['siteconfig']['jsdir'].'min.js').'.js', 'as' => 'script'],
        ]);
        #Send headers
        if (!self::$staleReturn) {
            self::$headers->links($GLOBALS['siteconfig']['links']);
            header('SourceMap: /js/' . filemtime($GLOBALS['siteconfig']['jsdir'] . 'min.js') . '.js.map', false);
            header('SourceMap: /css/' . filemtime($GLOBALS['siteconfig']['cssdir'] . 'min.css') . '.css.map', false);
        }
    }

    #Database connection
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function dbConnect(bool $extraChecks = false): bool
    {
        $healthCheck = (new Maintenance);
        #Check space availability
        $healthCheck->noSpace();
        #Check in case we accidentally call this for 2nd time
        if (self::$dbup === false) {
            try {
                (new Pool)->openConnection((new Config)->setUser($GLOBALS['siteconfig']['database']['user'])->setPassword($GLOBALS['siteconfig']['database']['password'])->setDB($GLOBALS['siteconfig']['database']['dbname'])->setOption(\PDO::MYSQL_ATTR_FOUND_ROWS, true)->setOption(\PDO::MYSQL_ATTR_INIT_COMMAND, $GLOBALS['siteconfig']['database']['settings'])->setOption(\PDO::ATTR_TIMEOUT, 1));
                self::$dbup = true;
                $healthCheck->dbDown();
                #Cache controller
                self::$dbController = (new Controller);
                #Check for maintenance
                self::$dbUpdate = boolval(self::$dbController->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\''));
                self::$dbController->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
                #In some cases these extra checks are not required
                if ($extraChecks === true) {
                    #Check if maintenance
                    if (self::$dbUpdate && preg_match($GLOBALS['siteconfig']['static_pages'], $_SERVER['REQUEST_URI']) !== 1) {
                        $this->twigProc(error: 503);
                    }
                    #Check if banned
                    if ((new Bans)->bannedIP() === true) {
                        $this->twigProc(error: 403);
                    }
                }
            } catch (\Throwable $error) {
                self::$dbup = false;
                #Trigger mail alert if PROD
                if (self::$PROD) {
                    $healthCheck->dbDown($error->getMessage()."\r\n".$error->getTraceAsString());
                }
                return false;
            }
        }
        if ($extraChecks === true) {
            #Try to start session. It's not critical for the whole site, thus it's ok for it to fail
            if (session_status() !== PHP_SESSION_DISABLED && !self::$staleReturn) {
                #Use custom session handler
                session_set_save_handler(new Session, true);
                session_start();
                if (!empty($_SESSION['UA']['client']) && preg_match('/^(Internet Explorer|Opera Mini|Baidu|UC Browser|QQ Browser|KaiOS Browser).*/i', $_SESSION['UA']['client']) === 1) {
                    $this->twigProc(['unsupported' => true, 'client' => $_SESSION['UA']['client']], 418);
                }
                #Process POST data if any
                (new MainRouter)->postProcess();
            }
        }
        return true;
    }

    #Twig processing of the generated page

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \Exception
     */
    public function twigProc(array $extraVars = [], ?int $error = NULL)
    {
        if (self::$staleReturn === true) {
            ob_start();
        }
        #Set Twig loader
        $twigLoader = new FilesystemLoader($GLOBALS['siteconfig']['templatesdir']);
        #Initiate Twig itself
        $twig = new Environment($twigLoader, ['cache' => $GLOBALS['siteconfig']['templatesdir'].'/cache', 'auto_reload' => !self::$PROD,]);
        #Set default variables
        $twigVars = [
            'domain' => $GLOBALS['siteconfig']['domain'],
            'url' => $GLOBALS['siteconfig']['domain'].'/'.$_SERVER['REQUEST_URI'],
            'site_name' => $GLOBALS['siteconfig']['site_name'],
        ];
        #Set versions of CSS and JS
        $twigVars['css_version'] = filemtime($GLOBALS['siteconfig']['cssdir'].'min.css');
        $twigVars['js_version'] = filemtime($GLOBALS['siteconfig']['jsdir'].'min.js');
        #Flag for Save-Data header
        if (isset($_SERVER['HTTP_SAVE_DATA']) && preg_match('/^on$/i', $_SERVER['HTTP_SAVE_DATA']) === 1) {
            $twigVars['save_data'] = 'true';
        } else {
            $twigVars['save_data'] = 'false';
        }
        #Set link tags
        $twigVars['link_tags'] = self::$headers->links($GLOBALS['siteconfig']['links'], 'head');
        if (self::$dbup) {
            #Update default variables with values from database
            $twigVars = array_merge($twigVars, self::$dbController->selectPair('SELECT `setting`, `value` FROM `sys__settings`'));
        } else {
            #Enforce 503 error
            $error = 503;
        }
        #Check if we are loading a static page
        if (preg_match($GLOBALS['siteconfig']['static_pages'], $_SERVER['REQUEST_URI']) === 1) {
            $twigVars['static_page'] = true;
        } else {
            $twigVars['static_page'] = false;
        }
        #Set error for Twig
        if (!empty($error)) {
            #Server error page
            $twigVars['http_error'] = (self::$dbup === false ? 'database' : (self::$dbUpdate === true ? 'maintenance' : $error));
            if (!$twigVars['static_page']) {
                $twigVars['title'] = (self::$dbup === false ? 'Database unavailable' : (self::$dbUpdate === true ? 'Site maintenance' : 'Error '.$error));
                $twigVars['h1'] = $twigVars['title'];
                if (!HomePage::$staleReturn) {
                    self::$headers->clientReturn(strval($error), false);
                }
            }
        }
        #Merge with extra variables provided
        $twigVars = array_merge($twigVars, $extraVars);
        #Set title if it's empty
        if (empty($twigVars['title'])) {
            $twigVars['title'] = $twigVars['site_name'];
            #Set H1 if it's empty
            if (empty($twigVars['h1'])) {
                $twigVars['h1'] = $twigVars['site_name'];
            }
        } else {
            #Set H1 if it's empty
            if (empty($twigVars['h1'])) {
                $twigVars['h1'] = $twigVars['title'];
            }
            #Add site name to it
            if (empty($twigVars['http_error'])) {
                $twigVars['title'] = $twigVars['title'] . ' on ' . $twigVars['site_name'];
            }
        }
        #Set OG values to global ones, if empty
        if (empty($twigVars['ogdesc'])) {
            $twigVars['ogdesc'] = $GLOBALS['siteconfig']['ogdesc'];
        }
        if (empty($twigVars['ogextra'])) {
            $twigVars['ogextra'] = $GLOBALS['siteconfig']['ogextra'];
        }
        if (empty($twigVars['ogimage'])) {
            $twigVars['ogimage'] = $GLOBALS['siteconfig']['ogimage'];
        }
        #Limit Ogdesc to 120 characters
        $twigVars['ogdesc'] = mb_substr($twigVars['ogdesc'], 0, 120, 'UTF-8');
        #Twitter
        $twigVars['twitter_card'] = $GLOBALS['siteconfig']['twitter_card'];
        #Facebook
        $twigVars['facebook'] = $GLOBALS['siteconfig']['facebook'];
        #Add CSRF Token to meta
        $twigVars['XCSRFToken'] = $_SESSION['CSRF'] ?? (new Security)->genCSRF();
        #Generate breadcrumbs
        if (!empty($twigVars['breadcrumbs'])) {
            $twigVars['breadcrumbsLevels'] = count($twigVars['breadcrumbs']);
            $twigVars['breadcrumbs'] = (new HTTP20\HTML)->breadcrumbs($twigVars['breadcrumbs']);
        }
        #Generate link for cache reset, if page uses cache
        if (!empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge'])) {
            $twigVars['cacheReset'] = parse_url(self::$canonical);
            $twigVars['cacheReset'] = self::$canonical.(empty($twigVars['cacheReset']['query']) ? '?cacheReset=true' : '&cacheReset=true');
        }
        #Render page
        $output = $twig->render('index.twig', $twigVars);
        #Close session
        if (session_status() === PHP_SESSION_ACTIVE && !self::$staleReturn) {
            session_write_close();
        }
        #Cache page if cache age is set up
        if (self::$PROD && !empty($twigVars['cacheAge']) && is_numeric($twigVars['cacheAge'])) {
            if (self::$staleReturn) {
                self::$HTMLCache->set($output, self::$canonical, intval($twigVars['cacheAge']), direct: false);
                @ob_end_clean();
            } else {
                self::$HTMLCache->set($output, self::$canonical, intval($twigVars['cacheAge']));
            }
        } else {
            if (self::$staleReturn === true) {
                @ob_end_clean();
            } else {
                echo $output;
            }
        }
        exit;
    }

    #Helper function to log errors with identifying the page
    public static function error_log(\Throwable $error, string $extra = ''): void
    {
        #Determine page link
        if (self::$CLI) {
            $page = 'CLI';
        } else {
            if (empty($_SERVER['REQUEST_URI'])) {
                $page = 'index.php';
            } else {
                $page = $_SERVER['REQUEST_URI'];
            }
        }
        error_log('Failed on `'.$page.'`'."\r\n".$error->getMessage()."\r\n".$error->getTraceAsString().(empty($extra) ? '' : "\r\n".'Extra information provided: '.$extra));
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
            self::error_log($e);
            return false;
        }
    }
}
