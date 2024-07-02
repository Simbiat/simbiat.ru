<?php
#Suppressing unhandled exceptions, since they are meant to be handled inside respective functions
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

#Load composer libraries
require_once dirname(__DIR__).'/vendor/autoload.php';

use Simbiat\Website\Config;
use Simbiat\Website\HomePage;

#Generate basic settings
new Config();

#Set error handling
set_error_handler('\Simbiat\Website\Errors::error_handler');
set_exception_handler('\Simbiat\Website\Errors::error_log');
register_shutdown_function('\Simbiat\Website\Errors::shutdown');

$HomePage = new HomePage();
exit;