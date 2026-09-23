<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Errors;
use Doctrine\DBAL\Connection;
use Simbiat\Cron\Agent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run CRON tasks from the database (using CRON library)
 */
final readonly class Cron
{
    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(
        /** @noinspection InterfacesAsConstructorDependenciesInspection */
        private Connection $connection,
    ) {}

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
            /* @var \PDO $pdo IDE complains due to more generic object */
            $pdo = $this->connection->getNativeConnection();
            $output->writeln(Errors::logfmt('Processing CRON tasks from DB...'));
            new Agent($pdo)->process(50);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable, cli: true);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
