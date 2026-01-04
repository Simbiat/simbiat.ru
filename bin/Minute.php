<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\Minute;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run some every-minute tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Minute();
#Run cron
try {
    if (Config::$dbup) {
        Minute::cliOutput('Cleaning sessions...');
        $maintenance->sessionClean();
        Minute::cliOutput('Cleaning cookies...');
        $maintenance->cookiesClean();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);