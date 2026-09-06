<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Config;
use App\Service\Errors;
use Simbiat\Cron\Agent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run CRON tasks from the database (using CRON library)
 */
final class Cron
{
    /**
     * Run CRON tasks from the database (using CRON library)
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:cron', description: 'Run CRON tasks from the database')]
    public function cron(OutputInterface $output): int
    {
        try {
            // Connect to DB
            Config::dbConnect();
            // Run cron
            if (Config::$dbup && !Config::$db_update) {
                $output->writeln(Errors::logfmt('Processing CRON tasks from DB...'));
                new Agent()->process(50);
            } else {
                $output->writeln(Errors::logfmt('DB is down, skipping...'));
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
