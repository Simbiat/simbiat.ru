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
            return Query::query('UPDATE `talks__posts` SET `updated`=`updated`, `locked`=1 WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) AND `locked`=0;');
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
            $limit = User::AVATAR_LIMIT;
            #Get users with more than 10 unused avatars
            $users = Query::query('SELECT `user_id`, COUNT(*) as `count` FROM `uc__avatars` WHERE `current`=0 GROUP BY `user_id` HAVING `count`>:limit;', [':limit' => [$limit, 'int']], return: 'pair');
            #Iterate over the list
            foreach ($users as $user => $count) {
                #Count how many avatars are excessive
                $excess = $count - $limit;
                #Get the IDs of the avatars to remove
                $to_delete = Query::query(
                    'SELECT `uc__avatars`.`file_id` FROM `uc__avatars` INNER JOIN `sys__files` ON `uc__avatars`.`file_id`=`sys__files`.`file_id` WHERE `uc__avatars`.`user_id`=:user_id AND `current`=0 ORDER BY `size` DESC, `added` LIMIT :limit;',
                    [
                        ':user_id' => [$user, 'int'],
                        ':limit' => [$excess, 'int'],
                    ], return: 'column'
                );
                #Log the change
                Security::log('Avatar', 'Automatically deleted avatars', $to_delete, user_id: $user);
                #Delete from DB
                Query::query(
                    'DELETE FROM `uc__avatars` WHERE `user_id`=:user_id AND `current`=0 AND `file_id` IN (:toDelete);',
                    [
                        ':user_id' => [$user, 'int'],
                        ':toDelete' => [$to_delete, 'in', 'string'],
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
            $db_files = Query::query('SELECT `file_id`, `extension`, `mime`, `sys__files`.`user_id`, IF(`file_id` IN (SELECT `file_id` FROM `talks__attachments`), 1, 0) as `attachment`, IF(`file_id` IN (SELECT `og_image` FROM `talks__threads`), 1, 0) as `og_image`, IF(`file_id` IN (SELECT `file_id` FROM `uc__avatars`), 1, 0) as `avatar`, IF(`file_id` IN (SELECT `icon` FROM `talks__sections`), 1, 0) as `section`, IF(`file_id` IN (SELECT `icon` FROM `talks__types`), 1, 0) as `section_defaults` FROM `sys__files` WHERE `added` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) HAVING `attachment`+`og_image`+`avatar`+`section`+`section_defaults`=0;', return: 'all');
            #Iterrate through the list
            foreach ($db_files as $file) {
                #Get the expected full path of the file
                if (\preg_match('/^image\/.+$/ui', $file['mime']) === 1) {
                    $full_path = Config::$uploaded_img;
                } else {
                    $full_path = Config::$uploaded;
                }
                $full_path .= '/'.mb_substr($file['file_id'], 0, 2, 'UTF-8').'/'.mb_substr($file['file_id'], 2, 2, 'UTF-8').'/'.mb_substr($file['file_id'], 4, 2, 'UTF-8').'/'.$file['file_id'].'.'.$file['extension'];
                #Log the removal
                Security::log('File upload', 'Automatically deleted file', $file['file_id'].'.'.$file['extension'], user_id: $file['user_id']);
                #Remove from DB
                Query::query('DELETE FROM `sys__files` WHERE `file_id`=:file_id;', [':file_id' => $file['file_id']]);
                #Remove from drive
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                @\unlink($full_path);
            }
            #Get all files from the drive
            $all_files = new \AppendIterator();
            $all_files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Config::$uploaded, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            $all_files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Config::$uploaded_img, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST));
            #Now get only the file IDs from DB. We need to do it a 2nd time in the function and AFTER directory iterators, to minimize the chances of removing a file that is in the process of being uploaded
            $db_files = Query::query('SELECT `file_id` FROM `sys__files`;', return: 'column');
            foreach ($all_files as $file) {
                #Ignore directories and .gitignore and check if the file's ID is present in a database
                if (!\is_dir($file) && \preg_match('/\.gitignore$/ui', $file) !== 1 && !\in_array(\pathinfo($file, \PATHINFO_FILENAME), $db_files, true)) {
                    #Get a directory tree for the file
                    $dirs = [dirname($file), dirname($file, 2), dirname($file, 3)];
                    #Log the removal
                    Security::log('File upload', 'Automatically deleted file', \basename($file), user_id: Config::USER_IDS['System user']);
                    #Remove the file
                    /** @noinspection PhpUsageOfSilenceOperatorInspection */
                    @\unlink($file);
                    #Remove a directory tree, if empty
                    foreach ($dirs as $dir) {
                        if (!new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)->valid()) {
                            /** @noinspection PhpUsageOfSilenceOperatorInspection */
                            @\rmdir($dir);
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
