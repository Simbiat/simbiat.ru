<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance\HealthCheck;
use Simbiat\Website\Cron\Maintenance\Minute;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run some regular server status checks

#Connect to DB
Config::dbConnect();
$maintenance = new HealthCheck();
#Run cron
try {
    Minute::cliOutput('Checking if DB is up...');
    $maintenance->dbDown();
    Minute::cliOutput('Checking space...');
    $maintenance->noSpace();
    Minute::cliOutput('Checking for error log...');
    $maintenance->errorLog();
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);