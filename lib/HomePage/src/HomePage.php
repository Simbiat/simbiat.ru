<?php
declare(strict_types=1);
namespace Simbiat;

#Some functions in this class can be realized in .htaccess files, but I am implementing the logic in PHP for more control and less dependencies on server software (not all web servers support htaccess files)

use Simbiat\Database\Config;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;
use Simbiat\HTTP20\Common;
use Simbiat\HTTP20\Headers;
use Simbiat\HTTP20\Meta;
use Simbiat\HTTP20\Sharing;
use Simbiat\usercontrol\Bans;
use Simbiat\usercontrol\Security;
use Simbiat\usercontrol\Session;
use Simbiat\usercontrol\Signinup;
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
    #HTMLCache object
    public static ?HTMLCache $HTMLCache = NULL;
    #HTTP headers object
    public static ?Headers $headers = NULL;

    public function __construct(bool $PROD = false)
    {
        #Set output compression to 9 for consistency
        ini_set('zlib.output_compression_level', '9');
        #Enforce UTC timezone
        ini_set('date.timezone', 'UTC');
        date_default_timezone_set('UTC');
        #Set path to error log
        ini_set('error_log', getcwd().'/error.log');
        #Update static value
        self::$PROD = $PROD;
        #Enable/disable display of errors
        ini_set('display_errors', strval(intval(!self::$PROD)));
        ini_set('display_startup_errors', strval(intval(!self::$PROD)));
        #Cache headers object
        self::$headers = new Headers;
    }

    public function canonical(): void
    {
        #Force HTTPS
        $this->forceSecure();
        #Force WWW
        $this->forceWWW();
        #Trim URI
        $this->trimURI();
        #Set canonical link, that may be used in the future
        self::$canonical = 'https://'.(preg_match('/^[a-z0-9\-_~]+\.[a-z0-9\-_~]+$/', $_SERVER['HTTP_HOST']) === 1 ? 'www.' : '').$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/';
    }

    #Redirect to HTTPS
    private function forceSecure(int $port = 443): void
    {
        if (
                #If HTTPS is not set or is set as 'off' - assume HTTP protocol
                (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') &&
                #If the above is true, it does not mean we are on HTTP, because there can be a special reverse proxy/balancer case. Thus we check X-FORWARDED-* headers
                (empty($_SERVER['HTTP_X_FORWARDED_PROTO']) || $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') &&
                (empty($_SERVER['HTTP_X_FORWARDED_SSL']) || $_SERVER['HTTP_X_FORWARDED_SSL'] === 'off') &&
                #This one is for Microsoft balancers and apps
                (empty($_SERVER['HTTP_FRONT_END_HTTPS']) || $_SERVER['HTTP_FRONT_END_HTTPS'] === 'off')
            ) {
            #Redirect to HTTPS, while keeping the port, in case it's not standard
            self::$headers->redirect('https://'.$_SERVER['HTTP_HOST'].($port !== 443 ? ':'.$port : '').$_SERVER['REQUEST_URI'], true, true, false);
        }
    }

    #Function to force www version of the website, unless on subdomain
    private function forceWWW(int $port = 443): void
    {
        if (preg_match('/^[a-z0-9\-_~]+\.[a-z0-9\-_~]+$/', $_SERVER['HTTP_HOST']) === 1) {
            #Redirect to www version
            self::$headers->redirect('https://'.'www.'.$_SERVER['HTTP_HOST'].($port != 443 ? ':'.$port : '').$_SERVER['REQUEST_URI'], true, true, false);
        }
    }

    #Function to trim request URI from whitespace, slashes, and then whitespaces before slashes
    private function trimURI(): void
    {
        $_SERVER['REQUEST_URI'] = rawurldecode(trim(trim(trim($_SERVER['REQUEST_URI']), '/')));
    }

    #Function returns version of the file based on number of files and date of the newest file
    public function filesVersion(string|array $files): string
    {
        #Check if a string
        if (is_string($files)) {
            #Convert to array
           $files = [$files];
        }
        #Prepare array of dates
        $dates = [];
        #Iterate array
        foreach ($files as $file) {
            #Check if string is file
            if (is_file($file)) {
                #Add date to list
                $dates[] = filemtime($file);
            } else {
                #Check if directory
                if (is_dir($file)) {
                    $fileList = (new \RecursiveIteratorIterator((new \RecursiveDirectoryIterator($file, \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::SKIP_DOTS)), \RecursiveIteratorIterator::SELF_FIRST));
                    foreach ($fileList as $subFile) {
                        #Add date to list
                        $dates[] = $subFile->getMTime();
                    }
                }
            }
        }
        return strval(max($dates));
    }

    #Function to process some special files

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function filesRequests(string $request): int
    {
        #Remove query string, if present (that is everything after ?)
        $request = preg_replace('/^(.*)(\?.*)?$/', '$1', $request);
        if (preg_match('/^browserconfig\.xml$/i', $request) === 1) {
            #Process MS Tile
            (new Meta)->msTile($GLOBALS['siteconfig']['mstile'], [], [], true, true);
        } elseif (preg_match('/^js\/\d+\.js$/i', $request) === 1) {
            #Process JS
            (new Common)->reductor($GLOBALS['siteconfig']['jsdir'], 'js', false, '', 'aggressive');
        } elseif (preg_match('/^css\/\d+\.css$/i', $request) === 1) {
            #Process CSS
            (new Common)->reductor($GLOBALS['siteconfig']['cssdir'], 'css', true, '', 'aggressive');
        } elseif (preg_match('/^img\/fftracker\/.*$/i', $request) === 1) {
            #Process FFTracker images
            #Get real path
            if (preg_match('/^(img\/fftracker\/avatar\/)(.+)$/i', $request) === 1) {
                $imgPath = preg_replace('/^(img\/fftracker\/avatar\/)(.+)/i', 'https://img2.finalfantasyxiv.com/f/$2', $request);
                (new Sharing)->proxyFile($imgPath, 'week');
            } elseif (preg_match('/^(img\/fftracker\/icon\/)(.+)$/i', $request) === 1) {
                $imgPath = preg_replace('/^(img\/fftracker\/icon\/)(.+)/i', 'https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/$2', $request);
                (new Sharing)->proxyFile($imgPath, 'week');
            } else {
                $imgPath = (new FFTracker)->ImageShow(preg_replace('/^img\/fftracker\//i', '', $request));
                #Output the image
                (new Sharing)->fileEcho($imgPath);
            }
        } elseif (preg_match('/^(favicon\.ico)|(img\/favicons\/favicon\.ico)$/i', $request) === 1) {
            #Process favicon
            (new Sharing)->fileEcho($GLOBALS['siteconfig']['favicon']);
        } elseif (preg_match('/^(bic)($|\/.*)/i', $request) === 1) {
            self::$headers->redirect('https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '') . '/' . (preg_replace('/^(bic)($|\/.*)/i', 'bictracker$2', $request)), true, true, false);
        } elseif (is_file($GLOBALS['siteconfig']['maindir'].'/static/'.$request)) {
            #Check if exists in static folder
            return (new Sharing)->fileEcho($GLOBALS['siteconfig']['maindir'].'/static/'.$request, allowedMime: $GLOBALS['siteconfig']['allowedMime'], exit: true);
        } elseif (is_file($GLOBALS['siteconfig']['maindir'].$request)) {
            #Attempt to send the file
            if (preg_match('/^('.implode('|', $GLOBALS['siteconfig']['prohibited']).').*$/i', $request) === 0) {
                return (new Sharing)->fileEcho($GLOBALS['siteconfig']['maindir'].$request, allowedMime: $GLOBALS['siteconfig']['allowedMime'], exit: true);
            } else {
                return 403;
            }
        } else {
            #Create HTMLCache object to check for cache
            self::$HTMLCache = (new HTMLCache($GLOBALS['siteconfig']['cachedir'].'html/'));
            #Attempt to use cache
            $output = self::$HTMLCache->get('', true, false);
            if (!empty($output)) {
                #Cache hit, we need to connect to DB and initiate session to write data about it
                if ($this->dbConnect(true) === true) {
                    #Process POST data if any
                    (new HomeRouter)->postProcess();
                    #Close session right after if it opened
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_write_close();
                    }
                }
                self::$HTMLCache->cacheOutput($output);
            }
        }
        #Return 0, since we did not hit anything
        return 0;
    }

    #Function to send headers common for all items
    public function commonHeaders(): void
    {
        self::$headers->performance()->secFetch()->security('strict', [], [], [], ['GET', 'HEAD', 'POST']);
    }

    #Function to send HTML only headers
    public function htmlHeaders(): void
    {
        self::$headers->features(['web-share'=>'\'self\''])->contentPolicy($GLOBALS['siteconfig']['allowedDirectives'], false);
    }

    #Function to send common Link headers
    public function commonLinks(): void
    {
        #Update list with dynamic values
        $GLOBALS['siteconfig']['links'] = array_merge($GLOBALS['siteconfig']['links'], [
            ['rel' => 'canonical', 'href' => 'https://'.(preg_match('/^[a-z0-9\-_~]+\.[a-z0-9\-_~]+$/', $_SERVER['HTTP_HOST']) === 1 ? 'www.' : '').$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.$_SERVER['REQUEST_URI']],
            ['rel' => 'stylesheet preload', 'href' => '/css/'.$this->filesVersion($GLOBALS['siteconfig']['cssdir']).'.css', 'as' => 'style'],
            ['rel' => 'preload', 'href' => '/js/'.$this->filesVersion($GLOBALS['siteconfig']['jsdir']).'.js', 'as' => 'script'],
        ]);
        #Send headers
        self::$headers->links($GLOBALS['siteconfig']['links']);
    }

    #Database connection

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function dbConnect(bool $extraChecks = false): bool
    {
        #Check in case we accidentally call this for 2nd time
        if (self::$dbup === false) {
            try {
                (new Pool)->openConnection((new Config)->setUser($GLOBALS['siteconfig']['database']['user'])->setPassword($GLOBALS['siteconfig']['database']['password'])->setDB($GLOBALS['siteconfig']['database']['dbname'])->setOption(\PDO::MYSQL_ATTR_FOUND_ROWS, true)->setOption(\PDO::MYSQL_ATTR_INIT_COMMAND, $GLOBALS['siteconfig']['database']['settings']));
                self::$dbup = true;
                #In some cases these extra checks are not required
                if ($extraChecks === true) {
                    #Check if maintenance
                    if ((new Controller)->selectValue('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\'') == 1) {
                        $this->twigProc(error: 5032);
                    }
                    #Check if banned
                    if ((new Bans)->bannedIP() === true) {
                        $this->twigProc(error: 403);
                    }
                }
            } catch (\Exception $e) {
                self::$dbup = false;
                return false;
            }
        }
        if ($extraChecks === true) {
            #Try to start session. It's not critical for the whole site, thus it's ok for it to fail
            if (session_status() !== PHP_SESSION_DISABLED) {
                #Use custom session handler
                session_set_save_handler(new Session, true);
                session_start();
                if (!empty($_SESSION['UA']['client']) && preg_match('/^Internet Explorer.*/i', $_SESSION['UA']['client']) === 1) {
                    $this->twigProc(['unsupported' => true, 'client' => $_SESSION['UA']['client']], 418, 'aggressive');
                }
                #Process POST data if any
                (new HomeRouter)->postProcess();
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
    public function twigProc(array $extraVars = [], ?int $error = NULL, string $cacheStrat = '')
    {
        #Set Twig loader
        $twigLoader = new FilesystemLoader($GLOBALS['siteconfig']['templatesdir']);
        #Initiate Twig itself (use caching only for PROD environment)
        $twig = new Environment($twigLoader, ['cache' => (self::$PROD ? $GLOBALS['siteconfig']['cachedir'].'/twig' : false)]);
        #Set default variables
        $twigVars = [
            'domain' => $GLOBALS['siteconfig']['domain'],
            'url' => $GLOBALS['siteconfig']['domain'].$_SERVER['REQUEST_URI'],
            'site_name' => $GLOBALS['siteconfig']['site_name'],
            'currentyear' => '-'.date('Y', time()),
        ];
        #Set versions of CSS and JS
        $twigVars['css_version'] = $this->filesVersion($GLOBALS['siteconfig']['cssdir']);
        $twigVars['js_version'] = $this->filesVersion($GLOBALS['siteconfig']['jsdir']);
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
            $twigVars = array_merge($twigVars, (new Controller)->selectPair('SELECT `setting`, `value` FROM `sys__settings`'));
            #Get sidebar
            $twigVars['sidebar']['fflinks'] = (new FFTracker)->GetLastEntities(5);
            #Show login form in sidebar, but only if we do not ahve login/registration page open
            if (preg_match('/^uc\/(registration|register|login|signin|signup|join)$/i', $_SERVER['REQUEST_URI']) !== 1) {
                $twigVars['user_side_panel'] = (new Signinup)->form();
            }
        } else {
            #Enforce 503 error
            $error = 503;
        }
        #Set error for Twig
        if (!empty($error)) {
            #Server error page
            $twigVars['http_error'] = $error;
            $twigVars['title'] = $twigVars['site_name'].': '.($error === 5032 ? 'Maintenance' : strval($error));
            $twigVars['h1'] = $twigVars['title'];
            self::$headers->clientReturn(($error === 5032 ? '503' : strval($error)), false);
        }
        #Merge with extra variables provided
        $twigVars = array_merge($twigVars, $extraVars);
        #Set title if it's empty
        if (empty($twigVars['title'])) {
            $twigVars['title'] = $twigVars['site_name'];
        } else {
            $twigVars['title'] = $twigVars['title'].' on '.$twigVars['site_name'];
        }
        #Set title if it's empty
        if (empty($twigVars['h1'])) {
            $twigVars['h1'] = $twigVars['title'];
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
        #Add meta tags
        $this->socialMeta($twigVars);
        #Add CSRF Token to meta
        $twigVars['XCSRFToken'] = $_SESSION['CSRF'] ?? (new Security)->genCSRF();
        #Render page
        $output = $twig->render('main/main.twig', $twigVars);
        #Close session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        #Cache page if cache age is setup
        if (self::$PROD && !empty($twigVars['cache_age']) && is_numeric($twigVars['cache_age'])) {
            self::$HTMLCache->set($output, '', intval($twigVars['cache_age']));
        } else {
            (new Common)->zEcho($output, $cacheStrat);
        }
        exit;
    }

    #Function to generate social media metas
    private function socialMeta(&$twigVars): void
    {
        #Cache object
        $meta = (new Meta);
        #Twitter
        $twigVars['twitter_card'] = $meta->twitter([
            'title' => (empty($twigVars['title']) ? 'Simbiat Software' : $twigVars['title']),
            'description' => (empty($twigVars['ogdesc']) ? 'Simbiat Software' : $twigVars['ogdesc']),
            'site' => 'simbiat199',
            'site:id' => '3049604752',
            'creator' => '@simbiat199',
            'creator:id' => '3049604752',
            'image' => $twigVars['domain'].'/img/favicons/simbiat.png',
            'image:alt' => 'Simbiat Software logo',
        ], [], false);
        #Facebook
        $twigVars['facebook'] = $meta->facebook(288606374482851, [100002283569233]);
        #MS Tile (for pinned sites)
        $twigVars['ms_tile'] = $meta->msTile($GLOBALS['siteconfig']['mstile'], [], [], false, false);
    }
}
