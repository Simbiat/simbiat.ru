<?php
#Suppressing unhandled exceptions, since they are meant to be handled inside respective functions
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

use Simbiat\Website\HomePage;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

$HomePage = new HomePage();
exit;