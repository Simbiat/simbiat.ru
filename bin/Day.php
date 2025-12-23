<?php
declare(strict_types = 1);

use Simbiat\Website\Config;
use Simbiat\Website\Cron\BICTracker;
use Simbiat\Website\Cron\Maintenance\Day;
use Simbiat\Website\Cron\Talks;
use Simbiat\Website\Errors;
use Simbiat\Website\Cron\FFTracker;
use Simbiat\Website\Sitemap\Generate;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is meant to run daily maintenance tasks

#Connect to DB
Config::dbConnect();
$maintenance = new Day();
$talks = new Talks();
$ff = new FFTracker();
#Run cron
try {
    if (Config::$dbup) {
        #Clean avatars
        $talks->cleanAvatars();
        #Clean files uploaded through Talks
        $talks->cleanFiles();
        #Clean notifications
        $maintenance->cleanNotifications();
        #Lock posts
        $talks->lockPosts();
        #Close tickets
        $talks->closeInactiveTickets();
        #Remove empty threads
        $talks->removeEmptyThreads();
        #Generate tasks for registering new FF characters
        $ff->registerNewCharacters();
        #Update FF statistics
        $ff->updateStatistics();
        #Generate ordered list of tables for backup
        $maintenance->forBackup();
        #Generate script for DB optimization
        $maintenance->dbOptimize();
        #Generate sitemap
        new Generate()->generate();
        #Update BIC
        (void)new BICTracker()->libraryUpdate();
        #Remove dead links
        $talks->removeDeadLinks();
    }
} catch (Throwable $throwable) {
    Errors::error_log($throwable);
}
exit(0);