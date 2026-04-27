<?php
declare(strict_types = 1);

use App\Service\Config;
use App\Service\Cron\Day;
use App\Service\Cron\FFXIV;
use App\Service\Cron\Minute;
use App\Service\Cron\Talks;
use App\Service\Errors;
use App\Service\Sitemap;

#Bootstrap things
require_once '/app/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run daily maintenance tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Day();
$talks = new Talks();
$ff = new FFXIV();
#Run cron
try {
    if (Config::$dbup) {
        Minute::cliOutput('Cleaning avatars...', true);
        $talks->cleanAvatars();
        Minute::cliOutput('Cleaning unused uploaded files...', true);
        $talks->cleanFiles();
        Minute::cliOutput('Cleaning notifications...', true);
        $maintenance->cleanNotifications();
        Minute::cliOutput('Locking posts...', true);
        $talks->lockPosts();
        Minute::cliOutput('Closing tickets...', true);
        $talks->closeInactiveTickets();
        Minute::cliOutput('Removing empty threads...', true);
        $talks->removeEmptyThreads();
        Minute::cliOutput('Registering new FF characters...', true);
        $ff->registerNewCharacters();
        Minute::cliOutput('Updating FF statistics...', true);
        $ff->updateStatistics();
        Minute::cliOutput('Generating ordered tables list...', true);
        $maintenance->forBackup();
        Minute::cliOutput('Generating script for DB optimization...', true);
        $maintenance->dbOptimize();
        Minute::cliOutput('Generating sitemap...', true);
        new Sitemap()->generate();
        Minute::cliOutput('Updating BIC...', true);
        (void)$maintenance->libraryUpdate();
        Minute::cliOutput('Removing dead links...', true);
        $talks->removeDeadLinks();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);