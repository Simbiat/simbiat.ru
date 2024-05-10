<?php
#Most of the functions are meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Config\Common;
use Simbiat\Config\FFTracker;
use Simbiat\Config\Talks;
use Simbiat\Database\Pool;
use Simbiat\usercontrol\Email;
use Simbiat\usercontrol\Session;

class Maintenance
{
    #Files clean
    public function filesClean(): bool
    {
        #Clean HTML cache
        $this->recursiveClean(Common::$htmlCache, 1440, 2048);
        #Clean merged crests cache
        $this->recursiveClean(FFtracker::$mergedCrestsCache, 14400);
        #Clean temp directory
        $this->recursiveClean(sys_get_temp_dir(), 4320, 0);
        return true;
    }

    #Clean session data
    public function sessionClean(): bool
    {
        try {
            return (bool)(new Session)->gc(300);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }

    #Clean cookies data
    public function cookiesClean(): bool
    {
        #Get existing cookies, that need to be cleaned
        try {
            $items = HomePage::$dbController->selectAll('SELECT * FROM `uc__cookies` WHERE `time`<=DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 MONTH)');
        } catch (\Throwable) {
            $items = [];
        }
        if (!empty($items)) {
            #Generate queries
            $queries = [];
            foreach ($items as $item) {
                #Register logout action
                $queries[] = [
                    'INSERT INTO `sys__logs`(`userid`, `ip`, `useragent`, `type`, `action`) VALUES (:userid, :ip, :useragent, 2, \'Logged out due to cookie timeout\')',
                    [
                        ':userid'=>$item['userid'],
                        ':ip'=>$item['ip'],
                        ':useragent'=>$item['useragent'],
                    ],
                ];
                #Actually delete cookie
                $queries[] = [
                    'DELETE FROM `uc__cookies`WHERE `cookieid`=:id',
                    [
                        ':id'=>$item['cookieid'],
                    ]
                ];
            }
        }
        #Add query to delete cookies for system users explicitly
        $queries[] = 'DELETE FROM `uc__cookies` WHERE `userid` IN ('.Talks::userIDs['Unknown user'].', '.Talks::userIDs['System user'].', '.Talks::userIDs['Deleted user'].');';
        $result = true;
        if (!empty($queries)) {
            try {
                $result = HomePage::$dbController->query($queries);
            } catch (\Throwable $e) {
                Errors::error_log($e);
                $result = false;
            }
        }
        return $result;
    }

    #Clean logs
    public function logsClean(): bool
    {
        $queries = [];
        #Clean audit logs
        $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 YEAR)';
        #Clean Cron errors
        $queries[] = 'DELETE FROM `cron__errors` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 YEAR)';
        try {
            $result = HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            $result = false;
        }
        return $result;
    }

    #Clean statistical data
    public function statisticsClean(): bool|string
    {
        $queries = [];
        #Remove pages that have not been viewed in 2 years
        $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 YEAR)';
        #Remove visitors who have not come in 2 years
        $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 YEAR)';
        try {
            $result = HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            $result = false;
        }
        return $result;
    }
    
    public function dbOptimize(): bool|string
    {
        $cron = (new Cron());
        try {
            HomePage::$dbController->query('UPDATE `sys__settings` SET `value`=1 WHERE `setting`=\'maintenance\'');
            $cron->setSetting('enabled', 0);
            (new optimizeTables())->setJsonPath('.\/data\/tables.json')->optimize('simbiatr_simbiat', true, true);
        } catch (\Throwable $e) {
            return $e->getMessage()."\r\n".$e->getTraceAsString();
        } finally {
            HomePage::$dbController->query('UPDATE `sys__settings` SET `value`=1 WHERE `setting`=\'maintenance\'');
            $cron->setSetting('enabled', 1);
        }
        return true;
    }

    #Function to get available disk space
    public function noSpace(): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        #Get free space in percentage
        $percentage = disk_free_space($dir)*100/disk_total_space($dir);
        if (Common::$PROD && $percentage < 5) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noSpace.flag')) {
                #Clean files
                $this->filesClean();
                #Recalculate percentage
                $percentage = disk_free_space($dir)*100/disk_total_space($dir);
                if ($percentage < 5) {
                    #Send mail
                    (new Email(Common::adminMail))->send('[Alert]: Low space', ['percentage' => $percentage], 'Simbiat');
                    #Generate flag
                    file_put_contents($dir . '/noSpace.flag', $percentage . '% of space left');
                }
            }
        } elseif (is_file($dir.'/noSpace.flag')) {
            @unlink($dir . '/noSpace.flag');
            #Send mail
            (new Email(Common::adminMail))->send('[Resolved]: Low space', ['percentage' => $percentage], 'Simbiat');
        }
    }

    #Function to send alert database going down
    public function dbDown(): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        if (Common::$PROD && !HomePage::$dbup) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noDB.flag')) {
                #Send mail
                (new Email(Common::adminMail))->send('[Alert]: Database is down', ['errors' => print_r(Pool::$errors, true)], 'Simbiat');
                #Generate flag
                file_put_contents($dir . '/noDB.flag', 'Database is down');
            }
        } elseif (is_file($dir.'/noDB.flag')) {
            @unlink($dir . '/noDB.flag');
            #Send mail
            (new Email(Common::adminMail))->send('[Resolved]: Database is down', username: 'Simbiat');
        }
    }

    #Clean temp folder
    private function recursiveClean(string $path, int $maxAge = 60, int $maxSize = 1024): void
    {
        #Get current maximum execution time
        $curMaxTime = (int)ini_get('max_execution_time');
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
        #Set list of empty folders (removing within iteration seems to cause fatal error)
        $emptyDirs = [];
        if ($maxAge > 0) {
            #Get the oldest allowed time
            $oldest = time() - $maxAge;
            #Garbage collector for old files, if files pool is used
            $sizeToRemove = 0;
            #Initiate iterator
            $fileSI = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
            #List of files to remove
            $toDelete = [];
            #List of fresh files with their sizes
            $fresh = [];
            #Iterate the files to get size and date first
            #Using catch to handle potential race condition, when file gets removed by a different process before the check gets called
            try {
                foreach ($fileSI as $file) {
                    if (is_dir($file)) {
                        #Check if empty
                        if (!(new \RecursiveDirectoryIterator($file, \FilesystemIterator::SKIP_DOTS))->valid()) {
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
                        if ($maxAge > 0 && $time <= $oldest) {
                            #Add to list of files to delete
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
            #If we have size limitation and list of fresh items is not empty
            if ($maxSize > 0 && !empty($fresh)) {
                #Calclate total size
                $totalSize = array_sum(array_column($fresh,'size')) + $sizeToRemove;
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
                        #Check if removing this file will be enough and break cycle if it is
                        if ($totalSize - $sizeToRemove < $maxSize) {
                            break;
                        }
                    }
                }
            }
            foreach ($toDelete as $file) {
                #Using catch to handle potential race condition, when file gets removed by a different process before the check gets called
                try {
                    #Check if file and is old enough
                    if (is_file($file)) {
                        #Remove the file
                        unlink($file);
                        #Remove parent directory if empty
                        if (!(new \RecursiveDirectoryIterator(dirname($file), \FilesystemIterator::SKIP_DOTS))->valid()) {
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
                #Using catch to handle potential race condition, when directory gets removed by a different process before the check gets called
                try {
                    @rmdir($dir);
                    #Remove parent directory if empty
                    if (dirname($dir) !== $path && !(new \RecursiveDirectoryIterator(dirname($dir), \FilesystemIterator::SKIP_DOTS))->valid()) {
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
