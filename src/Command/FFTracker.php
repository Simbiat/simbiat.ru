<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Config;
use App\Service\Errors;
use App\Service\FFXIVStatistics;
use Simbiat\Cron\Agent;
use Simbiat\Cron\EventTypes;
use Simbiat\Cron\TaskInstance;
use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Various commands for FFXIV Tracker
 */
final class FFTracker
{
    /**
     * Generate tasks to attempt to register new characters
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:ffxiv:characters', description: 'Register new FFXIV characters')]
    public function registerNewCharacters(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Registering new FFXIV characters...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $cron = new TaskInstance();
                // Try to register new characters
                $max_id = Query::query(
                    'SELECT MAX(`character_id`) as `character_id` FROM `ffxiv__character`;',
                    return: 'value',
                );
                // We can't go higher than MySQL max unsigned integer. Unlikely we will ever get to it, but who knows?
                $new_max_id = \min($max_id + 500, 4294967295);
                if ((int) $max_id < (int) $new_max_id) {
                    for ($character = ($max_id + 1); (int) $character <= (int) $new_max_id; $character++) {
                        $extra_for_error = 'character ID '.$character;
                        $cron->settingsFromArray(
                            ['task' => 'ff_update_entity', 'arguments' => [(string) $character, 'character'], 'message' => 'Updating character with ID '.$character],
                        )->add();
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable, $extra_for_error ?? '');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Update FFXIV statistics
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:ffxiv:statistics', description: 'Update FFXIV statistics')]
    public function updateStatistics(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Updating FFXIV statistics...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $cron_agent = new Agent();
                foreach (['raw', 'characters', 'groups', 'achievements', 'timelines', 'other', 'bugs'] as $type) {
                    $cron_agent->log('Updating FFXIV '.$type.' statistics...', EventTypes::CustomInformation);
                    new FFXIVStatistics()->update($type);
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Update FFXIV servers
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:ffxiv:servers', description: 'Update FFXIV servers')]
    public function updateServers(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Updating FFXIV servers...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $lodestone = (new Lodestone());
                #Get server
                $worlds = $lodestone->getWorldStatus()->getResult()['worlds'];
                #Prepare queries
                $queries = [];
                foreach ($worlds as $data_center => $servers) {
                    foreach ($servers as $server => $status) {
                        $queries[] = [
                            'INSERT IGNORE INTO `ffxiv__server` (`server`, `data_center`) VALUES (:server, :data_center)',
                            [':server' => $server, ':data_center' => $data_center],
                        ];
                    }
                }
                Query::query($queries);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
