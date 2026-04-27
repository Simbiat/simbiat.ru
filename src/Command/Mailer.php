<?php
declare(strict_types = 1);

use App\Service\Config;
use App\Service\Cron\Minute;
use App\Service\Errors;

#Bootstrap things
require_once '/app/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

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