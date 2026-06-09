<?php
declare(strict_types = 1);

#TODO: consider this to be absorbed into the Symfony kernel bootstrap
use App\Service\Config;

#Load composer libraries
require_once dirname(__DIR__).'/vendor/autoload.php';

#Generate basic settings
new Config();

#Set error handling
set_error_handler('\App\Service\Errors::error_handler');
set_exception_handler('\App\Service\Errors::error_log');
register_shutdown_function('\App\Service\Errors::shutdown');