<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\http20\IRI;
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
    protected array $breadcrumb = [];
    #Alternative representations of the content
    protected array $alt_links = [];
    #Sub service name
    protected string $subservice_name = '';
    #Page title. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $title = '';
    #Page's H1 tag. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $h1 = '';
    #Page's description. Practically needed only for main pages of a segment, since will be overridden otherwise
    protected string $og_desc = '';
    #Page's banner. Defaults to website's banner. Needs to be inside /assets/images directory and start with /
    protected string $og_image = '';
    #Cache age, in case we prefer the generated page to be cached
    protected int $cache_age = 0;
    #Time of last data modification (defaults to current time on initialization)
    protected int $last_modified = 0;
    #Flag to check if the Last Modified header was sent already
    protected bool $header_sent = false;
    #Language override, to be sent in header (if present)
    protected string $language = '';
    #Flag to indicate this is a static page
    protected bool $static = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $session_change = false;
    #Allowed methods
    protected array $methods = ['GET', 'POST', 'HEAD', 'OPTIONS'];
    #Cache strategy: aggressive, private, none, live, month, week, day, hour
    protected string $cache_strategy = 'hour';
    #Flag indicating that authentication is required
    protected bool $authentication_needed = false;
    #List of permissions, from which at least 1 is required to have access to the page
    protected array $required_permission = [];
    #Link to JS module for preload
    protected string $js_module = '';
    #Static list of images to H2 push, which are common for the page type
    protected array $h2_push = [
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
    protected array $h2_push_extra = [];
    
    final public function __construct()
    {
        #Check that subclass has set appropriate properties
        foreach (['subservice_name', 'breadcrumb'] as $property) {
            if (empty($this->{$property})) {
                throw new \LogicException(get_class($this).' must have a non-empty `'.$property.'` property.');
            }
        }
        #Set last modified data
        $this->last_modified = time();
    }
    
    /**
     * Send common headers
     * @return void
     */
    public static function headers(): void
    {
        #Send headers
        if (!\headers_sent()) {
            \header('X-Dns-Prefetch-Control: off');
            \header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
            \header('Allow: GET, HEAD, OPTIONS');
            \header('Content-Type: text/html; charset=utf-8');
            \header('SourceMap: /assets/'.\filemtime(Config::$js_dir.'/app.js').'.js.map', false);
            \header('SourceMap: /assets/styles/'.\filemtime(Config::$css_dir.'/app.css').'.css.map', false);
            \header('NEL: {"report_to":"default","max_age":31536000,"include_subdomains":true}');
            \header('feature-policy: accelerometer \'none\'; gyroscope \'none\'; magnetometer \'none\'; camera \'none\'; microphone \'none\'; midi \'none\'; usb \'none\'; encrypted-media \'self\'; publickey-credentials-get \'self\'; geolocation \'none\'; xr-spatial-tracking \'none\'; payment \'none\'; display-capture \'none\'; web-share \'none\'; sync-xhr \'none\'; autoplay \'none\'; fullscreen \'none\'; picture-in-picture \'none\'');
            \header('permissions-policy: accelerometer=(), ambient-light-sensor=(), autoplay=(), camera=(), cross-origin-isolated=(self), display-capture=(), document-domain=(), encrypted-media=(self), fullscreen=(), geolocation=(), gyroscope=(), keyboard-map=(), magnetometer=(), microphone=(), midi=(), payment=(), picture-in-picture=(), publickey-credentials-get=(self), screen-wake-lock=(), sync-xhr=(), usb=(), web-share=(self), xr-spatial-tracking=(), clipboard-read=(self), clipboard-write=(self), gamepad=(self), speaker-selection=(), hid=(), idle-detection=(), interest-cohort=(), serial=()');
            \header('content-security-policy: upgrade-insecure-requests; default-src \'self\'; child-src \'self\'; connect-src \'self\'; font-src \'self\'; frame-src \'self\'; img-src \'self\' https://img2.finalfantasyxiv.com; manifest-src \'self\'; media-src \'self\'; object-src \'none\'; script-src \'report-sample\' \'self\'; script-src-elem \'report-sample\' \'self\'; script-src-attr \'none\'; style-src \'report-sample\' \'self\'; style-src-elem \'report-sample\' \'self\'; style-src-attr \'none\'; worker-src \'self\'; base-uri \'self\'; form-action \'self\'; frame-ancestors \'self\';');
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
        if (!$this->session_change && \session_status() === PHP_SESSION_ACTIVE) {
            \session_write_close();
        }
        #Send page language
        if (!empty($this->language) && !\headers_sent()) {
            \header('Content-Language: '.$this->language);
        }
        #Check if user has required permission
        if (!empty($this->required_permission) && empty(array_intersect($this->required_permission, $_SESSION['permissions']))) {
            $page = ['http_error' => 403, 'reason' => 'No `'.implode('` or `', $this->required_permission).'` permission'];
        } elseif (empty(HomePage::$http_error) || $this->static) {
            #Generate the page only if no prior errors detected
            #Generate a list of allowed methods
            $allowed_methods = array_unique(array_merge(['HEAD', 'OPTIONS', 'GET'], $this->methods));
            #Send headers
            if (!headers_sent()) {
                header('Access-Control-Allow-Methods: '.implode(', ', $allowed_methods));
                header('Allow: '.implode(', ', $allowed_methods));
            }
            #Check if allowed method is used
            if (!in_array(HomePage::$method, $allowed_methods, true)) {
                $page = ['http_error' => 405];
                #Check that user is authenticated
            } elseif ($this->authentication_needed && $_SESSION['user_id'] === 1) {
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
                if (!$this->header_sent) {
                    $this->lastModified($this->last_modified);
                }
                $page = array_merge($page, HomePage::$http_error);
            }
        } else {
            $page = HomePage::$http_error;
        }
        #Ensure properties are included
        $page['http_method'] = HomePage::$method;
        $page['breadcrumbs'] = $this->breadcrumb;
        $page['subservice_name'] = $this->subservice_name;
        $page['title'] = $this->title;
        $page['h1'] = $this->h1;
        $page['og_desc'] = $this->og_desc;
        if (!empty($this->og_image) && empty($page['og_image'])) {
            $page = array_merge($page, Images::ogImage($this->og_image, true));
        }
        $page['cache_age'] = $this->cache_age;
        $page['cache_strategy'] = $this->cache_strategy;
        if (!empty($this->h2_push) || !empty($this->h2_push_extra)) {
            $this->h2_push = array_merge($this->h2_push, $this->h2_push_extra);
            #Prepare a set of images to push
            foreach ($this->h2_push as $key => $image) {
                $this->h2_push[$key] = ['href' => $image, 'rel' => 'preload', 'as' => 'image'];
            }
            Links::links($this->h2_push);
        }
        if (!empty($this->alt_links) || !empty($this->js_module)) {
            if (!empty($this->js_module)) {
                $this->alt_links = array_merge($this->alt_links, [['rel' => 'modulepreload', 'href' => '/assets/controllers/'.$this->js_module.'.'.filemtime(Config::$js_dir.'/controllers/'.$this->js_module.'.js').'.js', 'as' => 'script']]);
            }
            #Send HTTP header
            if (!HomePage::$stale_return) {
                Links::links($this->alt_links);
            }
            #Add a link to HTML
            $page['link_extra'] = $this->alt_links;
        }
        #Check if we are loading a static page
        $page['static_page'] = $this->static;
        #Set error for Twig
        if (!empty($page['http_error'])) {
            if (in_array($page['http_error'], ['database', 'maintenance'])) {
                Headers::clientReturn(503, false);
            } else {
                Headers::clientReturn($page['http_error'], false);
            }
        }
        #Limit Ogdesc to 120 characters
        $page['og_desc'] = mb_substr($page['og_desc'], 0, 120, 'UTF-8');
        #Generate a link for cache reset if page uses cache
        if ($this->cache_age > 0 && !$this->static) {
            $query = IRI::parseUri(Config::$canonical);
            if (is_array($query)) {
                $page['cache_reset'] = Config::$canonical.(empty($query['query']) ? '?cache_reset=true' : '&cache_reset=true');
            } else {
                $page['cache_reset'] = '';
            }
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
        if (is_string($time)) {
            $time = strtotime($time);
        }
        #If time is less than 0, use the Last Modified set initially
        if ($time === null || $time <= 0) {
            $time = $this->last_modified;
        }
        #Set Last Modified to the time
        $this->last_modified = $time;
        #Send the header
        if (!HomePage::$stale_return) {
            Headers::lastModified($this->last_modified, true);
        }
        #Set the flag indicating, that header was sent, but we did not exit, so that the header will not be sent the 2nd time
        $this->header_sent = true;
    }
    
    /**
     * Function to append a breadcrumb, which is based on the last crumb currently set
     * @param string $path  Current path node
     * @param string $name  Current path name
     * @param bool   $query Whether the current path name is an actual node or a GET parameter
     *
     * @return void
     */
    final protected function attachCrumb(string $path, string $name, bool $query = false): void
    {
        #Add a path to breadcrumbs
        $this->breadcrumb[] = [
            'href' => $this->breadcrumb[array_key_last($this->breadcrumb)]['href'].($query ? '&' : '/').$path,
            'name' => $name,
        ];
    }
    
    /**
     * Function to get last breadcrumb's href
     * @return string
     */
    final protected function getLastCrumb(): string
    {
        return $this->breadcrumb[array_key_last($this->breadcrumb)]['href'];
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
        #`mb_convert_encoding` is done as per workaround for UTF-8 loss/corruption on loading from https://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly
        #LIBXML_HTML_NOIMPLIED and LIBXML_HTML_NOTED to avoid adding wrappers (html, body, DTD). This will also allow fewer issues in case string has both regular HTML and some regular text (outside any tags). LIBXML_NOBLANKS to remove empty tags if any. LIBXML_PARSEHUGE to allow processing of larger strings. LIBXML_COMPACT for some potential optimization. LIBXML_NOWARNING and LIBXML_NOERROR to suppress warning in case of malformed HTML. LIBXML_NONET to protect from unsolicited connections to external sources.
        $html->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_PARSEHUGE | LIBXML_COMPACT | LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
        $html->preserveWhiteSpace = false;
        $html->formatOutput = false;
        $html->normalizeDocument();
        #Remove the elements
        foreach (new \DOMXPath($html)->query('//details') as $element) {
            $element->parentNode->removeChild($element);
        }
        #Get the cleaned HTML
        $cleaned_html = $html->saveHTML();
        #Strip the excessive HTML tags if we added them
        $cleaned_html = preg_replace('/(^\s*<html( [^<>]*)?>)(.*)(<\/html>\s*$)/uis', '$3', $cleaned_html);
        $new_description = strip_tags(Cut::cut(preg_replace('/(^\s*<html( [^<>]*)?>)(.*)(<\/html>\s*$)/uis', '$3', $cleaned_html), 160, 1));
        #Update description only if it's not empty
        if (preg_match('/^\s*$/u', $new_description) === 0) {
            $this->og_desc = $new_description;
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
