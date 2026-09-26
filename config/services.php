<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Command\DotenvDumpCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('.container.dumper.inline_factories', value: true);

    $parameters->set('app.admin_email', '%env(ADMIN_EMAIL)%');

    $parameters->set('app.admin_name', 'Dmitrii Kustov');

    $parameters->set('app.site_name', 'Simbiat Software');

    $parameters->set('app.from_email', '%env(PROTON_USER)%');

    $parameters->set('app.database_name', '%env(DATABASE_NAME)%');

    $parameters->set('app.mailer_dsn', '%env(MAILER_DSN)%');

    $parameters->set('app.encryption_passphrase', '%env(ENCRYPTION_PASSPHRASE)%');

    $parameters->set('app.security_settings', '%kernel.project_dir%/data/security.json');

    $parameters->set('app.base_url', 'https://%app.http_host%');

    $parameters->set('app.support_section', 26);

    $parameters->set('app.group_ids', [
        'Administrators' => 1,
        'Unverified' => 2,
        'Users' => 3,
        'Deleted' => 4,
        'Banned' => 5,
        'Linked to FF' => 6,
        'Bots' => 7,
    ]);

    $parameters->set('app.cookie_settings', [
        'path' => '/',
        'domain' => '%app.domain%',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
        'partitioned' => true,
    ]);

    $parameters->set('app.argon_settings', [
        'threads' => 1,
        'memory_cost' => 47_104,
        'time_cost' => 5,
    ]);

    $parameters->set('app.tracking_query_parameters', [
        '__hsfp',
        '__hssc',
        '__hstc',
        '__s',
        '_hsenc',
        '_openstat',
        '_reqid',
        '_trkparms',
        'ad_bucket',
        'ad_size',
        'ad_slot',
        'ad_type',
        'adid',
        'adserverid',
        'adserveroptimizerid',
        'adtype',
        'adurl',
        'aff_id',
        'affiliate',
        'AffiliateGuid',
        'aid',
        'bid',
        'bdref',
        'bstk',
        'campaign_id',
        'campaignid',
        'cid',
        'clickid',
        'client_id',
        'clkurlenc',
        'data',
        'dclid',
        'documentref',
        'exitPop',
        'fb',
        'fb_source',
        'fb_ref',
        'fbclid',
        'first_visit',
        'flash',
        'ga_campaign',
        'ga_content',
        'ga_fc',
        'ga_hid',
        'ga_medium',
        'ga_place',
        'ga_sid',
        'ga_source',
        'ga_term',
        'ga_vid',
        'gclid',
        'hsCtaTracking',
        'ImpressionGuid',
        'matchid',
        'mc_eid',
        'mediadataid',
        'minbid',
        'mkt_tok',
        'ml_subscriber',
        'ml_subscriber_hash',
        'msclkid',
        'num_ads',
        'oly_anon_id',
        'oly_enc_id',
        'origin',
        'page_referrer',
        'payload',
        'pid',
        'piggiebackcookie',
        'pk_campaign',
        'providerid',
        'pubclick',
        'pubid',
        'rb_clickid',
        'rcm',
        'ref',
        'ref_',
        'referrer',
        'reftype',
        'rev',
        'revmod',
        'rid',
        'rurl',
        's_cid',
        'sid',
        'site',
        'siteid',
        'sourceid',
        'src',
        'tldid',
        'trackid',
        'tracking',
        'uid',
        'usegapi',
        'utm_campaign',
        'utm_cid',
        'utm_content',
        'utm_medium',
        'utm_name',
        'utm_reader',
        'utm_source',
        'utm_term',
        'vero_conv',
        'vero_id',
        'wickedid',
        'yclid',
        'zoneid',
    ]);

    $parameters->set('app.directories.sitemap', '%kernel.project_dir%/var/sitemap/');

    $parameters->set('app.directories.js', '%kernel.project_dir%/public/assets/');

    $parameters->set('app.directories.css', '%kernel.project_dir%/public/assets/styles/');

    $parameters->set('app.directories.images', '%kernel.project_dir%/public/assets/images/');

    $parameters->set('app.directories.uploaded', '%kernel.project_dir%/data/uploaded/');

    $parameters->set('app.directories.uploaded_images', '%kernel.project_dir%/data/uploadedimages/');

    $parameters->set('app.directories.ddl', '%kernel.project_dir%/build/DDL/');

    $parameters->set('app.directories.html_cache', '%kernel.project_dir%/data/temp/html/');

    $parameters->set('app.directories.geoip', '/geoip/');

    $parameters->set('app.directories.ffxiv.crests.components', '%kernel.project_dir%/public/assets/images/fftracker/crests-components/');

    $parameters->set('app.directories.ffxiv.crests.cache', '%kernel.project_dir%/var/mergedcrests/');

    $parameters->set('app.directories.ffxiv.icons', '%kernel.project_dir%/public/assets/images/fftracker/icons/');

    $parameters->set('app.directories.ffxiv.statistics', '%kernel.project_dir%/data/ffstatistics/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', '/app/src/');

    $services->set(DotenvDumpCommand::class);
};
