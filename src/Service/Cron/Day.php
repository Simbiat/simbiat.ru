<?php
declare(strict_types = 1);

namespace App\Service\Cron;

use App\Enum\LogType;
use App\Enum\NotificationType;
use App\Enum\SystemUser;
use App\Enum\TalkType;
use App\Notification\CronFailure;
use App\Service\BICLibrary;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\ArrayHelpers\Converters;
use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\Database\Maintainer\Settings;
use Simbiat\Database\Manage;
use Simbiat\Database\Query;

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
        $analyzer->writeCommandsToFiles(Config::$work_dir.'/data/backups/optimization', $_ENV['DATABASE_NAME'], [], true);
    }
    
    /**
     * Update the BIC library
     * @return bool|string|int
     */
    public function libraryUpdate(): bool|string|int
    {
        $result = new BICLibrary()->update(true);
        #Ignore failures to download the file. CBR started using DDoS-Guard, which seems to be blocking the server most of the time now
        if (\is_string($result) && !\is_numeric($result) && !\str_contains($result, 'Не удалось скачать файл')) {
            #Send email notification, this most likely means some change in UFEBS form
            (void)new CronFailure()->save(SystemUser::Owner->value, ['method' => __METHOD__, 'errors' => $result], true, false, Config::ADMIN_MAIL);
        }
        return $result;
    }
    
    /**
     * Clean logs
     * @return bool
     */
    public function logsClean(): bool
    {
        $queries = [];
        #Clean audit logs
        $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 YEAR)';
        try {
            $result = Query::query($queries);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $result = false;
        }
        return $result;
    }
    
    /**
     * Clean statistical data
     * @return void
     */
    public function statisticsClean(): void
    {
        $queries = [];
        #Remove pages that have not been viewed in 2 years
        $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR)';
        #Remove visitors who have not come in 2 years
        $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR)';
        try {
            Query::query($queries);
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
        }
    }
    
    /**
     * Remove entries that would violate foreign key restrictions, if they were used.
     * Service does not use them normally due to performance hit, and to have potentially more flexibility in business logic.
     * While logic should be written in a way to prevent such "violations", having a job to forcefully remove them is useful.
     * Nullable values will be set to NULL.
     * @return bool
     */
    public function cleanForeignKeys(): bool
    {
        #TODO Actually write queries for this
        #Logs
        Query::query('DELETE FROM `sys__logs` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(LogType::class), 'in', 'int']]);
        Query::query('UPDATE `sys__logs` SET `user_id`=:user_id WHERE `user_id` NOT IN (SELECT `user_id` FROM `uc__users`);', [':user_id' => SystemUser::Unknown->value]);
        #Notification types
        Query::query('DELETE FROM `sys__notifications` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(NotificationType::class), 'in', 'int']]);
        #Unsupported section types
        Query::query('DELETE FROM `talks__sections` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(TalkType::class), 'in', 'int']]);
        return true;
    }
}
