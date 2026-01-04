<?php
declare(strict_types = 1);

use Simbiat\FFXIV\Cron;
use Simbiat\Website\Config;
use Simbiat\Website\Cron\BICTracker;
use Simbiat\Website\Cron\Day;
use Simbiat\Website\Cron\Minute;
use Simbiat\Website\Errors;
use Simbiat\Website\Sitemap\Generate;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#The below script is meant to run daily maintenance tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Day();
$talks = new \Simbiat\Talks\Cron();
$ff = new Cron();
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
        new Generate()->generate();
        Minute::cliOutput('Updating BIC...', true);
        (void)$maintenance->libraryUpdate();
        Minute::cliOutput('Removing dead links...', true);
        $talks->removeDeadLinks();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);