<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Errors;
use Simbiat\Argon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commands to do various maintenance stuff
 */
final class Maintain
{
    /**
     * Recalculate Argon settings
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:maintain:argon', description: 'Recalculate Argon settings')]
    public function argon(OutputInterface $output): int
    {
        $output->writeln('Calculating Argon settings...');
        try {
            $result = new Argon()->calc();
            if (\count($result) === 0) {
                $output->writeln('Failed to set Argon settings');

                return Command::FAILURE;
            }
            foreach ($result as $key => $value) {
                $output->writeln("Argon setting $key: $value");
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
