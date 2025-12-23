<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance\Minute;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is meant to run some every-minute tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Minute();
#Run cron
try {
    if (Config::$dbup) {
        #Send messages
        (void)$maintenance->sendNotifications();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);