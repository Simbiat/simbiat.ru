<?php

declare(strict_types=1);

namespace App;

use App\Controller\Abstracts\Page;
use App\Controller\Api\Api;
use App\Enum\SystemUser;
use App\Security\Security;
use App\Security\Session;
use App\Service\Caching;
use App\Service\Config;
use App\Service\Errors;
use App\Service\Routing\MainRouter;
use App\Service\Sanitization;
use App\Twig\EnvironmentGenerator;
use DateTimeInterface;
use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Yaml\Pecl;
use Simbiat\http20\Common;
use Simbiat\http20\Headers;
use Simbiat\http20\Links;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

/**
 * Class to generate pages. "HomePage" is a legacy name
 */
final class HomePage
{
    // Cache object
    private(set) static ?Caching $data_cache = null;

    // HTTP headers object
    private(set) static ?Headers $headers = null;

    // Flag indicating that cached view has been served already
    private(set) static bool $stale_return = false;

    // HTTP method being used
    public static ?string $method = null;

    // Array that can contain variables indicating common HTTP errors
    private(set) static ?array $http_error = [];

    // User agent details from
    private(set) static array $user_agent = [];
    public static array $links = [];
    public static string $canonical = '';
    public static ?DeviceDetector $device_detector = null;

    public function __construct()
    {
        // Cache headers object
        self::$headers = new Headers();
        self::$data_cache ??= new Caching();
        // Set method
        self::$method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? $_SERVER['REQUEST_METHOD'] ?? null;
        // Initiate device detector
        // Force full string versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        self::$device_detector = new DeviceDetector();
        self::$device_detector->setYamlParser(new Pecl());
        self::$device_detector->setCache(new PSR6Bridge(new ApcuAdapter('Matomo')));
        // Parse multipart/form-data for PUT/DELETE/PATCH methods (if any)
        Headers::multiPartFormParse();
        if (\in_array(self::$method, ['PUT', 'DELETE', 'PATCH'], true)) {
            $_POST = \array_change_key_case(Headers::$_PUT ?: Headers::$_DELETE ?: Headers::$_PATCH ?: []);
            $_FILES = Headers::$_FILES;
        }
        // Get all POST and GET keys to the lower case
        $_POST = \array_change_key_case($_POST);
        Sanitization::carefulArraySanitization($_POST);
        $_GET = \array_change_key_case($_GET);
        Sanitization::carefulArraySanitization($_GET);
        $this->init();
    }

    /**
     * Initial routing logic
     *
     * @return void
     */
    private function init(): void
    {
        // \Simbiat\Translit\Unicode::whatIsTransliterated();
        // \Simbiat\Website\Errors::dump(\Simbiat\Translit\Convert::caseVariations('OSDATA'));
        // echo 'here';
        // exit(0);

        // Set default Session shape
        $_SESSION = [
            'banned' => false,
            'csrf' => null,
            'permissions' => [
                'view_bic',
                'view_ff',
                'view_posts',
            ],
            'prev_page' => null,
            'timezone' => 'UTC',
            'user_id' => SystemUser::Unknown->value,
        ];
        try {
            // Maybe a client is using HTTP1.0, and there is little to worry about, but maybe there is.
            if (empty($_SERVER['HTTP_HOST'])) {
                Headers::clientReturn(403);
            }
            self::canonical();
            self::nonApiLinks();
            // Redirect if the page number is set and is less than 1
            if (
                \array_key_exists('page', $_GET)
                && (int) $_GET['page'] < 1
            ) {
                // Remove page (since we ignore page=1 in canonical)
                Headers::redirect(\preg_replace('/\\?page=-?\d+/ui', '', self::$canonical));
            }
            // Process requests to file or cache
            $this->filesRequests();
            // Exploding further processing
            /* @noinspection NotOptimalRegularExpressionsInspection False positive, since does not know what can be in the string */
            $uri = \explode('/', \preg_replace('/^(\/)([^?]*)(\?'.\preg_quote($_SERVER['QUERY_STRING'] ?? '', '/').')?/ui', '$2', $_SERVER['REQUEST_URI']));
            // Check if there was an internal redirect to a custom error page.
            // If there was no Caddy error, then the value of the variable will be `{http.error.status_code}`. Otherwise - it will be a numeric HTTP code.
            if (
                !empty($_SERVER['CADDY_HTTP_ERROR'])
                && \is_numeric($_SERVER['CADDY_HTTP_ERROR'])
            ) {
                self::$http_error = ['http_error' => $_SERVER['CADDY_HTTP_ERROR'], 'reason' => $_SERVER['CADDY_HTTP_ERROR_MSG'] ?? ''];
            }
            // Suppress inspection, since we only need headers to be sent
            /** @noinspection UnusedFunctionResultInspection */
            Links::links(self::$links, force_cross_origin: true);
            // Send standard headers
            if ($uri[0] === 'api') {
                Api::headers();
            } else {
                Page::headers();
            }
            if (\array_key_exists('http_error', self::$http_error)) {
                $vars = self::$http_error;
            } else {
                try {
                    // Connect to DB
                    Config::dbConnect();
                    if (Config::$db_update) {
                        // Show an error page if maintenance is running
                        self::$http_error = ['http_error' => 503, 'reason' => 'Site is under maintenance and temporary unavailable'];
                    } elseif (!Config::$dbup) {
                        // Show an error page if DB is down
                        self::$http_error = ['http_error' => 503, 'reason' => 'Failed to connect to database'];
                    }
                    // Get user agent details
                    self::$user_agent = self::getUA();
                    // Show that the client is unsupported
                    if (self::$user_agent['unsupported'] === true) {
                        self::$http_error = ['client' => self::$user_agent['client'] ?? 'Teapot', 'http_error' => 418, 'reason' => 'Teapot'];
                    }
                    // Clear POST data if bot was detected to prevent potential abuse
                    if (!empty(self::$user_agent['bot'])) {
                        $_POST = [];
                    }
                    // Block some bots, in case they somehow got through CrowdSec, but were detected by Matomo (unlikely to happen, this is precaution)
                    // Also block any bot known as AI one
                    if (
                        !empty(self::$user_agent['bot'])
                        &&
                        (
                            \array_key_exists('ai', self::$user_agent)
                            && self::$user_agent['ai'] === true
                        )
                    ) {
                        self::$http_error = ['http_error' => 403, 'reason' => 'Bad bot'];
                    }
                    // Handle Sec-Fetch. Use strict mode if the request is not from a known bot and is from a known browser (bots and non-browser applications like libraries may not have Sec-Fetch headers)
                    Headers::secFetch(strict: (empty(self::$user_agent['bot']) && self::$user_agent['browser']));
                    // Try to start a session if it's not started yet and DB is up. Do not do it if the cache is being returned, if an error has been detected already or if a bot was detected
                    if (
                        empty(self::$user_agent['bot'])
                        && (
                            self::$http_error === null
                            || self::$http_error === []
                        )
                        && Config::$dbup
                        && !Config::$db_update
                        && !self::$stale_return
                        && \session_status() === \PHP_SESSION_NONE
                    ) {
                        \session_set_save_handler(new Session(), true);
                        if (!\session_start()) {
                            throw new \RunTimeException('Failed to start session');
                        }

                        // Check if banned IP
                        if (!empty($_SESSION['banned_ip'])) {
                            self::$http_error = ['http_error' => 403, 'reason' => 'Banned IP'];
                        }
                        if (
                            \array_key_exists('banned', $_SESSION)
                            && $_SESSION['banned'] === true
                            && \preg_match('/^\/about\/contacts$/ui', $_SERVER['REQUEST_URI']) !== 1
                        ) {
                            self::$http_error = ['http_error' => 403, 'reason' => 'Banned user'];
                        }
                    }
                    // Check if we have cached the results already
                    self::$stale_return = $this->twigProc(self::$data_cache->read(), true, $uri[0] === 'api');
                    // We go to router in any case, since error checks will happen in Page class
                    $vars = new MainRouter()->route($uri);
                } catch (\Throwable $exception) {
                    Errors::error_log($exception);
                    $vars = ['http_error' => 500];
                }
            }
            if (
                $uri[0] === 'api'
                && empty($vars['template_override'])
            ) {
                $vars['template_override'] = 'common/pages/api.twig';
            }
            if (
                $uri[0] === 'api'
                && empty($vars['json_ready'])
            ) {
                $vars['json_ready'] = null;
            }
            // Generate page
            $this->twigProc(\array_merge($vars, ['request_from_bot' => self::$user_agent['bot'] ?? null]), false, $uri[0] === 'api');
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
        }
    }

    /**
     * Function to process some special files
     *
     * @return void
     */
    public function filesRequests(): void
    {
        // Remove query string, if present (that is everything after ?)
        $request = \preg_replace('/^(.*)(\?.*)?$/u', '$1', $_SERVER['REQUEST_URI']);
        if (\preg_match('/^\/\.well-known\/security\.txt$/iu', $request) !== 1) {
            return;
        }

        // Send headers that will identify this as an actual file
        if (!\headers_sent()) {
            \header('Content-Type: text/plain; charset=utf-8');
            \header('Content-Disposition: inline; filename="security.txt"');
        }
        if (
            self::$method !== 'HEAD'
            && self::$method !== 'OPTIONS'
        ) {
            $this->twigProc(['template_override' => 'about/security.txt.twig', 'expires' => \date(DateTimeInterface::RFC3339_EXTENDED, \strtotime('last Monday of next month midnight'))]);
        }
        exit(0);
    }

    /**
     * Twig processing of the generated page
     *
     * @param array $twig_vars List of Twig variables
     * @param bool  $cache     Indicates if this is a cache pass
     * @param bool  $api       Whether generation is for API
     *
     * @return bool
     */
    final public function twigProc(array $twig_vars = [], bool $cache = false, bool $api = false): bool
    {
        if (self::$method === 'OPTIONS') {
            exit(0);
        }
        if (
            $cache
            && (
                $twig_vars === []
                || self::$method !== 'GET'
                || \array_key_exists('cachereset', $_GET)
                || \array_key_exists('cachereset', $_POST)
            )
        ) {
            return false;
        }
        // Update CSRF token
        if (
            !$api
            && \session_status() === \PHP_SESSION_ACTIVE
        ) {
            $_SESSION['csrf'] = Security::genToken();
            if (!\headers_sent()) {
                \header('X-CSRF-Token: '.$_SESSION['csrf']);
            }
        }
        $twig_vars = \array_merge($twig_vars, self::$http_error, ['session_data' => $_SESSION ?? null]);
        if (
            \array_key_exists('http_error', $twig_vars)
            && \is_numeric($twig_vars['http_error'])
        ) {
            Headers::clientReturn($twig_vars['http_error'], false);
        }
        if ($cache) {
            try {
                \ob_end_clean();
                \ignore_user_abort(true);
                \ob_start();
                $output = EnvironmentGenerator::getTwig()->render($twig_vars['template_override'] ?? 'index.twig', $twig_vars);
                // Output data
                Common::zEcho($output, $twig_vars['cache_strategy'] ?? 'hour', false);
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @\ob_end_flush();
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @\ob_flush();
                \flush();
                if (
                    !empty($twig_vars['cache_expires_at'])
                    && ($twig_vars['cache_expires_at'] - \time()) > 0
                ) {
                    exit(0);
                }

                return true;
            } catch (\Throwable) {
                return false;
            }
        } else {
            // TODO: Needs to be cleaned up during refactor of pages
            // Handling strict variables
            foreach (
                [
                         // common/layout/metatags.twig
                         'og_image', 'ogtype', 'ogextra', 'favicon', 'service_name', 'title', 'error_page', 'static_page', 'cached_page', 'construction', 'suggested_link',
                         // index.twig
                         'link_extra', 'http_error', 'reason', 'pagination',
                         // common/layout/navigation.twig
                         'type', 'detailed_type', 'section_id', 'subservice_name', 'breadcrumbs',
                         // common/layout/header.twig
                         'cache_reset',
                         // talks/forms/thread.twig
                         'contact_form',
                     ] as $variable
            ) {
                if (!\array_key_exists($variable, $twig_vars)) {
                    $twig_vars[$variable] = null;
                }
            }
            \ob_start();
            try {
                $output = EnvironmentGenerator::getTwig()->render($twig_vars['template_override'] ?? 'index.twig', $twig_vars);
            } catch (\Throwable $exception) {
                Errors::error_log($exception);
                Headers::clientReturn(500, false);
                try {
                    // TODO: Needs to be cleaned up during refactor of pages
                    $output = EnvironmentGenerator::getTwig()->render('index.twig', [
'breadcrumbs' => [],
'cached_page' => false,
// common/layout/header.twig
                        'cache_reset' => null,
'construction' => false,
'detailed_type' => null,
'error_page' => 500,
'favicon' => null,
'http_error' => 500,
// index.twig
                        'link_extra' => null,
'ogextra' => null,
'ogtype' => null,
// common/layout/metatags.twig
                        'og_image' => null,
'pagination' => null,
'reason' => (\preg_match('/(Variable "[^"]+" does not exist)|(Key "[^"]+" does not exist as the sequence)|(Key "[^"]+" for sequence\/mapping with keys "[^"]+" does not exist)/ui', $exception->getMessage()) === 1 ? $exception->getMessage() : 'Twig failure'),
'request_from_bot' => null,
'section_id' => null,
'service_name' => null,
'session_data' => $_SESSION ?? null,
'static_page' => false,
'subservice_name' => null,
'title' => null,
// common/layout/navigation.twig
                        'type' => null,
                    ]);
                } catch (\Throwable $twig_error) {
                    Errors::error_log($twig_error);
                    $output = 'Complete twig failure';
                }
            }
            // Close session
            if (\session_status() === \PHP_SESSION_ACTIVE) {
                \session_write_close();
            }
            // Cache page if cache age is set up, no errors, GET method is used, and we are on PROD
            if (
                Config::$environment === 'prod'
                && !empty($twig_vars['cache_age'])
                && \is_numeric($twig_vars['cache_age'])
                && empty($twig_vars['http_error'])
                && self::$method === 'GET'
            ) {
                self::$data_cache->write($twig_vars, age: (int) $twig_vars['cache_age']);
            }
            if (self::$stale_return) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @\ob_end_clean();
            } else {
                // Output data
                Common::zEcho($output, $twig_vars['cache_strategy'] ?? 'hour', false);
            }
            exit(0);
        }
    }

    /**
     * Get Bot name, OS and Browser for user agent
     *
     * @return array
     */
    public static function getUA(): array
    {
        // Check if User Agent is present
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            // Something is fishy, so let's 418 this
            return ['unsupported' => true, 'browser' => false, 'bot' => null];
        }
        // Parse user agent
        self::$device_detector->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        self::$device_detector->setClientHints(ClientHints::factory($_SERVER));
        self::$device_detector->parse();
        // Get bot name
        $bot = self::$device_detector->getBot();
        if (\is_array($bot)) {
            // Do not waste resources on bots

            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            return ['bot' => \mb_substr($bot['name'], 0, 64, 'UTF-8'), 'os' => null, 'client' => null, 'unsupported' => false, 'browser' => false, 'ai' => \strncasecmp($bot['category'] ?? '', 'ai', 2) === 0];
        }
        // Get OS
        $os = self::$device_detector->getOs();
        // Concat OS and version
        $os = \mb_trim(($os['name'] ?? '').' '.($os['version'] ?? ''), null, 'UTF-8');
        // Force OS to be NULL if it's empty
        if (empty($os)) {
            $os = null;
        }
        // Get client
        $browser = self::$device_detector->isBrowser();
        $client = self::$device_detector->getClient();
        // Check if a client is supported
        $unsupported =
            \preg_match('/^(Internet Explorer|Opera Mini|Baidu|UC Browser|QQ Browser|KaiOS Browser)/ui', $client['name'] ?? '') === 1 ||
            (
                \array_key_exists($client['name'] ?? '', Config::$teapot_browsers) &&
                (
                    empty($client['version']) ||
                    \version_compare($client['version'], Config::$teapot_browsers[$client['name']], 'lt')
                )
            )
         ? true : false;
        // Concat client and version
        $client = \mb_trim(($client['name'] ?? '').' '.($client['version'] ?? ''), null, 'UTF-8');
        // Force the client to be NULL if it's empty
        if (empty($client)) {
            $client = null;
        }

        return ['bot' => null, 'os' => ($os !== null ? \mb_substr($os, 0, 100, 'UTF-8') : null), 'client' => ($client !== null ? \mb_substr($client, 0, 100, 'UTF-8') : null), 'full' => $_SERVER['HTTP_USER_AGENT'], 'unsupported' => $unsupported, 'browser' => $browser];
    }

    /**
     * Generate a canonical link.
     */
    public static function canonical(): void
    {
        // Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = \mb_strtolower(\rawurldecode(\mb_trim(\mb_trim(\mb_trim(\preg_replace('/(.*)(\?.*$)/u', '$1', $_SERVER['REQUEST_URI'] ?? ''), null, 'UTF-8'), '/', 'UTF-8'), null, 'UTF-8')), 'UTF-8');
        // Remove bad UTF
        self::$canonical = \mb_scrub(self::$canonical, 'UTF-8');
        // Remove the "friendly" portion of the links but exclude API
        self::$canonical = \preg_replace('/(^(?!api).*)(\/(bic|characters|freecompanies|pvpteams|linkshells|crossworldlinkshells|crossworld_linkshells|achievements|sections|threads|users)\/)([a-zA-Z\d]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        // Update REQUEST_URI to ensure the data returned will be consistent
        // For canonical, though, we need to ensure that it does have a trailing slash
        if (\preg_match('/\/\?/u', self::$canonical) !== 1) {
            self::$canonical = \preg_replace('/([^\/])$/u', '$1/', self::$canonical);
        }
        // Also return some of the GET parameters that we do support
        self::$canonical .= '?'.\http_build_query([
                // Do not add the 1st page as a query (since it is excessive)
                'page' => empty($_GET['page']) || $_GET['page'] === '1' ? null : $_GET['page'],
                'search' => $_GET['search'] ?? null,
            ], encoding_type: \PHP_QUERY_RFC3986);
        // Trim the excessive question mark, in case no query was attached
        self::$canonical = \mb_rtrim(self::$canonical, '?', 'UTF-8');
        // Trim trailing slashes if any
        self::$canonical = \mb_rtrim(self::$canonical, '/', 'UTF-8');
        // Set a canonical link that may be used in the future
        self::$canonical = 'https://'.(\preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', Config::$http_host) === 1 ? 'www.' : '').Config::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        // Update the list with dynamic values
        self::$links[] = ['rel' => 'canonical', 'href' => self::$canonical];
    }

    /**
     * Add CSS and JS preload links, if not using API.
     */
    public static function nonApiLinks(): void
    {
        if (\preg_match('/^\/api(\/|$)/ui', $_SERVER['REQUEST_URI']) === 0) {
            \array_push(
                self::$links,
                ['rel' => 'stylesheet preload', 'href' => '/assets/styles/'.\filemtime(Config::$css_dir.'app.css').'.css', 'as' => 'style'],
                ['rel' => 'preload', 'href' => '/assets/app.'.\filemtime(Config::$js_dir.'app.js').'.js', 'as' => 'script'],
                ['rel' => 'manifest', 'href' => '/manifest.webmanifest', 'type' => 'application/manifest+json'],
                ['rel' => 'privacy-policy', 'href' => '/about/privacy'],
                ['rel' => 'terms-of-service', 'href' => '/about/tos'],
                ['rel' => 'help', 'href' => '/talks/sections/8', 'title' => 'Knowledgebase'],
                ['rel' => 'help', 'href' => '/about/contacts', 'title' => 'Contacts'],
            );
        }
    }
}
