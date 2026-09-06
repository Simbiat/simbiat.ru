<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BICLibrary;
use App\Service\Config;
use App\Service\Errors;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update the app
 */
final class Update
{
    /**
     * Below is meant to be run only once, as part of some upgrade to the system.
     * It is encouraged to code stuff in a way to avoid potential impact in case of running it twice.
     * It is also encouraged to use `writeln` to output stuff to the command line for the sake of monitoring.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:update', description: 'Update the app')]
    public function update(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Updating the app...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {

                return Command::SUCCESS;
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        #TODO: Need to figure out a way to prevent run for the 2nd time, if 1st run reaches this part. Or migrations needs to be used, if it fits better.
        return Command::SUCCESS;
    }
}
