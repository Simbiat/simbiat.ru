<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron\Maintenance;

use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\Database\Maintainer\Settings;
use Simbiat\Database\Manage;
use Simbiat\Database\Query;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;

/**
 * Various maintenance tasks menat to be run every day
 */
class Day
{
    /**
     * Remove old notifications
     * @return bool
     */
    public function cleanNotifications(): bool
    {
        return Query::query('DELETE FROM `sys__notifications` WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 YEAR);');
    }
    
    /**
     * Create a list of ordered tables for backup generation
     * @return void
     */
    public function forBackup(): void
    {
        if (!\is_dir(Config::$ddl_dir) && !\mkdir(Config::$ddl_dir, recursive: true) && !\is_dir(Config::$ddl_dir)) {
            Errors::error_log(new \RuntimeException('Failed to create DDL directory'));
        }
        $dump_order = '';
        try {
            #Clean up SQL files
            \array_map('\unlink', \glob(Config::$ddl_dir.'/*.sql'));
            #Get tables in order
            foreach (Manage::showOrderedTables($_ENV['DATABASE_NAME']) as $order => $table) {
                #Get DDL statement
                $create = Manage::showCreateTable($table['schema'], $table['table'], if_not_exist: true, add_use: true);
                if ($create === null) {
                    throw new \UnexpectedValueException('Failed to get CREATE statement for table `'.$table['table'].'`;');
                }
                #Get DDL statement
                if (\preg_match('/^(cron|maintainer)__/ui', $table['table']) !== 1) {
                    \file_put_contents(Config::$ddl_dir.'/'.mb_str_pad((string)($order + 1), 3, '0', \STR_PAD_LEFT, 'UTF-8').'-'.$table['table'].'.sql', mb_trim($create, null, 'UTF-8'));
                }
                #Add item to the file with dump order
                $dump_order .= $table['table'].' ';
            }
            \file_put_contents(Config::$ddl_dir.'/000-recommended_table_order.txt', $dump_order);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return;
        }
    }
    
    /**
     * Generates commands for optimizing tables
     *
     * @return void
     */
    public function dbOptimize(): void
    {
        $analyzer = new Analyzer();
        $settings = new Settings();
        #Ensure we have all tables, even though we end up doing this twice
        $analyzer->updateTables($_ENV['DATABASE_NAME']);
        #Ensure settings are set to what we want
        $settings->setTableFineTune($_ENV['DATABASE_NAME'], [], 'analyze_histogram', true)
            ->setTableFineTune($_ENV['DATABASE_NAME'], [], 'analyze_histogram_auto', true)
            ->setThresholdFragmentation($_ENV['DATABASE_NAME'], [], 5.0)
            ->setRun($_ENV['DATABASE_NAME'], [], 'check', true)
            ->setRun($_ENV['DATABASE_NAME'], [], 'fulltext_rebuild', true)
            ->setGlobalFineTune('prefer_compressed', true)
            ->setGlobalFineTune('prefer_extended', true)
            ->setGlobalFineTune('compress_auto_run', true)
            ->setGlobalFineTune('use_flush', true);
        $commands = $analyzer->getCommands($_ENV['DATABASE_NAME'], [], true, true);
        foreach ($commands as $key => $command) {
            if (\preg_match('/^UPDATE.*`sys__settings` SET/ui', $command) === 1) {
                unset($commands[$key]);
            }
        }
        #Dump commands to file
        \file_put_contents(Config::$work_dir.'/data/backups/optimization_commands.sql', \implode(\PHP_EOL, $commands));
    }
}
