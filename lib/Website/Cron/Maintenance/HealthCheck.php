<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron\Maintenance;

use Simbiat\CuteBytes;
use Simbiat\Database\Pool;
use Simbiat\Database\Query;
use Simbiat\Website\Config;
use Simbiat\Website\Entities\Notifications\DatabaseDown;
use Simbiat\Website\Entities\Notifications\DatabaseUp;
use Simbiat\Website\Entities\Notifications\EnoughSpace;
use Simbiat\Website\Entities\Notifications\ErrorLog;
use Simbiat\Website\Entities\Notifications\NoSpace;
use Simbiat\Website\Enums\SystemUsers;

/**
 * Various health check tasks menat to be run every minute
 */
class HealthCheck
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
        $this->recursiveClean(\sys_get_temp_dir(), 4320, 0);
        return true;
    }
    
    /**
     * Function to get available disk space
     * @return void
     */
    public function noSpace(): void
    {
        #Get directory
        $dir = \sys_get_temp_dir();
        if (!\is_dir($dir) && !\mkdir($dir) && !\is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        #Get free space in percentage
        $free = \disk_free_space($dir);
        $total = \disk_total_space($dir);
        $percentage = $free * 100 / $total;
        if (Config::$prod && $percentage < 5) {
            #Do not do anything if mail has already been sent
            if (!\is_file($dir.'/noSpace.flag')) {
                #Clean files
                $this->filesClean();
                #Recalculate percentage
                $percentage = $free * 100 / $total;
                if ($percentage < 5) {
                    #Send mail
                    new NoSpace()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['percentage' => $percentage, 'free' => CuteBytes::bytes($free, 1024), 'total' => CuteBytes::bytes($total, 1024)])->save()->send(Config::ADMIN_MAIL, true);
                    #Generate flag
                    \file_put_contents($dir.'/noSpace.flag', $percentage.'% of space left');
                }
            }
        } elseif (\is_file($dir.'/noSpace.flag')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($dir.'/noSpace.flag');
            #Send mail
            new EnoughSpace()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['percentage' => $percentage, 'free' => CuteBytes::bytes($free, 1024), 'total' => CuteBytes::bytes($total, 1024)])->save()->send(Config::ADMIN_MAIL, true);
        }
    }
    
    /**
     * Function to send alert database going down
     * @return void
     */
    public function dbDown(): void
    {
        #Get directory
        $dir = \sys_get_temp_dir();
        if (!\is_dir($dir) && !\mkdir($dir) && !\is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        $no_db_flag = $dir.'/no_db.flag';
        $crash_flag = Config::$work_dir.'/data/backups/crash.flag';
        if (Config::$prod && !Config::$dbup) {
            #Do not do anything if mail has already been sent
            if (!\is_file($no_db_flag)) {
                #Send mail
                new DatabaseDown()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['errors' => \print_r(Pool::$errors, true)])->save()->send(Config::ADMIN_MAIL, true);
                #Generate flag
                \file_put_contents($no_db_flag, 'Database is down');
            }
            return;
        }
        if (\is_file($crash_flag)) {
            $error_text = \file_get_contents($crash_flag);
            try {
                $result = Query::query('UPDATE `sys__settings` SET `value` = 0 WHERE `setting` = \'maintenance\';');
            } catch (\Throwable) {
                $result = false;
            }
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($crash_flag);
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($no_db_flag);
            #Send mail
            new DatabaseUp()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['maintenance' => true, 'restored' => $result, 'error_text' => $error_text])->save()->send(Config::ADMIN_MAIL, true);
            return;
        }
        if (\is_file($no_db_flag)) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($no_db_flag);
            #Send mail
            new DatabaseUp()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['maintenance' => false])->save()->send(Config::ADMIN_MAIL, true);
        }
    }
    
    /**
     * Check if error log exists and notify about it
     * @return void
     */
    public function errorLog(): void
    {
        $dir = \sys_get_temp_dir();
        if (!\is_dir($dir) && !\mkdir($dir) && !\is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        $error_log = Config::$work_dir.'/logs/php.log';
        $error_flag = $dir.'/error_log.flag';
        #Check if the error log exists and there is no flag yet (thus no notification has been sent)
        if (\is_file($error_log)) {
            if (!\is_file($error_flag)) {
                #Send mail
                new ErrorLog()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate()->save()->send(Config::ADMIN_MAIL, true);
                #Generate flag
                \file_put_contents($error_flag, 'Error log found');
            }
            return;
        }
        if (\is_file($error_flag)) {
            #If the error log does not exist - remove the flag if it exists
            \unlink($error_flag);
        }
    }
    
    /**
     * Clean temp folder
     * @param string $path     Path to clean
     * @param int    $max_age  Oldest age of a file in minutes
     * @param int    $max_size Maximum size to remove
     *
     * @return void
     */
    private function recursiveClean(string $path, int $max_age = 60, int $max_size = 1024): void
    {
        #Get current maximum execution time
        $cur_max_time = (int)\ini_get('max_execution_time');
        #Iterration can take a long time, so let it run its course
        \set_time_limit(0);
        #Restore execution time
        \set_time_limit($cur_max_time);
        #Sanitize values
        if ($max_age < 0) {
            #Reset to default 1 hour cache
            $max_age = 60 * 60;
        } else {
            #Otherwise, convert into minutes (seconds do not make sense here at all)
            $max_age *= 60;
        }
        if ($max_size < 0) {
            #Consider that the size limit was removed
            $max_size = 0;
        } else {
            #Otherwise, convert to megabytes (lower than 1 MB does not make sense)
            $max_size *= 1024 * 1024;
        }
        #Set a list of empty folders (removing within iteration seems to cause a fatal error)
        $empty_dirs = [];
        if ($max_age > 0) {
            #Get the oldest allowed time
            $oldest = \time() - $max_age;
            #Garbage collector for old files, if the file pool is used
            $size_to_remove = 0;
            #Initiate iterator
            $file_iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            #List of files to remove
            $to_delete = [];
            #List of fresh files with their sizes
            $fresh = [];
            #Iterate the files to get size and date first
            #Using catch to handle potential race condition, when a file gets removed by a different process before the check gets called
            try {
                foreach ($file_iterator as $file) {
                    if (\is_dir($file)) {
                        #Check if empty
                        if (!new \RecursiveDirectoryIterator($file, \FilesystemIterator::SKIP_DOTS)->valid()) {
                            #Remove directory
                            $empty_dirs[] = $file;
                        }
                    } elseif (\is_file($file)) {
                        #If we have age restriction, check if the age
                        $time = \filemtime($file);
                        if ($max_size > 0) {
                            $size = \filesize($file);
                        } else {
                            $size = 0;
                        }
                        if ($max_age > 0 && \is_int($time) && $time <= $oldest) {
                            #Add to a list of files to delete
                            $to_delete[] = $file;
                            if ($max_size > 0) {
                                $size_to_remove += $size;
                            }
                        } elseif ($max_size > 0) {
                            $fresh[] = ['path' => $file, 'time' => $time, 'size' => $size];
                        }
                    }
                }
                #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
            } catch (\Throwable) {
                #Do nothing
            }
            #If we have size limitation and a list of fresh items is not empty
            if ($max_size > 0 && \count($fresh) !== 0) {
                #Calclate total size
                $total_size = \array_sum(\array_column($fresh, 'size')) + $size_to_remove;
                #Check if we are already removing enough. If so - skip further checks
                if ($total_size - $size_to_remove >= $max_size) {
                    #Sort files by time from oldest to newest
                    \usort($fresh, static function ($a, $b) {
                        return $a['time'] <=> $b['time'];
                    });
                    #Iterrate list
                    foreach ($fresh as $file) {
                        $to_delete[] = $file['path'];
                        $size_to_remove += $file['size'];
                        #Check if removing this file will be enough and break the cycle if it is
                        if ($total_size - $size_to_remove < $max_size) {
                            break;
                        }
                    }
                }
            }
            foreach ($to_delete as $file) {
                #Using catch to handle potential race condition, when a file gets removed by a different process before the check gets called
                try {
                    #Check if the file is old enough
                    if (\is_file($file)) {
                        #Remove the file
                        /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
                        @\unlink($file);
                        #Remove the parent directory if empty
                        if (!new \RecursiveDirectoryIterator(\dirname($file), \FilesystemIterator::SKIP_DOTS)->valid()) {
                            $empty_dirs[] = $file;
                        }
                    }
                    #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
                } catch (\Throwable) {
                    #Do nothing
                }
            }
        }
        #Garbage collector for empty directories
        foreach ($empty_dirs as $dir) {
            if ($dir !== $path) {
                #Using catch to handle potential race condition, when a directory gets removed by a different process before the check gets called
                try {
                    /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
                    @\rmdir($dir);
                    #Remove the parent directory if empty
                    if (\dirname($dir) !== $path && !new \RecursiveDirectoryIterator(\dirname($dir), \FilesystemIterator::SKIP_DOTS)->valid()) {
                        \rmdir(\dirname($dir));
                    }
                } catch (\Throwable) {
                    #Do nothing
                }
            }
        }
        #Restore execution time
        \set_time_limit($cur_max_time);
    }
}
