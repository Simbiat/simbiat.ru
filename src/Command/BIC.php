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
 * Run tasks related to BIC library
 */
final class BIC
{
    /**
     * Clean old sessions
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:bic:update', description: 'Update BIC library')]
    public function libraryUpdate(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Updating BIC library...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $result = new BICLibrary()->update(true);
                // Ignore failures to download the file. CBR started using DDoS-Guard, which seems to be blocking the server most of the time now
                if (
                    \is_string($result) &&
                    !\is_numeric($result) &&
                    !\str_contains($result, 'Не удалось скачать файл')
                ) {
                    throw new \RuntimeException($result);
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
