<?php
declare(strict_types = 1);

use Simbiat\Cron\Agent;
use Simbiat\Cron\Installer;
use Simbiat\Cron\Task;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\FFXIV\Cron;
use Simbiat\Website\Config;

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

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
    new Task()->settingsFromArray(['task' => 'ff_new_linkshells', 'function' => 'registerNewLinkshells', 'object' => '\\'.Cron::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Check for potential new linkshells and schedule jobs for them'])->add();
    new Task()->settingsFromArray(['task' => 'ff_update_entity', 'function' => 'UpdateEntity', 'object' => '\\'.Cron::class, 'allowed_returns' => '["character", "freecompany", "linkshell", "crossworldlinkshell", "pvpteam", "achievement"]', 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV entities'])->add();
    new Task()->settingsFromArray(['task' => 'ff_update_old', 'function' => 'UpdateOld', 'object' => '\\'.Cron::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update oldest FFXIV entities'])->add();
    #Adding task instances
    new TaskInstance()->settingsFromArray(['task' => 'ff_new_linkshells', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 1, 'message' => 'Checking for new linkshells', 'next_run' => strtotime('today 5:00')])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 2, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 3, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '"[50, ""$cron_instance""]"', 'instance' => 4, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 5, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
    new TaskInstance()->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 6, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
}
#Install the Maintainer library. This *SHOULD* be the last operation, so that all tables are added in the initial update.
if (new \Simbiat\Database\Maintainer\Installer()->install()) {
    new Analyzer()->updateTables($_ENV['DATABASE_NAME']);
}
exit(0);