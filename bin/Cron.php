<?php
declare(strict_types = 1);

use Simbiat\Cron\Agent;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
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