<?php
#Supressing too many properties warning: this is a config class, it's supposed to be like this
/** @noinspection PhpClassHasTooManyDeclaredMembersInspection */
declare(strict_types = 1);

namespace Simbiat\Website;

#Database settings
use Dotenv\Dotenv;
use Simbiat\Database\Controller;
use Simbiat\Database\Pool;

/**
 * Class that holds main settings for the website. Needs to be instantiated early as part of bootstrapping.
 */
final class Config
{
    public static bool $PROD = false;
    public static string $workDir = '';
    public const string adminMail = 'simbiat@outlook.com';
    public const string adminName = 'Dmitry Kustov';
    public const string siteName = 'Simbiat Software';
    #Mail to use to send emails from
    public const string from = 'noreply@simbiat.dev';
    public static string $http_host = 'www.simbiat.dev';
    public static string $baseUrl = 'https://www.simbiat.dev';
    public static string $htmlCache = '';
    public static string $securitySettings = '';
    public static string $sitemap = '';
    #Path where JS files are stored
    public static string $jsDir = '';
    #Path where CSS files are stored
    public static string $cssDir = '';
    #Path where images stored
    public static string $imgDir = '';
    #Path to uploaded files
    public static string $uploaded = '';
    #Path to uploaded images
    public static string $uploadedImg = '';
    #Folder to dump DDLs to
    public static string $DDLDir = '';
    #GeoIP folder
    public static string $geoip = '';
    #Set of general LINKs to be sent both in HTML and in HEADER
    public static array $links = [];
    #FFTracker directories
    public static string $crestsComponents = '';
    public static string $mergedCrestsCache = '';
    public static string $icons = '';
    public static string $statistics = '';
    #List of system user IDs
    public const array userIDs = [
        'Unknown user' => 1,
        'System user' => 2,
        'Deleted user' => 3,
        'Owner' => 4,
    ];
    #List of system group IDs
    public const array groupsIDs = [
        'Bots' => 0,
        'Administrators' => 1,
        'Unverified' => 2,
        'Users' => 3,
        'Deleted' => 4,
        'Banned' => 5,
        'Linked to FF' => 6,
    ];
    public static array $argonSettings = [];
    #Flag indicating whether we are in CLI
    public static bool $CLI = false;
    #Allow access to canonical value of the host
    public static string $canonical = '';
    #Track if DB connection is up
    public static bool $dbup = false;
    #Maintenance flag
    public static bool $dbUpdate = false;
    #Database controller object
    public static ?Controller $dbController = NULL;
    
    public function __construct()
    {
        #Check if we are in CLI
        if (preg_match('/^cli(-server)?$/i', PHP_SAPI) === 1) {
            self::$CLI = true;
        } else {
            self::$CLI = false;
        }
        self::$workDir = '/app';
        $dotenv = Dotenv::createImmutable(self::$workDir, '.env');
        $dotenv->load();
        #Database settings
        $dotenv->required(['DATABASE_USER', 'DATABASE_PASSWORD', 'DATABASE_NAME', 'DATABASE_HOST', 'DATABASE_TLS_CA', 'DATABASE_TLS_KEY', 'DATABASE_TLS_CRT'])->notEmpty();
        $dotenv->required('MARIADB_PORT')->isInteger();
        #Other settings
        $dotenv->required(['WEB_SERVER_TEST', 'SENDGRID_API_KEY', 'ENCRYPTION_PASSPHRASE'])->notEmpty();
        self::$PROD = ($_ENV['WEB_SERVER_TEST'] === 'false');
        self::$http_host = (self::$PROD ? 'www.simbiat.dev' : 'localhost');
        self::$baseUrl = 'https://'.self::$http_host;
        self::$htmlCache = self::$workDir.'/data/cache/html/';
        self::$securitySettings = self::$workDir.'/data/security.json';
        self::$sitemap = self::$workDir.'/data/sitemap/';
        self::$jsDir = self::$workDir.'/public/assets';
        self::$cssDir = self::$workDir.'/public/assets/styles/';
        self::$imgDir = self::$workDir.'/public/assets/images';
        self::$uploaded = self::$workDir.'/data/uploaded';
        self::$uploadedImg = self::$workDir.'/data/uploadedimages';
        self::$DDLDir = self::$workDir.'/build/DDL';
        self::$crestsComponents = self::$imgDir.'/fftracker/crests-components/';
        self::$mergedCrestsCache = self::$workDir.'/data/mergedcrests/';
        self::$icons = self::$imgDir.'/fftracker/icons/';
        self::$statistics = self::$workDir.'/data/ffstatistics/';
        self::$geoip = self::$workDir.'/data/geoip/';
        #Generate Argon settings
        if (empty(self::$argonSettings)) {
            self::$argonSettings = Security::argonCalc();
        }
        #These are required only if we are outside of CLI mode
        if (!self::$CLI) {
            $this->canonical();
            $this->nonApiLinks();
        }
    }
    
    /**
     * Generate canonical link
     * @return void
     */
    private function canonical(): void
    {
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
        self::$canonical = 'https://'.(preg_match('/^[a-z\d\-_~]+\.[a-z\d\-_~]+$/iu', self::$http_host) === 1 ? 'www.' : '').self::$http_host.($_SERVER['SERVER_PORT'] !== '443' ? ':'.$_SERVER['SERVER_PORT'] : '').'/'.self::$canonical;
        #Update list with dynamic values
        self::$links = array_merge(self::$links, [
            ['rel' => 'canonical', 'href' => self::$canonical],
        ]);
    }
    
    /**
     * Add CSS and JS preload links, if not using API
     * @return void
     */
    private function nonApiLinks(): void
    {
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        if ($uri[0] !== 'api') {
            self::$links = array_merge(self::$links, [
                ['rel' => 'stylesheet preload', 'href' => '/assets/styles/'.filemtime(self::$cssDir.'/app.css').'.css', 'as' => 'style'],
                ['rel' => 'preload', 'href' => '/assets/app.'.filemtime(self::$jsDir.'/app.js').'.js', 'as' => 'script'],
            ]);
        }
    }
    
    /**
     * Database connection
     * @return bool
     */
    public static function dbConnect(): bool
    {
        #Check in case we accidentally call this for 2nd time
        if (self::$dbup === false) {
            try {
                Pool::openConnection(
                    new \Simbiat\Database\Config()
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
                self::$dbController = new Controller();
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
}