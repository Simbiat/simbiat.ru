<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\LogType;
use App\Enum\NotificationType;
use App\Enum\SystemUser;
use App\Enum\TalkType;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\ArrayHelpers\Converters;
use Simbiat\Database\Query;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove entries that would violate foreign key restrictions if they were used.
 */
final class ForeignKeys
{
    /**
     * Remove entries that would violate foreign key restrictions if they were used.
     * Service does not use them normally due to performance hit and to have potentially more flexibility in business logic.
     * While logic should be written in a way to prevent such "violations", having a job to forcefully remove them is useful.
     * Nullable values will be set to NULL.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:clean:foreign_keys', description: 'Clean-up entries that would have picked up by Foreign Keys')]
    public function clean(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Cleaning foreign keys...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                #TODO Actually write queries for this
                #Logs
                Query::query('DELETE FROM `sys__logs` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(LogType::class), 'in', 'int']]);
                Query::query('UPDATE `sys__logs` SET `user_id`=:user_id WHERE `user_id` NOT IN (SELECT `user_id` FROM `uc__users`);', [':user_id' => SystemUser::Unknown->value]);
                #Notification types
                Query::query('DELETE FROM `sys__notifications` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(NotificationType::class), 'in', 'int']]);
                #Unsupported section types
                Query::query('DELETE FROM `talks__sections` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(TalkType::class), 'in', 'int']]);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
