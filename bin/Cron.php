<?php
declare(strict_types = 1);

use Simbiat\Cron\Agent;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';


#Below is a script meant to run CRON tasks from the database (using CRON library)

#Connect to DB
Config::dbConnect();
#Run cron
try {
    if (Config::$dbup && !Config::$dbUpdate) {
        new Agent()->process(50);
    }
} catch (Throwable $e) {
    Errors::error_log($e);
}
exit;