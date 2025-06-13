<?php
#Most of the functions are meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use Simbiat\CuteBytes;
use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\Database\Maintainer\Settings;
use Simbiat\Database\Manage;
use Simbiat\Database\Optimize;
use Simbiat\Database\Pool;
use Simbiat\Database\Query;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\Email;
use Simbiat\Website\usercontrol\Session;
use function dirname;

/**
 * Various functions for service maintenance
 */
class Maintenance
{
    /**
     * Files clean
     * @return bool
     */
    public function filesClean(): bool
    {
        #Clean HTML cache
        $this->recursiveClean(Config::$html_cache, 1440, 2048);
        #Clean merged crests cache
        $this->recursiveClean(Config::$merged_crests_cache, 14400);
        #Clean temp directory
        $this->recursiveClean(sys_get_temp_dir(), 4320, 0);
        return true;
    }
    
    /**
     * Clean session data
     * @return bool
     */
    public function sessionClean(): bool
    {
        try {
            return (bool)new Session()->gc();
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
    
    /**
     * Clean cookies data
     * @return bool
     */
    public function cookiesClean(): bool
    {
        #Get existing cookies that need to be cleaned
        try {
            $items = Query::query(
                'SELECT `cookie_id`, `user_id`, `ip`, `user_agent`, `time` FROM `uc__cookies` WHERE `user_id` IN (SELECT `user_id` FROM `uc__users` WHERE `system`=1) OR `time`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 MONTH);', return: 'all'
            );
        } catch (\Throwable) {
            $items = [];
        }
        foreach ($items as $item) {
            #Try to delete cookie
            $affected = Query::query('DELETE FROM `uc__cookies`WHERE `cookie_id`=:id',
                [
                    ':id' => $item['cookie_id'],
                ], return: 'affected'
            );
            #If it was deleted - log it
            if ($affected > 0) {
                Security::log('Logout', 'Logged out due to cookie timeout', $item, $item['user_id']);
            }
        }
        return true;
    }
    
    /**
     * Clean logs
     * @return bool
     */
    public function logsClean(): bool
    {
        $queries = [];
        #Clean audit logs
        $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 YEAR)';
        try {
            $result = Query::query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            $result = false;
        }
        return $result;
    }
    
    /**
     * Clean statistical data
     * @return bool|string
     */
    public function statisticsClean(): bool|string
    {
        $queries = [];
        #Remove pages that have not been viewed in 2 years
        $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 YEAR)';
        #Remove visitors who have not come in 2 years
        $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 YEAR)';
        try {
            $result = Query::query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            $result = false;
        }
        return $result;
    }
    
    /**
     * Force regeneration of Argon settings
     * @return bool
     */
    public function argon(): bool
    {
        return !empty(Security::argonCalc(true));
    }
    
    /**
     * Create a list of ordered tables for backup generation
     * @return bool|string
     */
    public function forBackup(): bool|string
    {
        if (!is_dir(Config::$ddl_dir) && !mkdir(Config::$ddl_dir, recursive: true) && !is_dir(Config::$ddl_dir)) {
            return 'Failed to create DDL directory';
        }
        $dumpOrder = '';
        try {
            #Clean up SQL files
            array_map('unlink', glob(Config::$ddl_dir.'/*.sql'));
            #Get tables in order
            foreach (Manage::showOrderedTables($_ENV['DATABASE_NAME']) as $order => $table) {
                #Get DDL statement
                $create = Manage::showCreateTable($table['schema'], $table['table'], ifNotExist: true, addUse: true);
                if ($create === null) {
                    throw new \UnexpectedValueException('Failed to get CREATE statement for table `'.$table['table'].'`;');
                }
                #Get DDL statement
                if (preg_match('/^(cron|maintainer)__/ui', $table['table']) !== 1) {
                    file_put_contents(Config::$ddl_dir.'/'.mb_str_pad((string)($order + 1), 3, '0', STR_PAD_LEFT, 'UTF-8').'-'.$table['table'].'.sql', mb_trim($create, null, 'UTF-8'));
                }
                #Add item to the file with dump order
                $dumpOrder .= $table['table'].' ';
            }
            file_put_contents(Config::$ddl_dir.'/000-recommended_table_order.txt', $dumpOrder);
            $this->dbOptimize();
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return $e->getMessage();
        }
        return true;
    }
    
    /**
     * Generates commands for optimizing tables
     * @return bool
     */
    public function dbOptimize(): bool
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
            if (preg_match('/^UPDATE.*`sys__settings` SET/ui', $command) === 1) {
                unset($commands[$key]);
            }
        }
        #Dump commands to file
        file_put_contents(Config::$work_dir.'/data/backups/optimization_commands.sql', implode(PHP_EOL, $commands));
        return true;
    }
    
    /**
     * Function to get available disk space
     * @return void
     */
    public function noSpace(): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        #Get free space in percentage
        $free = disk_free_space($dir);
        $total = disk_total_space($dir);
        $percentage = $free * 100 / $total;
        if (Config::$prod && $percentage < 5) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noSpace.flag')) {
                #Clean files
                $this->filesClean();
                #Recalculate percentage
                $percentage = $free * 100 / $total;
                if ($percentage < 5) {
                    #Send mail
                    new Email(Config::ADMIN_MAIL)->send('[Alert]: Low space', ['percentage' => $percentage, 'free' => CuteBytes::bytes($free, 1024), 'total' => CuteBytes::bytes($total, 1024)], 'Simbiat');
                    #Generate flag
                    file_put_contents($dir.'/noSpace.flag', $percentage.'% of space left');
                }
            }
        } elseif (is_file($dir.'/noSpace.flag')) {
            @unlink($dir.'/noSpace.flag');
            #Send mail
            new Email(Config::ADMIN_MAIL)->send('[Resolved]: Low space', ['percentage' => $percentage, 'free' => CuteBytes::bytes($free, 1024), 'total' => CuteBytes::bytes($total, 1024)], 'Simbiat');
        }
    }
    
    /**
     * Function to send alert database going down
     * @return void
     */
    public function dbDown(): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        if (Config::$prod && !Config::$dbup) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noDB.flag')) {
                #Send mail
                new Email(Config::ADMIN_MAIL)->send('[Alert]: Database is down', ['errors' => print_r(Pool::$errors, true)], 'Simbiat');
                #Generate flag
                file_put_contents($dir.'/noDB.flag', 'Database is down');
            }
        } elseif (is_file(Config::$work_dir.'/data/backups/crash.flag')) {
            $error_text = file_get_contents(Config::$work_dir.'/data/backups/crash.flag');
            try {
                $result = Query::query('UPDATE `sys__settings` SET `value` = 0 WHERE `setting` = \'maintenance\';');
            } catch (\Throwable) {
                $result = false;
            }
            @unlink(Config::$work_dir.'/data/backups/crash.flag');
            @unlink($dir.'/noDB.flag');
            #Send mail
            new Email(Config::ADMIN_MAIL)->send('[Resolved]: Database is down', ['maintenance' => true, 'restored' => $result, 'error' => $error_text], 'Simbiat');
        } elseif (is_file($dir.'/noDB.flag')) {
            @unlink($dir.'/noDB.flag');
            #Send mail
            new Email(Config::ADMIN_MAIL)->send('[Resolved]: Database is down', username: 'Simbiat');
        }
    }
    
    /**
     * Clean temp folder
     * @param string $path    Path to clean
     * @param int    $maxAge  Oldest age of a file in minutes
     * @param int    $maxSize Maximum size to remove
     *
     * @return void
     */
    private function recursiveClean(string $path, int $maxAge = 60, int $maxSize = 1024): void
    {
        #Get current maximum execution time
        $curMaxTime = (int)\ini_get('max_execution_time');
        #Iterration can take a long time, so let it run its course
        set_time_limit(0);
        #Restore execution time
        set_time_limit($curMaxTime);
        #Sanitize values
        if ($maxAge < 0) {
            #Reset to default 1 hour cache
            $maxAge = 60 * 60;
        } else {
            #Otherwise, convert into minutes (seconds do not make sense here at all)
            $maxAge *= 60;
        }
        if ($maxSize < 0) {
            #Consider that the size limit was removed
            $maxSize = 0;
        } else {
            #Otherwise, convert to megabytes (lower than 1 MB does not make sense)
            $maxSize *= 1024 * 1024;
        }
        #Set a list of empty folders (removing within iteration seems to cause a fatal error)
        $emptyDirs = [];
        if ($maxAge > 0) {
            #Get the oldest allowed time
            $oldest = time() - $maxAge;
            #Garbage collector for old files, if the file pool is used
            $sizeToRemove = 0;
            #Initiate iterator
            $fileSI = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            #List of files to remove
            $toDelete = [];
            #List of fresh files with their sizes
            $fresh = [];
            #Iterate the files to get size and date first
            #Using catch to handle potential race condition, when a file gets removed by a different process before the check gets called
            try {
                foreach ($fileSI as $file) {
                    if (is_dir($file)) {
                        #Check if empty
                        if (!new \RecursiveDirectoryIterator($file, \FilesystemIterator::SKIP_DOTS)->valid()) {
                            #Remove directory
                            $emptyDirs[] = $file;
                        }
                    } elseif (is_file($file)) {
                        #If we have age restriction, check if the age
                        $time = filemtime($file);
                        if ($maxSize > 0) {
                            $size = filesize($file);
                        } else {
                            $size = 0;
                        }
                        if ($maxAge > 0 && \is_int($time) && $time <= $oldest) {
                            #Add to a list of files to delete
                            $toDelete[] = $file;
                            if ($maxSize > 0) {
                                $sizeToRemove += $size;
                            }
                        } elseif ($maxSize > 0) {
                            $fresh[] = ['path' => $file, 'time' => $time, 'size' => $size];
                        }
                    }
                }
                #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
            } catch (\Throwable) {
                #Do nothing
            }
            #If we have size limitation and a list of fresh items is not empty
            if ($maxSize > 0 && !empty($fresh)) {
                #Calclate total size
                $totalSize = array_sum(array_column($fresh, 'size')) + $sizeToRemove;
                #Check if we are already removing enough. If so - skip further checks
                if ($totalSize - $sizeToRemove >= $maxSize) {
                    #Sort files by time from oldest to newest
                    usort($fresh, static function ($a, $b) {
                        return $a['time'] <=> $b['time'];
                    });
                    #Iterrate list
                    foreach ($fresh as $file) {
                        $toDelete[] = $file['path'];
                        $sizeToRemove += $file['size'];
                        #Check if removing this file will be enough and break the cycle if it is
                        if ($totalSize - $sizeToRemove < $maxSize) {
                            break;
                        }
                    }
                }
            }
            foreach ($toDelete as $file) {
                #Using catch to handle potential race condition, when a file gets removed by a different process before the check gets called
                try {
                    #Check if the file is old enough
                    if (is_file($file)) {
                        #Remove the file
                        /** @noinspection PhpUsageOfSilenceOperatorInspection */
                        @unlink($file);
                        #Remove the parent directory if empty
                        if (!new \RecursiveDirectoryIterator(dirname($file), \FilesystemIterator::SKIP_DOTS)->valid()) {
                            $emptyDirs[] = $file;
                        }
                    }
                    #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
                } catch (\Throwable) {
                    #Do nothing
                }
            }
        }
        #Garbage collector for empty directories
        foreach ($emptyDirs as $dir) {
            if ($dir !== $path) {
                #Using catch to handle potential race condition, when a directory gets removed by a different process before the check gets called
                try {
                    @rmdir($dir);
                    #Remove the parent directory if empty
                    if (dirname($dir) !== $path && !new \RecursiveDirectoryIterator(dirname($dir), \FilesystemIterator::SKIP_DOTS)->valid()) {
                        @rmdir(dirname($dir));
                    }
                } catch (\Throwable) {
                    #Do nothing
                }
            }
        }
        #Restore execution time
        set_time_limit($curMaxTime);
    }
}
