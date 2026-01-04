<?php
declare(strict_types = 1);

use Simbiat\FFXIV\Cron;
use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance\HealthCheck;
use Simbiat\Website\Cron\Maintenance\Minute;
use Simbiat\Website\Cron\Maintenance\Month;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run some monthly tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Month();
#Run cron
try {
    if (Config::$dbup) {
        Minute::cliOutput('Updating argon settings...', true);
        $maintenance->argon();
        Minute::cliOutput('Cleaning files...', true);
        new HealthCheck()->filesClean();
        Minute::cliOutput('Cleaning logs...', true);
        $maintenance->logsClean();
        Minute::cliOutput('Cleaning statistics...', true);
        $maintenance->statisticsClean();
        Minute::cliOutput('Updating FF servers...', true);
        new Cron()->updateServers();
        Minute::cliOutput('Cleaning foreign keys...', true);
        $maintenance->cleanForeignKeys();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);