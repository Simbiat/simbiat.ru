<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\FFTracker;
use Simbiat\Website\Cron\Maintenance\HealthCheck;
use Simbiat\Website\Cron\Maintenance\Month;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is meant to run some monthly tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Month();
#Run cron
try {
    if (Config::$dbup) {
        #Update argon settings
        $maintenance->argon();
        #Clean some files
        new HealthCheck()->filesClean();
        #Clean logs
        $maintenance->logsClean();
        #Clean statistics
        $maintenance->statisticsClean();
        #Add FF servers, if any
        new FFTracker()->updateServers();
        #Clean foreign keys
        $maintenance->cleanForeignKeys();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);