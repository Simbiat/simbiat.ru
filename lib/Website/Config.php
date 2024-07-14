<?php
declare(strict_types = 1);

namespace Simbiat\Website;

#Database settings
use Dotenv\Dotenv;
use Simbiat\Website\Security;

#Common settings
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

    public function __construct()
    {
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
    }
}