<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Thread;
use App\Entity\User;
use App\Enum\LogType;
use App\Enum\TalkType;
use App\Security\Security;
use App\Security\Session;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Query;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Various commands for Talks
 */
final class Talks
{
    /**
     * Clean old avatars
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:talks:clean_avatars', description: 'Clean old avatars')]
    public function cleanAvatars(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning old avatars...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
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
                    Security::log(LogType::Avatar->value, 'Automatically deleted avatars', $to_delete, user_id: $user);
                    #Delete from DB
                    Query::query(
                        'DELETE FROM `uc__avatars` WHERE `user_id`=:user_id AND `current`=0 AND `file_id` IN (:toDelete);',
                        [
                            ':user_id' => [$user, 'int'],
                            ':toDelete' => [$to_delete, 'in', 'string'],
                        ]
                    );
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Lock posts older than 1 day so that only users with special permission can edit them
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:talks:lock_posts', description: 'Lock posts')]
    public function lockPosts(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Locking posts...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                Query::query('UPDATE `talks__posts` SET `updated`=`updated`, `locked`=1 WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) AND `locked`=0;');
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Close tickets that have been inactive for some time
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:talks:close_tickets', description: 'Close tickets that have been inactive for some time')]
    public function closeTickets(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Closing tickets...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $tickets = Query::query('SELECT `thread_id` FROM `talks__threads` LEFT JOIN `talks__sections` ON `talks__threads`.`section_id`=`talks__sections`.`section_id` WHERE `type`=:type AND `last_post` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 MONTH);', [':type' => TalkType::Support->value], return: 'column');
                foreach ($tickets as $ticket) {
                    try {
                        (void)new Thread($ticket)->setClosed(true);
                    } catch (\Throwable $throwable) {
                        Errors::error_log($throwable);
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
     * Remove empty threads older than 1 day
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:talks:empty_threads', description: 'Remove empty threads')]
    public function removeEmptyThreads(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Removing empty threads...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                Query::query('DELETE FROM `talks__threads` WHERE `created` <= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 DAY) AND `posts`=0;');
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
