<?php

declare(strict_types=1);

namespace App\Service;

use Simbiat\Database\Query;
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
    public private(set) static array $tracking_query_parameters = [];
    public private(set) static array $teapot_browsers = [];
    public private(set) static string $mailer_dsn = '';
    public private(set) static string $database_name = '';
    public private(set) static string $encryption_passphrase = '';
    public private(set) static array $argon_settings = [];

    // Track if the DB connection is up
    public private(set) static bool $dbup = false;

    // Maintenance flag
    public private(set) static bool $db_update = false;

    private static ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        self::$container = $container;
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
        self::$tracking_query_parameters = $container->getParameter('app.tracking_query_parameters');
        self::$teapot_browsers = $container->getParameter('app.teapot_browsers');
        self::$argon_settings = $container->getParameter('app.argon_settings');
        self::$mailer_dsn = $container->getParameter('app.mailer_dsn');
        self::$database_name = $container->getParameter('app.database_name');
        self::$encryption_passphrase = $container->getParameter('app.encryption_passphrase');
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
    }

    /**
     * Database connection.
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
                new Query(self::$container->get('doctrine.dbal.default_connection')?->getNativeConnection());
                self::$dbup = true;
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
