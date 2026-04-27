<?php
declare(strict_types = 1);

use App\Service\Config;
use App\Service\Errors;
use Simbiat\Cron\Agent;

#Bootstrap things
require_once '/app/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#Below is a script meant to run CRON tasks from the database (using CRON library)

#Connect to DB
Config::dbConnect();
#Run cron
try {
    if (Config::$dbup && !Config::$db_update) {
        new Agent()->process(50);
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);