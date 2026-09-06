<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\LogType;
use App\Enum\SystemUser;
use App\Security\Security;
use App\Security\Session;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Query;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run every-minute maintenance tasks
 */
final class Clean
{
    /**
     * Clean old sessions
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:sessions', description: 'Clean old sessions')]
    public function sessions(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning old sessions...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                new Session()->gc();
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clean old cookies
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:cookies', description: 'Clean old sessions')]
    public function cookies(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning old cookies...'));
        try {
            // Connect to DB
            Config::dbConnect();
            // Get existing cookies that need to be cleaned
            if (Config::$dbup) {
                try {
                    /** @var list<array{cookie_id: string, user_id: int, ip: string, user_agent: string, time: string}> $items */
                    $items = Query::query(
                        'SELECT `cookie_id`, `user_id`, `ip`, `user_agent`, `time` FROM `uc__cookies` WHERE `user_id` IN (SELECT `user_id` FROM `uc__users` WHERE `system`=1) OR `time`<=DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 MONTH);',
                        return: 'all',
                    );
                } catch (\Throwable) {
                    $items = [];
                }
                foreach ($items as $item) {
                    // Try to delete cookie
                    /** @var int $affected */
                    $affected = Query::query(
                        'DELETE FROM `uc__cookies`WHERE `cookie_id`=:id',
                        [
                            ':id' => $item['cookie_id'],
                        ],
                        return: 'affected',
                    );
                    // If it was deleted, log it
                    if ($affected > 0) {
                        Security::log(LogType::Logout->value, 'Logged out due to cookie timeout', $item, $item['user_id']);
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clean old logs
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:logs', description: 'Clean old logs')]
    public function logs(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning old logs...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $queries = [];
                #Clean audit logs
                $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 YEAR)';
                Query::query($queries);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clean old statistics
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:statistics', description: 'Clean old statistics')]
    public function statistics(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning old statistics...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $queries = [];
                #Remove pages that have not been viewed in 2 years
                $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR);';
                #Remove visitors who have not come in 2 years
                $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR);';
                Query::query($queries);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Remove dead links
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:dead_links', description: 'Remove dead links')]
    public function deadLinks(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Removing dead links...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                #TODO https://github.com/Simbiat/simbiat.ru/issues/96

                return Command::SUCCESS;
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clean uploaded files that either do not physically exist or are not linked as an avatar, attachment, or og:image
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:uploads', description: 'Clean unused uploaded files')]
    public function uploads(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Removing unused uploaded files...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                // PHPStorm does not like HAVING in the query, even though it is completely normal, so suppressing inspection for it
                /** @noinspection SqlAggregates */
                $db_files = Query::query('SELECT `file_id`, `extension`, `mime`, `sys__files`.`user_id`, IF(`file_id` IN (SELECT `file_id` FROM `talks__attachments`), 1, 0) as `attachment`, IF(`file_id` IN (SELECT `og_image` FROM `talks__threads`), 1, 0) as `og_image`, IF(`file_id` IN (SELECT `file_id` FROM `uc__avatars`), 1, 0) as `avatar`, IF(`file_id` IN (SELECT `icon` FROM `talks__sections`), 1, 0) as `section`, IF(`file_id` IN (SELECT `icon` FROM `talks__types`), 1, 0) as `section_defaults` FROM `sys__files` WHERE `added` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) HAVING `attachment`+`og_image`+`avatar`+`section`+`section_defaults`=0;', return: 'all');
                // Iterate through the list
                foreach ($db_files as $file) {
                    #Get the expected full path of the file
                    if (\preg_match('/^image\/.+$/ui', $file['mime']) === 1) {
                        $full_path = Config::$uploaded_img;
                    } else {
                        $full_path = Config::$uploaded;
                    }
                    $full_path .= '/'.mb_substr($file['file_id'], 0, 2, 'UTF-8').'/'.mb_substr($file['file_id'], 2, 2, 'UTF-8').'/'.mb_substr($file['file_id'], 4, 2, 'UTF-8').'/'.$file['file_id'].'.'.$file['extension'];
                    // Log the removal
                    Security::log(LogType::FileUpload->value, 'Automatically deleted file', $file['file_id'].'.'.$file['extension'], user_id: $file['user_id']);
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
                        Security::log(LogType::FileUpload->value, 'Automatically deleted file', \basename($file), user_id: SystemUser::System->value);
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
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Remove old notifications
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:notifications', description: 'Remove old notifications')]
    public function notifications(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Removing old notifications...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                Query::query('DELETE FROM `sys__notifications` WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 YEAR);');
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
