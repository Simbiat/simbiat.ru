<?php
#Suppressing unhandled exceptions, since they are meant to be handled inside respective functions
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

#Load composer libraries
require __DIR__. '/composer/vendor/autoload.php';

use Simbiat\HomePage;

#Get config file
require_once __DIR__. '/config.php';

#Set error handling
set_error_handler('\Simbiat\Errors::error_handler');
set_exception_handler( '\Simbiat\Errors::error_log');
register_shutdown_function( '\Simbiat\Errors::shutdown');

$HomePage = new HomePage();
exit;
