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
    $agent->setSetting('logLife', 14);
    $agent->setSetting('maxThreads', 10);
    #Add tasks
    new Task()->settingsFromArray(['task' => 'argon', 'function' => 'argon', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to recalculate optimal Argon encryption settings'])->add();
    new Task()->settingsFromArray(['task' => 'bicUpdate', 'function' => 'LibraryUpdate', 'object' => '\\'.BICTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to update BIC library'])->add();
    new Task()->settingsFromArray(['task' => 'cleanAvatars', 'function' => 'cleanAvatars', 'object' => '\\'.Talks::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Removing excessive avatars'])->add();
    new Task()->settingsFromArray(['task' => 'cleanUploads', 'function' => 'cleanFiles', 'object' => '\\'.Talks::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Removing unused and orphaned uploaded files'])->add();
    new Task()->settingsFromArray(['task' => 'dbForBackup', 'function' => 'forBackup', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Generate DDLs and recommended dump order for current DB structure'])->add();
    new Task()->settingsFromArray(['task' => 'dbMaintenance', 'function' => 'dbOptimize', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to optimize tables'])->add();
    new Task()->settingsFromArray(['task' => 'ffAddServers', 'function' => 'UpdateServers', 'object' => '\\'.FFTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Adds new servers'])->add();
    new Task()->settingsFromArray(['task' => 'ffNewCharacters', 'function' => 'registerNewCharacters', 'object' => '\\'.FFTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Schedule jobs to add potential new characters'])->add();
    new Task()->settingsFromArray(['task' => 'ffNewLinkshells', 'function' => 'registerNewLinkshells', 'object' => '\\'.FFTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Check for potential new linkshells and schedule jobs for them'])->add();
    new Task()->settingsFromArray(['task' => 'ffUpdateEntity', 'function' => 'UpdateEntity', 'object' => '\\'.FFTracker::class, 'allowedreturns' => '["character", "freecompany", "linkshell", "crossworldlinkshell", "pvpteam", "achievement"]', 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV entities'])->add();
    new Task()->settingsFromArray(['task' => 'ffUpdateOld', 'function' => 'UpdateOld', 'object' => '\\'.FFTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update oldest FFXIV entities'])->add();
    new Task()->settingsFromArray(['task' => 'ffUpdateStatistics', 'function' => 'UpdateStatistics', 'object' => '\\'.FFTracker::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV statistics'])->add();
    new Task()->settingsFromArray(['task' => 'filesClean', 'function' => 'filesClean', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Delete old files'])->add();
    new Task()->settingsFromArray(['task' => 'lockPosts', 'function' => 'lockPosts', 'object' => '\\'.Talks::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Locking posts for editing'])->add();
    new Task()->settingsFromArray(['task' => 'logsClean', 'function' => 'logsClean', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to purge old logs'])->add();
    new Task()->settingsFromArray(['task' => 'sitemap', 'function' => 'generate', 'object' => '\\'.Generate::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Job to generate text and XML sitemap files'])->add();
    new Task()->settingsFromArray(['task' => 'statisticsClean', 'function' => 'statisticsClean', 'object' => '\\'.Maintenance::class, 'maxTime' => 3600, 'minFrequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Delete old statistical data'])->add();
    #Adding task instances
    new TaskInstance()->settingsFromArray(['task' => 'argon', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Recalculating Argon settings', 'nextrun' => strtotime('today 2:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'bicUpdate', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 5, 'message' => 'Updating BIC library', 'nextrun' => strtotime('today 23:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'cleanAvatars', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Removing excessive avatars', 'nextrun' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'cleanUploads', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Cleaning uploaded files', 'nextrun' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'dbForBackup', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 9, 'message' => 'Dumping DDLs', 'nextrun' => strtotime('today 2:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'dbMaintenance', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Running database maintenance', 'nextrun' => strtotime('today 5:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffAddServers', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 604800, 'dayofweek' => '[3]', 'priority' => 1, 'message' => 'Checking for new servers on Lodestone', 'nextrun' => strtotime('today 7:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffNewCharacters', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 1, 'message' => 'Scheduling potential new characters', 'nextrun' => strtotime('today 12:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffNewLinkshells', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 1, 'message' => 'Checking for new linkshells', 'nextrun' => strtotime('today 9:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '[50, "$cronInstance"]', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '[50, "$cronInstance"]', 'instance' => 2, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '[50, "$cronInstance"]', 'instance' => 3, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '"[50, ""$cronInstance""]"', 'instance' => 4, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '[50, "$cronInstance"]', 'instance' => 5, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateOld', 'arguments' => '[50, "$cronInstance"]', 'instance' => 6, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'nextrun' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ffUpdateStatistics', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 2, 'message' => 'Updating FFXIV statistics', 'nextrun' => strtotime('today 3:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'filesClean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 604800, 'priority' => 0, 'message' => 'Removing old files', 'nextrun' => strtotime('today 1:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'lockPosts', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 9, 'message' => 'Locking posts', 'nextrun' => strtotime('today 1:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'logsClean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 9, 'message' => 'Removing old logs', 'nextrun' => strtotime('today 5:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'sitemap', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 86400, 'priority' => 0, 'message' => 'Generating sitemap files', 'nextrun' => strtotime('today 0:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'statisticsClean', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 2592000, 'priority' => 0, 'message' => 'Removing old statistical data', 'nextrun' => strtotime('today 2:00')])->add();
}
#Install the Maintainer library. This *SHOULD* be the last operation, so that all tables are added in the initial update.
if (new \Simbiat\Database\Maintainer\Installer()->install()) {
    new Analyzer()->updateTables($_ENV['DATABASE_NAME']);
}
exit;