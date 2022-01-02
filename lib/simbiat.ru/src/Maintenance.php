<?php
#Most of the functions are meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat;

use Simbiat\usercontrol\Session;

class Maintenance
{
    #Files clean
    public function filesClean(): bool
    {
        #Clean HTML cache
        (new HTMLCache)->gc(1440, 2048);
        #Clean temp directory
        $this->tempClean();
        return true;
    }

    #Clean session data
    public function sessionClean(): bool
    {
        try {
            return (new Session)->gc(300);
        } catch (\Throwable $e) {
            HomePage::error_log($e);
            return false;
        }
    }

    #Clean cookies data
    public function cookiesClean(): bool
    {
        #Get existing cookies, that need to be cleaned
        try {
            $items = HomePage::$dbController->selectAll('SELECT * FROM `uc__cookies` WHERE `time`<=DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 WEEK)');
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
        $result = true;
        if (!empty($queries)) {
            try {
                $result = HomePage::$dbController->query($queries);
            } catch (\Throwable $e) {
                HomePage::error_log($e);
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
        $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 YEAR)';
        #Clean Cron errors
        $queries[] = 'DELETE FROM `cron__errors` WHERE `time`<= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 YEAR)';
        try {
            $result = HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            HomePage::error_log($e);
            $result = false;
        }
        return $result;
    }

    #Clean statistical data
    public function statisticsClean(): bool|string
    {
        $queries = [];
        #Remove pages that have not been viewed in 2 years
        $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 YEAR)';
        #Remove visitors who have not come in 2 years
        $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 YEAR)';
        try {
            $result = HomePage::$dbController->query($queries);
        } catch (\Throwable $e) {
            HomePage::error_log($e);
            $result = false;
        }
        return $result;
    }

    #Function to get available disk space
    public function noSpace(): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        #Get free space in percentage
        $percentage = disk_free_space($dir)*100/disk_total_space($dir);
        if ($percentage < 5) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noSpace.flag')) {
                #Clean files
                $this->filesClean();
                #Recalculate percentage
                $percentage = disk_free_space($dir)*100/disk_total_space($dir);
                if ($percentage < 5) {
                    #Send mail
                    HomePage::sendMail($GLOBALS['siteconfig']['adminmail'], '[Alert]: Low space', $percentage . '% of space left! Check ASAP!');
                    #Generate flag
                    file_put_contents($dir . '/noSpace.flag', $percentage . '% of space left');
                }
            }
        } else {
            if (is_file($dir.'/noSpace.flag')) {
                @unlink($dir . '/noSpace.flag');
                #Send mail
                HomePage::sendMail($GLOBALS['siteconfig']['adminmail'], '[Resolved]: Low space', $percentage . '% of space is now free. Alert resolved.');
            }
        }
    }

    #Function to send alert database going down
    public function dbDown(?string $message = null): void
    {
        #Get directory
        $dir = sys_get_temp_dir();
        if (!HomePage::$dbup) {
            #Do not do anything if mail has already been sent
            if (!is_file($dir.'/noDB.flag')) {
                #Send mail
                HomePage::sendMail($GLOBALS['siteconfig']['adminmail'], '[Alert]: Database is down', 'Database appears to be down! Check ASAP!');
                #Generate flag
                file_put_contents($dir . '/noDB.flag', 'Database is down: '.($message ?? ''));
            }
        } else {
            if (is_file($dir.'/noDB.flag')) {
                @unlink($dir . '/noDB.flag');
                #Send mail
                HomePage::sendMail($GLOBALS['siteconfig']['adminmail'], '[Resolved]: Database is down', 'Database appears to started up again.');
            }
        }
    }

    #Clean temp folder
    private function tempClean(): void
    {
        $fileSI = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(sys_get_temp_dir(), \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        #List of files to remove
        $toDelete = [];
        $oldest = time() - (86400 * 3);
        #Iterate the files to get date first
        #Using catch to handle potential race condition, when file gets removed by a different process before the check gets called
        try {
            foreach ($fileSI as $file) {
                    #Check if file
                    if (is_file($file)) {
                        #If we have age restriction, check if the age
                        $time = filemtime($file);
                        if ($time <= $oldest) {
                            #Add to list of files to delete
                            $toDelete[] = $file;
                        }
                    }
            }
            #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
        } catch (\Throwable) {
            #Do nothing
        }
        foreach ($toDelete as $file) {
            #Using catch to handle potential race condition, when file gets removed by a different process before the check gets called
            try {
                #Check if file and is old enough
                if (is_file($file)) {
                    #Remove the file
                    unlink($file);
                }
                #Catching Throwable, instead of \Error or \Exception, since we can't predict what exactly will happen here
            } catch (\Throwable) {
                #Do nothing
            }
        }
    }
}
