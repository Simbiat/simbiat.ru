<?php
declare(strict_types = 1);

use App\HomePage;

#Bootstrap things
require_once '/app/bin/Bootstrap.php';

$home_page = new HomePage();
exit(0);