<?php
declare(strict_types = 1);

use App\Service\Config;
use App\Service\Cron\FFXIV;
use App\Service\Cron\HealthCheck;
use App\Service\Cron\Minute;
use App\Service\Cron\Month;
use App\Service\Errors;

#Bootstrap things
require_once '/app/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run some monthly tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Month();
#Run cron
try {
    if (Config::$dbup) {
        Minute::cliOutput('Updating argon settings...');
        $maintenance->argon();
        Minute::cliOutput('Cleaning files...');
        new HealthCheck()->filesClean();
        Minute::cliOutput('Cleaning logs...');
        $maintenance->logsClean();
        Minute::cliOutput('Cleaning statistics...');
        $maintenance->statisticsClean();
        Minute::cliOutput('Updating FF servers...');
        new FFXIV()->updateServers();
        Minute::cliOutput('Cleaning foreign keys...');
        $maintenance->cleanForeignKeys();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);