<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types=1);
namespace Simbiat\Talks;

use Simbiat\Config\Common;
use Simbiat\Config\Talks;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Security;
use Simbiat\usercontrol\User;

class Cron
{
    #Function to lock posts older than 1 day, so that only users with special permission can edit them
    public function lockPosts(): bool
    {
        try {
            return HomePage::$dbController->query('UPDATE `talks__posts` SET `locked`=1 WHERE `created` <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY) AND `locked`=0;');
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
    
    #Function to limit the number of unused avatars per user
    public function cleanAvatars(): bool
    {
        try {
            $limit = User::avatarLimit;
            #Get users with more than 10 unused avatars
            $users = HomePage::$dbController->selectPair('SELECT `userid`, COUNT(*) as `count` FROM `uc__avatars` WHERE `current`=0 GROUP BY `userid` HAVING `count`>:limit;', [':limit' => [$limit, 'int']]);
            #Iterate over the list
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
                #Log the change
                Security::log('Avatar', 'Automatically deleted avatars', $toDelete, userid: $user);
                #Delete from DB
                HomePage::$dbController->query(
                    'DELETE FROM `uc__avatars` WHERE `userid`=:userid AND `current`=0 AND `fileid` IN (\''.implode('\', \'', $toDelete).'\');',
                    [
                        ':userid' => [$user, 'int'],
                    ]
                );
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
    
    #Function to clean uploaded files, that either do not physically exist or are not linked as an avatar, attachment or og:image
    public function cleanFiles(): bool
    {
        #Get the files from DB
        try {
            #PHPStorm does not like HAVING in the query, even though it is completely normal, so suppressing inspection for it
            /** @noinspection SqlAggregates */
            $dbFiles = HomePage::$dbController->selectAll('SELECT `fileid`, `extension`, `mime`, `sys__files`.`userid`, IF(`fileid` IN (SELECT `fileid` FROM `talks__attachments`), 1, 0) as `attachment`, IF(`fileid` IN (SELECT `ogimage` FROM `talks__threads`), 1, 0) as `ogimage`, IF(`fileid` IN (SELECT `fileid` FROM `uc__avatars`), 1, 0) as `avatar`, IF(`fileid` IN (SELECT `icon` FROM `talks__sections`), 1, 0) as `section`, IF(`fileid` IN (SELECT `icon` FROM `talks__types`), 1, 0) as `section_defaults` FROM `sys__files` HAVING `attachment`+`ogimage`+`avatar`+`section`+`section_defaults`=0;');
            #Iterrate through the list
            foreach ($dbFiles as $file) {
                #Get expected full path of the file
                if (preg_match('/^image\/.+$/ui', $file['mime']) === 1) {
                    $fullPath = Common::$uploadedImg;
                } else {
                    $fullPath = Common::$uploaded;
                }
                $fullPath .= '/'.substr($file['fileid'], 0, 2).'/'.substr($file['fileid'], 2, 2).'/'.substr($file['fileid'], 4, 2).'/'.$file['fileid'].'.'.$file['extension'];
                #Log the removal
                Security::log('File upload', 'Automatically deleted file', $file['fileid'].'.'.$file['extension'], userid: $file['userid']);
                #Remove from DB
                HomePage::$dbController->query('DELETE FROM `sys__files` WHERE `fileid`=:fileid;',[':fileid' => $file['fileid']]);
                #Remove from drive
                @unlink($fullPath);
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
                        #Log the removal
                        Security::log('File upload', 'Automatically deleted file', basename($file), userid: Talks::userIDs['System user']);
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
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
}
