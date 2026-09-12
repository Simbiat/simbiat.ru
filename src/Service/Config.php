<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\SystemUser;
use App\Security\Security;
use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\AbstractParser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Yaml\Pecl;
use Pdo\Mysql;
use Simbiat\Database\Connection;
use Simbiat\Database\Pool;
use Simbiat\Database\Query;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class that holds the main settings for the website. Needs to be instantiated early as part of bootstrapping.
 */
final class Config
{
    // Handled in Symfony YAML settings
    public private(set) static string $work_dir = '';
    public private(set) static string $environment = 'dev';
    public private(set) static string $admin_email = '';
    public private(set) static string $admin_name = '';
    public private(set) static string $site_name = '';
    public private(set) static string $from_email = '';
    public private(set) static string $http_host = '';
    public private(set) static string $base_url = '';
    public private(set) static string $html_cache = '';
    public private(set) static string $security_settings = '';
    public private(set) static string $sitemap = '';
    public private(set) static string $js_dir = '';
    public private(set) static string $css_dir = '';
    public private(set) static string $img_dir = '';
    public private(set) static string $uploaded = '';
    public private(set) static string $uploaded_img = '';
    public private(set) static string $ddl_dir = '';
    public private(set) static string $geoip = '';
    public private(set) static string $crests_components = '';
    public private(set) static string $merged_crests_cache = '';
    public private(set) static string $icons = '';
    public private(set) static string $statistics = '';
    public private(set) static array $cookie_settings = [];
    public private(set) static array $group_ids = [];

    // Set of general LINKs to be sent both in HTML and in HEADER
    public private(set) static array $links = [];

    // Device detector object
    public private(set) static ?DeviceDetector $device_detector = null;
    public private(set) static array $argon_settings = [];

    // Flag indicating whether we are in CLI
    public private(set) static bool $cli = false;

    // Allow access to canonical value of the host
    public private(set) static string $canonical = '';

    // Track if the DB connection is up
    public private(set) static bool $dbup = false;

    // Maintenance flag
    public private(set) static bool $db_update = false;

    // Settings shared by PHP and JS code
    public private(set) static array $shared_with_js = [];

    public function __construct(ContainerInterface $container)
    {
        // Check if we are in CLI
        if (\preg_match('/^cli(-server)?$/iu', \PHP_SAPI) === 1) {
            self::$cli = true;
        } else {
            self::$cli = false;
        }
        // Database settings
        if (empty($_ENV['DATABASE_USER']) || empty($_ENV['DATABASE_PASSWORD']) || empty($_ENV['DATABASE_NAME']) || empty($_ENV['DATABASE_SOCKET'])) {
            throw new \RuntimeException('Missing database configuration');
        }
        if (empty($_ENV['MAILER_DSN']) || empty($_ENV['ENCRYPTION_PASSPHRASE'])) {
            throw new \RuntimeException('Missing important setting');
        }

        self::$work_dir = $container->getParameter('kernel.project_dir');
        self::$environment = $container->getParameter('kernel.environment');
        self::$admin_email = $container->getParameter('app.admin_email');
        self::$from_email = $container->getParameter('app.from_email');
        self::$security_settings = $container->getParameter('app.security_settings');
        self::$http_host = $container->getParameter('app.http_host');
        self::$base_url = $container->getParameter('app.base_url');
        self::$cookie_settings = $container->getParameter('app.cookie_settings');

        self::$admin_name = $container->getParameter('app.admin_name');
        self::$site_name = $container->getParameter('app.site_name');
        // Set directories
        self::$sitemap = $container->getParameter('app.directories.sitemap');
        self::$geoip = $container->getParameter('app.directories.geoip');
        self::$js_dir = $container->getParameter('app.directories.js');
        self::$css_dir = $container->getParameter('app.directories.css');
        self::$img_dir = $container->getParameter('app.directories.images');
        self::$uploaded = $container->getParameter('app.directories.uploaded');
        self::$uploaded_img = $container->getParameter('app.directories.uploaded_images');
        self::$ddl_dir = $container->getParameter('app.directories.ddl');
        self::$crests_components = $container->getParameter('app.directories.ffxiv.crests.components');
        self::$merged_crests_cache = $container->getParameter('app.directories.ffxiv.crests.cache');
        self::$icons = $container->getParameter('app.directories.ffxiv.icons');
        self::$statistics = $container->getParameter('app.directories.ffxiv.statistics');
        self::$html_cache = $container->getParameter('app.directories.html_cache');
        self::$group_ids = $container->getParameter('app.group_ids');

        // Generate Argon settings
        if (\count(self::$argon_settings) === 0) {
            self::$argon_settings = Security::argonCalc();
        }
        if (self::$cli) {
            // Impersonate system user
            $_SESSION['user_id'] = SystemUser::System->value;
            $_SESSION['username'] = 'System user';
            $_SESSION['permissions'] = ['close_own_threads', 'close_others_threads'];
        } else {
            // These are required only if we are outside CLI mode
            $this->canonical();
            $this->nonApiLinks();
        }
        // Load shared config
        try {
            self::$shared_with_js = \json_decode(\file_get_contents(self::$work_dir.'/build/js/shared_with_php.json'), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            // For now just logging, at the moment of writing there should not be anything critical here
            Errors::error_log($exception);
        }
        // Initiate device detector
        // Force full string versions
        AbstractDeviceParser::setVersionTruncation(AbstractParser::VERSION_TRUNCATION_NONE);
        self::$device_detector = new DeviceDetector();
        self::$device_detector->setYamlParser(new Pecl());
        self::$device_detector->setCache(new PSR6Bridge(new ApcuAdapter('Matomo')));
    }

    /**
     * Generate a canonical link
     *
     * @return void
     */
    private function canonical(): void
    {
        // Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = \mb_strtolower(\rawurldecode(mb_trim(mb_trim(mb_trim(\preg_replace('/(.*)(\?.*$)/u', '$1', $_SERVER['REQUEST_URI'] ?? ''), null, 'UTF-8'), '/', 'UTF-8'), null, 'UTF-8')), 'UTF-8');
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
        self::$canonical = mb_rtrim(self::$canonical, '?', 'UTF-8');
        // Trim trailing slashes if any
        self::$canonical = mb_rtrim(self::$canonical, '/', 'UTF-8');
        // Set a canonical link that may be used in the future
        self::$canonical = 'httpss://'.(\preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', self::$http_host) === 1 ? 'www.' : '').self::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        // Update the list with dynamic values
        self::$links[] = ['rel' => 'canonical', 'href' => self::$canonical];
    }

    /**
     * Add CSS and JS preload links, if not using API
     * @return void
     */
    private function nonApiLinks(): void
    {
        if (\preg_match('/^\/api(\/|$)/ui', $_SERVER['REQUEST_URI']) === 0) {
            \array_push(self::$links,
                ['rel' => 'stylesheet preload', 'href' => '/assets/styles/'.\filemtime(self::$css_dir.'app.css').'.css', 'as' => 'style'],
                ['rel' => 'preload', 'href' => '/assets/app.'.\filemtime(self::$js_dir.'app.js').'.js', 'as' => 'script'],
                ['rel' => 'manifest', 'href' => '/manifest.webmanifest', 'type' => 'application/manifest+json'],
                ['rel' => 'privacy-policy', 'href' => '/about/privacy'],
                ['rel' => 'terms-of-service', 'href' => '/about/tos'],
                ['rel' => 'help', 'href' => '/talks/sections/8', 'title' => 'Knowledgebase'],
                ['rel' => 'help', 'href' => '/about/contacts', 'title' => 'Contacts'],
            );
        }
    }

    /**
     * Database connection
     * @return bool
     */
    public static function dbConnect(): bool
    {
        // Check if flag file exists for an earlier exit
        if (\is_file('/app/var/log/db_maintenance.flag')) {
            self::$dbup = false;
            self::$db_update = true;
            return false;
        }
        // Check in case we accidentally call this for the 2nd time
        if (!self::$dbup) {
            try {
                new Query(Pool::openConnection(
                    new Connection()
                        ->setHost(socket: $_ENV['DATABASE_SOCKET'])
                        ->setUser($_ENV['DATABASE_USER'])
                        ->setPassword($_ENV['DATABASE_PASSWORD'])
                        ->setDB($_ENV['DATABASE_NAME'])
                        ->setOption(Mysql::ATTR_FOUND_ROWS, true)
                        ->setOption(Mysql::ATTR_INIT_COMMAND, 'SET SESSION character_set_client = \'utf8mb4\',
                                                                                    SESSION collation_connection = \'utf8mb4_0900_as_cs\',
                                                                                    SESSION character_set_connection = \'utf8mb4\',
                                                                                    SESSION character_set_database = \'utf8mb4\',
                                                                                    SESSION character_set_results = \'utf8mb4\',
                                                                                    SESSION character_set_server = \'utf8mb4\',
                                                                                    SESSION time_zone=\'+00:00\';')
                        ->setOption(\PDO::ATTR_TIMEOUT, 1), max_tries: 5));
                self::$dbup = true;
                // Check for maintenance
                try {
                    self::$db_update = (bool) Query::query('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\'', return: 'value');
                } catch (\Throwable $exception) {
                    // The most likely cause of the maintenance check to fail is if the table does not exist. If it does not, consider that we are under maintenance.
                    self::$db_update = true;
                    Errors::error_log($exception);
                }
            } catch (\Throwable $exception) {
                // 2002 error code means server is not listening on port
                // 2006 error code means server has gone away
                // This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
                if (\preg_match('/HY000.*\[(2002|2006)]/u', $exception->getMessage()) !== 1) {
                    Errors::error_log($exception);
                }
                self::$dbup = false;
                return false;
            }
        }
        return true;
    }
}
