<?php
#Supressing "too many properties" warning: this is a config class, it's supposed to be like this
/** @noinspection PhpClassHasTooManyDeclaredMembersInspection */
declare(strict_types = 1);

namespace Simbiat\Website;

#Database settings
use Dotenv\Dotenv;
use Simbiat\Database\Connection;
use Simbiat\Database\Query;
use Simbiat\Database\Pool;

/**
 * Class that holds the main settings for the website. Needs to be instantiated early as part of bootstrapping.
 */
final class Config
{
    public static bool $prod = false;
    public static string $work_dir = '';
    public const string ADMIN_MAIL = 'support@simbiat.dev';
    public const string ADMIN_NAME = 'Dmitrii Kustov';
    public const string SITE_NAME = 'Simbiat Software';
    #Mail to use to send emails from
    public const string FROM = 'noreply@simbiat.dev';
    public static string $http_host = 'www.simbiat.dev';
    public static string $base_url = 'https://www.simbiat.dev';
    public static string $html_cache = '';
    public static string $security_settings = '';
    public static string $sitemap = '';
    #Path where JS files are stored
    public static string $js_dir = '';
    #Path where CSS files are stored
    public static string $css_dir = '';
    #Path where images stored
    public static string $img_dir = '';
    #Path to uploaded files
    public static string $uploaded = '';
    #Path to uploaded images
    public static string $uploaded_img = '';
    #Folder to dump DDLs to
    public static string $ddl_dir = '';
    #GeoIP folder
    public static string $geoip = '';
    #Set of general LINKs to be sent both in HTML and in HEADER
    public static array $links = [];
    #FFTracker directories
    public static string $crests_components = '';
    public static string $merged_crests_cache = '';
    public static string $icons = '';
    public static string $statistics = '';
    #List of system user IDs
    public const array USER_IDS = [
        'Unknown user' => 1,
        'System user' => 2,
        'Deleted user' => 3,
        'Owner' => 4,
    ];
    #List of system group IDs
    public const array GROUP_IDS = [
        'Administrators' => 1,
        'Unverified' => 2,
        'Users' => 3,
        'Deleted' => 4,
        'Banned' => 5,
        'Linked to FF' => 6,
        'Bots' => 7,
    ];
    public static array $argon_settings = [];
    #Flag indicating whether we are in CLI
    public static bool $cli = false;
    #Allow access to canonical value of the host
    public static string $canonical = '';
    #Track if the DB connection is up
    public static bool $dbup = false;
    #Maintenance flag
    public static bool $db_update = false;
    #Default cookie settings
    public static array $cookie_settings = [];
    #Settings shared by PHP and JS code
    public static array $shared_with_js = [];
    
    public function __construct()
    {
        #Check if we are in CLI
        if (\preg_match('/^cli(-server)?$/i', \PHP_SAPI) === 1) {
            self::$cli = true;
        } else {
            self::$cli = false;
        }
        self::$work_dir = '/app';
        $dotenv = Dotenv::createImmutable(self::$work_dir, '.env');
        /** @noinspection UnusedFunctionResultInspection */
        $dotenv->load();
        #Database settings
        $dotenv->required(['DATABASE_USER', 'DATABASE_PASSWORD', 'DATABASE_NAME', 'DATABASE_SOCKET'])->notEmpty();
        #Other settings
        $dotenv->required(['WEB_SERVER_TEST', 'PROTON_DSN', 'ENCRYPTION_PASSPHRASE'])->notEmpty();
        self::$prod = ($_ENV['WEB_SERVER_TEST'] === 'false');
        self::$http_host = (self::$prod ? 'www.simbiat.dev' : 'localhost');
        self::$base_url = 'https://'.self::$http_host;
        self::$html_cache = self::$work_dir.'/data/cache/html/';
        self::$security_settings = self::$work_dir.'/data/security.json';
        self::$sitemap = self::$work_dir.'/data/sitemap/';
        self::$js_dir = self::$work_dir.'/public/assets';
        self::$css_dir = self::$work_dir.'/public/assets/styles/';
        self::$img_dir = self::$work_dir.'/public/assets/images';
        self::$uploaded = self::$work_dir.'/data/uploaded';
        self::$uploaded_img = self::$work_dir.'/data/uploadedimages';
        self::$ddl_dir = self::$work_dir.'/build/DDL';
        self::$crests_components = self::$work_dir.'/lib/FFXIV/CrestComponents/';
        self::$merged_crests_cache = self::$work_dir.'/data/mergedcrests/';
        self::$icons = self::$work_dir.'/lib/FFXIV/Icons/';
        self::$statistics = self::$work_dir.'/data/ffstatistics/';
        self::$geoip = self::$work_dir.'/data/geoip/';
        #Generate Argon settings
        if (empty(self::$argon_settings)) {
            self::$argon_settings = Security::argonCalc();
        }
        #Set default cookie settings
        self::$cookie_settings = [
            'expires' => \time() + 60,
            'path' => '/',
            'domain' => (self::$prod ? 'simbiat.dev' : 'localhost'),
            'secure' => true,
            'httponly' => true,
            #Adding "Partitioned" to enable CHIPS. This is a "hack" until PHP supports the setting natively. https://github.com/php/php-src/issues/12646
            'samesite' => 'Strict; Partitioned',
        ];
        #These are required only if we are outside CLI mode
        if (!self::$cli) {
            $this->canonical();
            $this->nonApiLinks();
        }
        #Load shared config
        try {
            self::$shared_with_js = \json_decode(\file_get_contents(self::$work_dir.'/public/assets/config.json'), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            #For now just logging, at the moment of writing there should not be anything critical here
            Errors::error_log($exception);
        }
    }
    
    /**
     * Generate canonical link
     * @return void
     */
    private function canonical(): void
    {
        #Trim request URI from parameters, whitespace, slashes, and then whitespaces before slashes. Also lower the case.
        self::$canonical = mb_strtolower(\rawurldecode(mb_trim(mb_trim(mb_trim(\preg_replace('/(.*)(\?.*$)/u', '$1', $_SERVER['REQUEST_URI'] ?? ''), null, 'UTF-8'), '/', 'UTF-8'), null, 'UTF-8')), 'UTF-8');
        #Remove bad UTF
        self::$canonical = mb_scrub(self::$canonical, 'UTF-8');
        #Remove the "friendly" portion of the links but exclude API
        self::$canonical = \preg_replace('/(^(?!api).*)(\/(bic|characters|freecompanies|pvpteams|linkshells|crossworldlinkshells|crossworld_linkshells|achievements|sections|threads|users)\/)([a-zA-Z\d]+)(\/?.*)/iu', '$1$2$4/', self::$canonical);
        #Update REQUEST_URI to ensure the data returned will be consistent
        $_SERVER['REQUEST_URI'] = self::$canonical;
        #For canonical, though, we need to ensure that it does have a trailing slash
        if (\preg_match('/\/\?/u', self::$canonical) !== 1) {
            self::$canonical = \preg_replace('/([^\/])$/u', '$1/', self::$canonical);
        }
        #And also return a page or search query, if present
        self::$canonical .= '?'.\http_build_query([
                #Do not add the 1st page as a query (since it is excessive)
                'page' => empty($_GET['page']) || $_GET['page'] === '1' ? null : $_GET['page'],
                'search' => $_GET['search'] ?? null,
            ], encoding_type: \PHP_QUERY_RFC3986);
        #Trim the excessive question mark, in case no query was attached
        self::$canonical = mb_rtrim(self::$canonical, '?', 'UTF-8');
        #Trim trailing slashes if any
        self::$canonical = mb_rtrim(self::$canonical, '/', 'UTF-8');
        #Set a canonical link that may be used in the future
        self::$canonical = 'https://'.(\preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', self::$http_host) === 1 ? 'www.' : '').self::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        #Update the list with dynamic values
        self::$links[] = ['rel' => 'canonical', 'href' => self::$canonical];
    }
    
    /**
     * Add CSS and JS preload links, if not using API
     * @return void
     */
    private function nonApiLinks(): void
    {
        $uri = \explode('/', $_SERVER['REQUEST_URI'], 100);
        if ($uri[0] !== 'api') {
            \array_push(self::$links, ['rel' => 'stylesheet preload', 'href' => '/assets/styles/'.\filemtime(self::$css_dir.'/app.css').'.css', 'as' => 'style'], ['rel' => 'preload', 'href' => '/assets/app.'.\filemtime(self::$js_dir.'/app.js').'.js', 'as' => 'script'], ['rel' => 'preload', 'href' => '/assets/config.'.\filemtime(self::$js_dir.'/config.json').'.json', 'as' => 'fetch', 'crossorigin' => 'same-origin', 'type' => 'application/json']);
        }
    }
    
    /**
     * Database connection
     * @return bool
     */
    public static function dbConnect(): bool
    {
        #Check in case we accidentally call this for the 2nd time
        if (!self::$dbup) {
            self::$dbup = true;
            try {
                new Query(Pool::openConnection(
                    new Connection()
                        ->setHost(socket: $_ENV['DATABASE_SOCKET'])
                        ->setUser($_ENV['DATABASE_USER'])
                        ->setPassword($_ENV['DATABASE_PASSWORD'])
                        ->setDB($_ENV['DATABASE_NAME'])
                        ->setOption(\PDO::MYSQL_ATTR_FOUND_ROWS, true)
                        ->setOption(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET SESSION character_set_client = \'utf8mb4\',
                                                                                    SESSION collation_connection = \'utf8mb4_0900_as_cs\',
                                                                                    SESSION character_set_connection = \'utf8mb4\',
                                                                                    SESSION character_set_database = \'utf8mb4\',
                                                                                    SESSION character_set_results = \'utf8mb4\',
                                                                                    SESSION character_set_server = \'utf8mb4\',
                                                                                    SESSION time_zone=\'+00:00\';')
                        ->setOption(\PDO::ATTR_TIMEOUT, 1), max_tries: 5)
                );
                #Check for maintenance
                try {
                    self::$db_update = (bool)Query::query('SELECT `value` FROM `sys__settings` WHERE `setting`=\'maintenance\'', return: 'value');
                } catch (\Throwable $exception) {
                    #The most likely cause of the maintenance check to fail is if the table does not exist. If it does not - consider that we are under maintenance.
                    self::$db_update = true;
                    Errors::error_log($exception);
                }
                Query::query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            } catch (\Throwable $exception) {
                #2002 error code means server is not listening on port
                #2006 error code means server has gone away
                #This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
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