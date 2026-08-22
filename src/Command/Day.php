<?php
declare(strict_types = 1);

use App\Service\Config;
use App\Service\Cron\Day;
use App\Service\Cron\FFXIV;
use App\Service\Cron\HealthCheck;
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
    Minute::cliOutput('Cleaning files...');
    new HealthCheck()->filesClean();
    if (Config::$dbup) {
        Minute::cliOutput('Cleaning avatars...');
        $talks->cleanAvatars();
        Minute::cliOutput('Cleaning unused uploaded files...');
        $talks->cleanFiles();
        Minute::cliOutput('Cleaning notifications...');
        $maintenance->cleanNotifications();
        Minute::cliOutput('Locking posts...');
        $talks->lockPosts();
        Minute::cliOutput('Closing tickets...');
        $talks->closeInactiveTickets();
        Minute::cliOutput('Removing empty threads...');
        $talks->removeEmptyThreads();
        Minute::cliOutput('Registering new FF characters...');
        $ff->registerNewCharacters();
        Minute::cliOutput('Updating FF statistics...');
        $ff->updateStatistics();
        Minute::cliOutput('Generating ordered tables list...');
        $maintenance->forBackup();
        Minute::cliOutput('Generating script for DB optimization...');
        $maintenance->dbOptimize();
        Minute::cliOutput('Generating sitemap...');
        new Sitemap()->generate();
        Minute::cliOutput('Updating BIC...');
        (void)$maintenance->libraryUpdate();
        Minute::cliOutput('Removing dead links...');
        $talks->removeDeadLinks();
        Minute::cliOutput('Cleaning logs...');
        $maintenance->logsClean();
        Minute::cliOutput('Cleaning statistics...');
        $maintenance->statisticsClean();
        Minute::cliOutput('Updating FF servers...');
        new FFXIV()->updateServers();
        Minute::cliOutput('Cleaning foreign keys...');
        $maintenance->cleanForeignKeys();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);