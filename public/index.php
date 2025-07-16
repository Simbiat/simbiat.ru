<?php
declare(strict_types = 1);

use Simbiat\Website\HomePage;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

$home_page = new HomePage();
exit(0);