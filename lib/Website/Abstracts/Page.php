<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\HomePage;
use Simbiat\HTML\Cut;
use Simbiat\http20\Headers;
use Simbiat\http20\Links;
use Simbiat\Website\Images;

use function in_array;

/**
 * General page class
 */
abstract class Page
{
    #Current breadcrumb for navigation
    protected array $breadCrumb = [];
    #Alternative representations of the content
    protected array $altLinks = [];
    #Sub service name
    protected string $subServiceName = '';
    #Page title. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of segment, since will be overridden otherwise
    protected string $ogdesc = '';
    #Page's banner. Defaults to website's banner. Needs to be inside /assets/images directory and start with /
    protected string $ogimage = '';
    #Cache age, in case we prefer the generated page to be cached
    protected int $cacheAge = 0;
    #Time of last data modification (defaults to current time on initialization)
    protected int $lastModified = 0;
    #Flag to check if Last Modified header was sent already
    protected bool $headerSent = false;
    #Language override, to be sent in header (if present)
    protected string $language = '';
    #Flag to indicate this is a static page
    protected bool $static = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = false;
    #Allowed methods
    protected array $methods = ['GET', 'POST', 'HEAD', 'OPTIONS'];
    #Cache strategy: aggressive, private, none, live, month, week, day, hour
    protected string $cacheStrat = 'hour';
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = false;
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $requiredPermission = [];
    #Link to JS module for preload
    protected string $jsModule = '';
    #Static list of images to H2 push, which are common for the page type
    protected array $h2push = [
        '/assets/images/logo.svg',
        '/assets/images/share.svg',
        '/assets/images/navigation/home.svg',
        '/assets/images/navigation/talks.svg',
        '/assets/images/navigation/fftracker.svg',
        '/assets/images/navigation/bictracker.svg',
        '/assets/images/navigation/about.svg',
        '/assets/images/navigation/simplepages.svg',
        '/assets/images/navigation/gamepad.svg',
    ];
    #List of images to H2 push, which are dependent on data grabbed by the page during generation
    protected array $h2pushExtra = [];
    
    final public function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['subServiceName', 'breadCrumb'] as $property) {
            if (empty($this->{$property})) {
                throw new \LogicException(\get_class($this).' must have a non-empty `'.$property.'` property.');
            }
        }
        #Set last modified data
        $this->lastModified = time();
    }
    
    /**
     * Send common headers
     * @return void
     */
    public static function headers(): void
    {
        #Send headers
        if (!headers_sent()) {
            header('X-Dns-Prefetch-Control: off');
            header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
            header('Allow: GET, HEAD, OPTIONS');
            header('Content-Type: text/html; charset=utf-8');
            header('SourceMap: /assets/'.filemtime(Config::$jsDir.'/app.js').'.js.map', false);
            header('SourceMap: /assets/styles/'.filemtime(Config::$cssDir.'/app.css').'.css.map', false);
            header('NEL: {"report_to":"default","max_age":31536000,"include_subdomains":true}');
            header('feature-policy: accelerometer \'none\'; gyroscope \'none\'; magnetometer \'none\'; camera \'none\'; microphone \'none\'; midi \'none\'; usb \'none\'; encrypted-media \'self\'; publickey-credentials-get \'self\'; geolocation \'none\'; xr-spatial-tracking \'none\'; payment \'none\'; display-capture \'none\'; web-share \'none\'; sync-xhr \'none\'; autoplay \'none\'; fullscreen \'none\'; picture-in-picture \'none\'');
            header('permissions-policy: accelerometer=(), ambient-light-sensor=(), autoplay=(), camera=(), cross-origin-isolated=(self), display-capture=(), document-domain=(), encrypted-media=(self), fullscreen=(), geolocation=(), gyroscope=(), keyboard-map=(), magnetometer=(), microphone=(), midi=(), payment=(), picture-in-picture=(), publickey-credentials-get=(self), screen-wake-lock=(), sync-xhr=(), usb=(), web-share=(self), xr-spatial-tracking=(), clipboard-read=(self), clipboard-write=(self), gamepad=(self), speaker-selection=(), hid=(), idle-detection=(), interest-cohort=(), serial=()');
            header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' https://img2.finalfantasyxiv.com; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
        }
    }
    
    /**
     * Get the page
     * @param array $path
     *
     * @return array|int[]
     */
    final public function get(array $path): array
    {
        #Close session early, if we know, that its data will not be changed (default)
        if (!$this->sessionChange && session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        #Send page language
        if (!empty($this->language) && !headers_sent()) {
            header('Content-Language: '.$this->language);
        }
        #Check if user has required permission
        if (!empty($this->requiredPermission) && empty(array_intersect($this->requiredPermission, $_SESSION['permissions']))) {
            $page = ['http_error' => 403, 'reason' => 'No `'.implode('` or `', $this->requiredPermission).'` permission'];
        } elseif (empty(HomePage::$http_error) || $this->static) {
            #Generate the page only if no prior errors detected
            #Generate list of allowed methods
            $allowedMethods = array_unique(array_merge(['HEAD', 'OPTIONS', 'GET'], $this->methods));
            #Send headers
            if (!headers_sent()) {
                header('Access-Control-Allow-Methods: '.implode(', ', $allowedMethods));
                header('Allow: '.implode(', ', $allowedMethods));
            }
            #Check if allowed method is used
            if (!in_array(HomePage::$method, $allowedMethods, true)) {
                $page = ['http_error' => 405];
                #Check that user is authenticated
            } elseif ($this->authenticationNeeded && $_SESSION['userid'] === 1) {
                $page = ['http_error' => 403, 'reason' => 'Authentication required'];
            } else {
                #Generate the page
                try {
                    $page = $this->generate($path);
                    #Close session if it's still open. Normally at this point all manipulations have been done.
                    if (session_status() === PHP_SESSION_ACTIVE) {
                        session_write_close();
                    }
                } catch (\Throwable $exception) {
                    if (preg_match('/(ID `.*` for entity `.*` has incorrect format\.)|(ID can\'t be empty\.)/ui', $exception->getMessage()) === 1) {
                        $page = ['http_error' => 400, 'reason' => $exception->getMessage()];
                    } else {
                        Errors::error_log($exception);
                        $page = ['http_error' => 500, 'reason' => 'Unknown error occurred'];
                    }
                }
                #Send Last Modified header to potentially allow earlier exit
                if (!$this->headerSent) {
                    $this->lastModified($this->lastModified);
                }
                $page = array_merge($page, HomePage::$http_error);
            }
        } else {
            $page = HomePage::$http_error;
        }
        #Ensure properties are included
        $page['http_method'] = HomePage::$method;
        $page['breadcrumbs'] = $this->breadCrumb;
        $page['subServiceName'] = $this->subServiceName;
        $page['title'] = $this->title;
        $page['h1'] = $this->h1;
        $page['ogdesc'] = $this->ogdesc;
        if (!empty($this->ogimage) && empty($page['ogimage'])) {
            $page = array_merge($page, Images::ogImage($this->ogimage, true));
        }
        $page['cacheAge'] = $this->cacheAge;
        $page['cacheStrat'] = $this->cacheStrat;
        if (!empty($this->h2push) || !empty($this->h2pushExtra)) {
            $this->h2push = array_merge($this->h2push, $this->h2pushExtra);
            #Prepare set of images to push
            foreach ($this->h2push as $key => $image) {
                $this->h2push[$key] = ['href' => $image, 'rel' => 'preload', 'as' => 'image'];
            }
            Links::links($this->h2push);
        }
        if (!empty($this->altLinks) || !empty($this->jsModule)) {
            if (!empty($this->jsModule)) {
                $this->altLinks = array_merge($this->altLinks, [['rel' => 'modulepreload', 'href' => '/assets/controllers/'.$this->jsModule.'.'.filemtime(Config::$jsDir.'/controllers/'.$this->jsModule.'.js').'.js', 'as' => 'script']]);
            }
            #Send HTTP header
            if (!HomePage::$staleReturn) {
                Links::links($this->altLinks);
            }
            #Add link to HTML
            $page['link_extra'] = $this->altLinks;
        }
        #Check if we are loading a static page
        $page['static_page'] = $this->static;
        #Set error for Twig
        if (!empty($page['http_error'])) {
            //$page['h1'] .= ' ('.(HomePage::$dbup === false ? 'Database unavailable' : (HomePage::$dbUpdate === true ? 'Site maintenance' : 'Error '.$page['http_error'])).')';
            if (in_array($page['http_error'], ['database', 'maintenance'])) {
                Headers::clientReturn(503, false);
            } else {
                Headers::clientReturn($page['http_error'], false);
            }
        }
        #Limit Ogdesc to 120 characters
        $page['ogdesc'] = mb_substr($page['ogdesc'], 0, 120, 'UTF-8');
        #Generate link for cache reset, if page uses cache
        if ($this->cacheAge > 0 && $this->static === false) {
            $page['cacheReset'] = parse_url(Config::$canonical, PHP_URL_QUERY);
            $page['cacheReset'] = Config::$canonical.(empty($page['cacheReset']) ? '?cacheReset=true' : '&cacheReset=true');
        }
        return $page;
    }
    
    /**
     * Generate Last-Modified header
     * @param int|string|null $time
     *
     * @return void
     */
    final protected function lastModified(int|string|null $time = null): void
    {
        #Convert string to int
        if (\is_string($time)) {
            $time = strtotime($time);
        }
        #If time is less than 0, use Last Modified set initially
        if ($time === null || $time <= 0) {
            $time = $this->lastModified;
        }
        #Set Last Modified to the time
        $this->lastModified = $time;
        #Send the header
        if (!HomePage::$staleReturn) {
            Headers::lastModified($this->lastModified, true);
        }
        #Set the flag indicating, that header was sent, but we did not exit, so that the header will not be sent the 2nd time
        $this->headerSent = true;
    }
    
    /**
     * Function to append a breadcrumb, which is based on last crumb currently set
     * @param string $path  Current path node
     * @param string $name  Current path name
     * @param bool   $query Whether current path name is an actual node or a GET parameter
     *
     * @return void
     */
    final protected function attachCrumb(string $path, string $name, bool $query = false): void
    {
        #Add path to breadcrumbs
        $this->breadCrumb[] = [
            'href' => $this->breadCrumb[array_key_last($this->breadCrumb)]['href'].($query ? '&' : '/').$path,
            'name' => $name,
        ];
    }
    
    /**
     * Function to get last breadcrumb's href
     * @return string
     */
    final protected function getLastCrumb(): string
    {
        return $this->breadCrumb[array_key_last($this->breadCrumb)]['href'];
    }
    
    /**
     * Function to set og:desc
     * @param string $string
     *
     * @return void
     */
    final protected function setOgDesc(string $string): void
    {
        #Remove <details> to avoid spoilers and generally complex items
        $string = '<html>'.$string.'</html>';
        /** @noinspection DuplicatedCode */
        $html = new \DOMDocument(encoding: 'UTF-8');
        #mb_convert_encoding is done as per workaround for UTF-8 loss/corruption on load from https://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly
        #LIBXML_HTML_NOIMPLIED and LIBXML_HTML_NOTED to avoid adding wrappers (html, body, DTD). This will also allow fewer issues in case string has both regular HTML and some regular text (outside any tags). LIBXML_NOBLANKS to remove empty tags if any. LIBXML_PARSEHUGE to allow processing of larger strings. LIBXML_COMPACT for some potential optimization. LIBXML_NOWARNING and LIBXML_NOERROR to suppress warning in case of malformed HTML. LIBXML_NONET to protect from unsolicited connections to external sources.
        $html->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_PARSEHUGE | LIBXML_COMPACT | LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        $html->preserveWhiteSpace = false;
        $html->formatOutput = false;
        $html->normalizeDocument();
        #Get elements
        $xpath = new \DOMXPath($html);
        $elements = $xpath->query('//details');
        #Actually remove the elements
        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }
        #Get the cleaned HTML
        $cleanedHtml = $html->saveHTML();
        #Strip the excessive HTML tags, if we added them
        $cleanedHtml = preg_replace('/(^\s*<html( [^<>]*)?>)(.*)(<\/html>\s*$)/uis', '$3', $cleanedHtml);
        $newDesc = strip_tags(Cut::Cut(preg_replace('/(^\s*<html( [^<>]*)?>)(.*)(<\/html>\s*$)/uis', '$3', $cleanedHtml), 160, 1));
        #Update description only if it's not empty
        if (preg_match('/^\s*$/u', $newDesc) === 0) {
            $this->ogdesc = $newDesc;
        }
    }
    
    /**
     * Generation of the page data
     * @param array $path
     *
     * @return array
     */
    abstract protected function generate(array $path): array;
}
