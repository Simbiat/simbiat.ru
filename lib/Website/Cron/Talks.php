<?php
#Functions meant to be called from Cron, so they are not used "directly"
/** @noinspection PhpUnused */
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use Simbiat\Database\Query;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;
use Simbiat\Website\usercontrol\User;
use function dirname;

/**
 * Functions supposed to be called from Cron
 */
class Talks
{
    /**
     * Function to lock posts older than 1 day so that only users with special permission can edit them
     * @return bool
     */
    public function lockPosts(): bool
    {
        try {
            return Query::query('UPDATE `talks__posts` SET `updated`=`updated`, `locked`=1 WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) AND `locked`=0;');
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
    
    /**
     * Function to limit the number of unused avatars per user
     * @return bool
     */
    public function cleanAvatars(): bool
    {
        try {
            $limit = User::avatarLimit;
            #Get users with more than 10 unused avatars
            $users = Query::query('SELECT `userid`, COUNT(*) as `count` FROM `uc__avatars` WHERE `current`=0 GROUP BY `userid` HAVING `count`>:limit;', [':limit' => [$limit, 'int']], return: 'pair');
            #Iterate over the list
            foreach ($users as $user => $count) {
                #Count how many avatars are excessive
                $excess = $count - $limit;
                #Get the IDs of the avatars to remove
                $toDelete = Query::query(
                    'SELECT `uc__avatars`.`fileid` FROM `uc__avatars` INNER JOIN `sys__files` ON `uc__avatars`.`fileid`=`sys__files`.`fileid` WHERE `uc__avatars`.`userid`=:userid AND `current`=0 ORDER BY `size` DESC, `added` LIMIT :limit;',
                    [
                        ':userid' => [$user, 'int'],
                        ':limit' => [$excess, 'int'],
                    ], return: 'column'
                );
                #Log the change
                Security::log('Avatar', 'Automatically deleted avatars', $toDelete, userid: $user);
                #Delete from DB
                Query::query(
                    'DELETE FROM `uc__avatars` WHERE `userid`=:userid AND `current`=0 AND `fileid` IN (:toDelete);',
                    [
                        ':userid' => [$user, 'int'],
                        ':toDelete' => [$toDelete, 'in', 'string'],
                    ]
                );
            }
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
    }
    
    /**
     * Function to clean uploaded files that either do not physically exist or are not linked as an avatar, attachment or og:image
     * @return bool
     */
    public function cleanFiles(): bool
    {
        #Get the files from DB
        try {
            #PHPStorm does not like HAVING in the query, even though it is completely normal, so suppressing inspection for it
            /** @noinspection SqlAggregates */
            $dbFiles = Query::query('SELECT `fileid`, `extension`, `mime`, `sys__files`.`userid`, IF(`fileid` IN (SELECT `fileid` FROM `talks__attachments`), 1, 0) as `attachment`, IF(`fileid` IN (SELECT `ogimage` FROM `talks__threads`), 1, 0) as `ogimage`, IF(`fileid` IN (SELECT `fileid` FROM `uc__avatars`), 1, 0) as `avatar`, IF(`fileid` IN (SELECT `icon` FROM `talks__sections`), 1, 0) as `section`, IF(`fileid` IN (SELECT `icon` FROM `talks__types`), 1, 0) as `section_defaults` FROM `sys__files` WHERE `added` <= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) HAVING `attachment`+`ogimage`+`avatar`+`section`+`section_defaults`=0;', return: 'all');
            #Iterrate through the list
            foreach ($dbFiles as $file) {
                #Get the expected full path of the file
                if (preg_match('/^image\/.+$/ui', $file['mime']) === 1) {
                    $fullPath = Config::$uploadedImg;
                } else {
                    $fullPath = Config::$uploaded;
                }
                $fullPath .= '/'.mb_substr($file['fileid'], 0, 2, 'UTF-8').'/'.mb_substr($file['fileid'], 2, 2, 'UTF-8').'/'.mb_substr($file['fileid'], 4, 2, 'UTF-8').'/'.$file['fileid'].'.'.$file['extension'];
                #Log the removal
                Security::log('File upload', 'Automatically deleted file', $file['fileid'].'.'.$file['extension'], userid: $file['userid']);
                #Remove from DB
                Query::query('DELETE FROM `sys__files` WHERE `fileid`=:fileid;', [':fileid' => $file['fileid']]);
                #Remove from drive
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @unlink($fullPath);
            }
            #Get all files from the drive
            $allFiles = new \AppendIterator();
            $allFiles->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Config::$uploaded, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            $allFiles->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Config::$uploadedImg, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            #Now get only the file IDs from DB. We need to do it a 2nd time in the function and AFTER directory iterators, to minimize the chances of removing a file that is in the process of being uploaded
            $dbFiles = Query::query('SELECT `fileid` FROM `sys__files`;', return: 'column');
            foreach ($allFiles as $file) {
                #Ignore directories and .gitignore and check if the file's ID is present in a database
                if (!is_dir($file) && preg_match('/\.gitignore$/ui', $file) !== 1 && !\in_array(pathinfo($file, PATHINFO_FILENAME), $dbFiles, true)) {
                    #Get a directory tree for the file
                    $dirs = [dirname($file), dirname($file, 2), dirname($file, 3)];
                    #Log the removal
                    Security::log('File upload', 'Automatically deleted file', basename($file), userid: Config::userIDs['System user']);
                    #Remove the file
                    /** @noinspection PhpUsageOfSilenceOperatorInspection */
                    @unlink($file);
                    #Remove a directory tree, if empty
                    foreach ($dirs as $dir) {
                        if (!new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)->valid()) {
                            /** @noinspection PhpUsageOfSilenceOperatorInspection */
                            @rmdir($dir);
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
