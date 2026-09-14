<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Errors;
use App\Service\Config;
use App\Service\Cron\FFXIV;
use Simbiat\Cron\Agent;
use Simbiat\Cron\Installer;
use Simbiat\Cron\Task;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Maintainer\Analyzer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run once to install the necessary stuff for the service to work correctly
 */
final class Install
{
    /**
     * Run once to install the necessary stuff for the service to work correctly
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:install', description: 'Install the app')]
    public function install(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Installing the app...'));
        try {

            #TODO: Need to figure out a way to prevent run for the 2nd time
            return Command::SUCCESS;

            // Connect to DB
            Config::dbConnect();
            // Install CRON
            if (new Installer(Config::$PDO)->install()) {
                // Update settings
                $agent = new Agent(Config::$PDO);
                $agent->setSetting('log_life', 14);
                $agent->setSetting('max_threads', 6);
                // Add tasks
                new Task(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_new_linkshells', 'function' => 'registerNewLinkshells', 'object' => '\\'.FFXIV::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Check for potential new linkshells and schedule jobs for them'])->add();
                new Task(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_entity', 'function' => 'UpdateEntity', 'object' => '\\'.FFXIV::class, 'allowed_returns' => '["character", "freecompany", "linkshell", "crossworldlinkshell", "pvpteam", "achievement"]', 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update FFXIV entities'])->add();
                new Task(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'function' => 'UpdateOld', 'object' => '\\'.FFXIV::class, 'max_time' => 3600, 'min_frequency' => 60, 'retry' => 0, 'enabled' => 1, 'system' => 1, 'description' => 'Update oldest FFXIV entities'])->add();
                // Adding task instances
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_new_linkshells', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 3600, 'priority' => 1, 'message' => 'Checking for new linkshells', 'next_run' => strtotime('today 5:00')])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 1, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 2, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 3, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '"[50, ""$cron_instance""]"', 'instance' => 4, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 5, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
                new TaskInstance(dbh: Config::$PDO)->settingsFromArray(['task' => 'ff_update_old', 'arguments' => '[50, "$cron_instance"]', 'instance' => 6, 'enabled' => 1, 'system' => 1, 'frequency' => 60, 'priority' => 0, 'message' => 'Updating old FFXIV entities', 'next_run' => time()])->add();
            }
            // Install the Maintainer library. This *SHOULD* be the last operation, so that all tables are added in the initial update.
            if (new \Simbiat\Database\Maintainer\Installer(Config::$PDO)->install()) {
                new Analyzer(Config::$PDO)->updateTables(Config::$database_name);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
