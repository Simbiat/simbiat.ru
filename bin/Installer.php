<?php
declare(strict_types = 1);

use Simbiat\Cron\Agent;
use Simbiat\Cron\Installer;
use Simbiat\Cron\Task;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\Website\Config;
use Simbiat\Website\Cron\BICTracker;
use Simbiat\Website\Cron\FFTracker;
use Simbiat\Website\Cron\Maintenance;
use Simbiat\Website\Cron\Talks;
use Simbiat\Website\Sitemap\Generate;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#The below script is to be run once to install the necessary stuff for the service ro work correctly

#Connect to DB
Config::dbConnect();
#Install CRON
if (new Installer()->install()) {
    #Update settings
    $agent = new Agent();
    $agent->setSetting('log_life', 14);
    $agent->setSetting('max_threads', 10);
    #Add tasks
    new Task()->settingsFromArray(['task' => 'argon', 'function' => 'argon', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to recalculate optimal Argon encryption settings'])->add();
    new Task()->settingsFromArray(['task' => 'bic_update', 'function' => 'LibraryUpdate', 'object' => '\\'.BICTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to update BIC library'])->add();
    new Task()->settingsFromArray(['task' => 'clean_avatars', 'function' => 'cleanAvatars', 'object' => '\\'.Talks::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Removing excessive avatars'])->add();
    new Task()->settingsFromArray(['task' => 'clean_uploads', 'function' => 'cleanFiles', 'object' => '\\'.Talks::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Removing unused and orphaned uploaded files'])->add();
    new Task()->settingsFromArray(['task' => 'db_for_backup', 'function' => 'forBackup', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Generate DDLs and recommended dump order for current DB structure'])->add();
    new Task()->settingsFromArray(['task' => 'ff_add_servers', 'function' => 'UpdateServers', 'object' => '\\'.FFTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Adds new servers'])->add();
    new Task()->settingsFromArray(['task' => 'ff_new_characters', 'function' => 'registerNewCharacters', 'object' => '\\'.FFTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Schedule jobs to add potential new characters'])->add();
    new Task()->settingsFromArray(['task' => 'ff_new_linkshells', 'function' => 'registerNewLinkshells', 'object' => '\\'.FFTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Check for potential new linkshells and schedule jobs for them'])->add();
    new Task()->settingsFromArray(['task' => 'ff_update_entity', 'function' => 'UpdateEntity', 'object' => '\\'.FFTracker::class, 'allowed_returns' => '["character", "freecompany", "linkshell", "crossworldlinkshell", "pvpteam", "achievement"]', 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV entities'])->add();
    new Task()->settingsFromArray(['task' => 'ff_update_old', 'function' => 'UpdateOld', 'object' => '\\'.FFTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update oldest FFXIV entities'])->add();
    new Task()->settingsFromArray(['task' => 'ff_update_statistics', 'function' => 'UpdateStatistics', 'object' => '\\'.FFTracker::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV statistics'])->add();
    new Task()->settingsFromArray(['task' => 'files_clean', 'function' => 'filesClean', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Delete old files'])->add();
    new Task()->settingsFromArray(['task' => 'lock_posts', 'function' => 'lockPosts', 'object' => '\\'.Talks::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Locking posts for editing'])->add();
    new Task()->settingsFromArray(['task' => 'logs_clean', 'function' => 'logsClean', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to purge old logs'])->add();
    new Task()->settingsFromArray(['task' => 'sitemap', 'function' => 'generate', 'object' => '\\'.Generate::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to generate text and XML sitemap files'])->add();
    new Task()->settingsFromArray(['task' => 'statistics_clean', 'function' => 'statisticsClean', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Delete old statistical data'])->add();
    new Task()->settingsFromArray(['task' => 'foreign_keys_clean', 'function' => 'cleanForeignKeys', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Remove entries that would violate foreign key restrictions, if they were used.'])->add();
    new Task()->settingsFromArray(['task' => 'empty_threads', 'function' => 'removeEmptyThreads', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Remove empty threads older than 1 day'])->add();
    new Task()->settingsFromArray(['task' => 'dead_links', 'function' => 'removeDeadLinks', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Remove alt links that no longer exist'])->add();
    new Task()->settingsFromArray(['task' => 'clean_notifications', 'function' => 'cleanNotifications', 'object' => '\\'.Maintenance::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Remove old notifications'])->add();
    #Adding task instances
    new TaskInstance()->settingsFromArray(['task' => 'argon', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Recalculating Argon settings', 'next_run' => strtotime('today 2:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'bic_update', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 5, 'message' => 'Updating BIC library', 'next_run' => strtotime('today 23:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'clean_avatars', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Removing excessive avatars', 'next_run' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'clean_uploads', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Cleaning uploaded files', 'next_run' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'db_for_backup', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 9, 'message' => 'Dumping DDLs', 'next_run' => strtotime('today 2:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_add_servers', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 604800, 'day_of_week' => '[3]', 'priority' => 1, 'message' => 'Checking for new servers on Lodestone', 'next_run' => strtotime('today 7:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_new_characters', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 1, 'message' => 'Scheduling potential new characters', 'next_run' => strtotime('today 12:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_new_linkshells', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 1, 'message' => 'Checking for new linkshells', 'next_run' => strtotime('today 9:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 2, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 3, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '"[50, ""$cron_instance""]"', 'instance' => 4, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 5, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 6, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_statistics', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 2, 'message' => 'Updating FFXIV statistics', 'next_run' => strtotime('today 3:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'files_clean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 604800, 'priority' => 0, 'message' => 'Removing old files', 'next_run' => strtotime('today 1:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'lock_posts', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 9, 'message' => 'Locking posts', 'next_run' => strtotime('today 1:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'logs_clean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 9, 'message' => 'Removing old logs', 'next_run' => strtotime('today 5:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'sitemap', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Generating sitemap files', 'next_run' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'statistics_clean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Removing old statistical data', 'next_run' => strtotime('today 2:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'foreign_keys_clean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Cleaning foreign key violations', 'next_run' => strtotime('today 22:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'empty_threads', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Cleaning empty threads', 'next_run' => strtotime('today 20:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'dead_links', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Checking for dead links', 'next_run' => strtotime('today 20:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'clean_notifications', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Removing old notifications', 'next_run' => strtotime('today 14:00')])->add();
}
#Install the Maintainer library. This *SHOULD* be the last operation, so that all tables are added in the initial update.
if (new \Simbiat\Database\Maintainer\Installer()->install()) {
    new Analyzer()->updateTables($_ENV['DATABASE_NAME']);
}
exit(0);