<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat\Talks;

use Simbiat\Config\Common;
use Simbiat\HomePage;

class Cron
{
    #Function to lock posts older than 1 day, so that only users with special permission can edit them
    public function lockPosts(): bool
    {
        try {
            return HomePage::$dbController->query('UPDATE `talks__posts` SET `locked`=1 WHERE `created` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY) AND `locked`=0;');
        } catch (\Throwable) {
            return false;
        }
    }
    
    #Function to limit the number of unused avatars per user
    public function cleanAvatars(): bool
    {
        try {
            $limit = 10;
            #Get users with more than 10 unused avatars
            $users = HomePage::$dbController->selectPair('SELECT `userid`, COUNT(*) as `count` FROM `uc__avatars` WHERE `current`=0 GROUP BY `userid` HAVING `count`>:limit;', [':limit' => [$limit, 'int']]);
            #Iterate over the list
            $queries = [];
            foreach ($users as $user=>$count) {
                #Count how many avatars are excessive
                $excess = $count - $limit;
                #Get the IDs of the avatars to remove
                $toDelete = HomePage::$dbController->selectColumn(
                'SELECT `uc__avatars`.`fileid` FROM `uc__avatars` INNER JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid AND `current`=0 ORDER BY `size` DESC, `added` LIMIT :limit;',
                    [
                        ':userid' => [$user, 'int'],
                        ':limit' => [$excess, 'int'],
                    ]
                );
                #Generate query
                $queries[] = [
                    'DELETE FROM `uc__avatars` WHERE `userid`=:userid AND `fileid` IN (\''.implode('\', \'', $toDelete).'\');',
                    [
                        ':userid' => [$user, 'int'],
                    ]
                ];
            }
            #Run the queries
            return HomePage::$dbController->query($queries);
        } catch (\Throwable) {
            return false;
        }
    }
    
    #Function to clean uploaded files, that either do not physically exist or are not linked as an avatar, attachment or og:image
    public function cleanFiles(): bool
    {
        #Get the files from DB
        try {
            $dbFiles = HomePage::$dbController->selectAll('SELECT `fileid`, `extension`, `mime`, IF((SELECT `fileid` FROM `talks__attachments` WHERE `talks__attachments`.`fileid`=`sys__files`.`fileid`), 1, 0) as `attachment`, IF((SELECT `fileid` FROM `talks__threads` WHERE `talks__threads`.`ogimage`=`sys__files`.`fileid`), 1, 0) as `ogimage`, IF((SELECT `fileid` FROM `uc__avatars` WHERE `uc__avatars`.`fileid`=`sys__files`.`fileid`), 1, 0) as `avatar` FROM `sys__files`;');
            $queries = [];
            #Iterrate through the list
            foreach ($dbFiles as $key=>$file) {
                #Get expected full path of the file
                if (preg_match('/^image\/.+$/ui', $file['mime']) === 1) {
                    $fullPath = Common::$uploadedImg;
                } else {
                    $fullPath = Common::$uploaded;
                }
                $fullPath .= '/'.substr($file['fileid'], 0, 2).'/'.substr($file['fileid'], 2, 2).'/'.substr($file['fileid'], 4, 2).'/'.$file['fileid'].'.'.$file['extension'];
                if (!is_file($fullPath) || (!$file['attachment'] && !$file['ogimage'] && !$file['avatar'])) {
                    #File needs to be deleted
                    $queries[] = [
                        'DELETE FROM `sys__files` WHERE `fileid`=:fileid;',
                        [':fileid' => $file['fileid']],
                    ];
                    #Remove file from the array
                    unset($dbFiles[$key]);
                    #Remove the file from drive
                    @unlink($fullPath);
                }
            }
            #Remove files from DB
            if (!empty($queries)) {
                HomePage::$dbController->query($queries);
            }
            #Get all files from drive
            $allFiles = new \AppendIterator();
            $allFiles->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Common::$uploaded, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            $allFiles->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Common::$uploadedImg, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            #Now get only the file IDs from DB. We need to do it 2nd time in the function and AFTER directory iterators, to minimize chances of removing a file, that is in process of being uploaded
            $dbFiles = HomePage::$dbController->selectColumn('SELECT `fileid` FROM `sys__files`;');
            foreach ($allFiles as $file) {
                #Ignore directories and .gitignore
                if (!is_dir($file) && preg_match('/.*\.gitignore$/ui', $file) !== 1) {
                    #Check if file's ID is present in database
                    if (!in_array(pathinfo($file, PATHINFO_FILENAME), $dbFiles)) {
                        #Get directory tree for the file
                        $dirs = [dirname($file), dirname($file, 2), dirname($file, 3)];
                        #Remove the file
                        @unlink($file);
                        #Remove directory tree, if empty
                        foreach ($dirs as $dir) {
                            if (!(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS))->valid()) {
                                @rmdir($dir);
                            }
                        }
                    }
                }
            }
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
