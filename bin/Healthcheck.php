<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance\HealthCheck;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is meant to run some regular server status checks

#Connect to DB
Config::dbConnect();
$maintenance = new HealthCheck();
#Run cron
try {
    #Check if DB is down
    $maintenance->dbDown();
    #Check space availability
    $maintenance->noSpace();
    #Check for error log
    $maintenance->errorLog();
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);