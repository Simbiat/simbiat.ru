<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is meant to run some regular server status checks, as well as remove sessions and cookies.
#Both things need to be run every minute regardless of whether CRON is up.

#Connect to DB
Config::dbConnect();
$maintenance = new Maintenance();
#Check if DB is down
$maintenance->dbDown();
#Check space availability
$maintenance->noSpace();
#Check for error log
$maintenance->errorLog();
#Run cron
try {
    if (Config::$dbup) {
        #Clean sessions
        $maintenance->sessionClean();
        #Clean cookies
        $maintenance->cookiesClean();
    }
} catch (Throwable $e) {
    Errors::error_log($e);
}
exit(0);