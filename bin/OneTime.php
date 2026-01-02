<?php
declare(strict_types = 1);

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#Below is meant to be run only once, as part of some upgrade to the system.
#It is encouraged to code stuff in a way to avoid potential impact in case of running it twice.
#It is also encouraged to use `echo` or similar function(s) to output stuff to the command line for the sake of monitoring.
#Tp run in use `/usr/local/bin/php -f /app/bin/OneTime.php` on the respective container

use Simbiat\Website\Config;
use Simbiat\Website\Errors;

Config::dbConnect();
try {
    echo '['.date('c').'] Getting characters...'.PHP_EOL;
    echo '['.date('c').'] Completed'.PHP_EOL;
} catch (Throwable $exception) {
    #2002 error code means server is not listening on port
    #2006 error code means server has gone away
    #This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
    if (preg_match('/HY000.*\[(2002|2006)]/u', $exception->getMessage()) !== 1) {
        Errors::error_log($exception);
    }
    return false;
}
exit(0);