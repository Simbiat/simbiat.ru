<?php

declare(strict_types=1);

namespace App\Command;

use App\Security\Security;
use App\Service\Errors;
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
        $output->writeln(Errors::logfmt('Recalculating Argon settings...'));
        try {
            if (\count(Security::argonCalc(true)) === 0) {
                $output->writeln(Errors::logfmt('Failed to set Argon settings'));

                return Command::FAILURE;
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
