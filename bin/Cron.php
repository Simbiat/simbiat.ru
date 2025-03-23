<?php
declare(strict_types = 1);

use Simbiat\Cron\Agent;
use Simbiat\Website\Config;
use Simbiat\Website\Cron\Maintenance;
use Simbiat\Website\Errors;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#Process Cron
Config::dbConnect();
$healthCheck = new Maintenance();
#Check if DB is down
$healthCheck->dbDown();
#Check space availability
$healthCheck->noSpace();
#Run cron
try {
    (new Agent())->process(50);
} catch (Throwable $e) {
    Errors::error_log($e);
}
exit;