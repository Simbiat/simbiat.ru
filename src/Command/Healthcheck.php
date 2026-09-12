<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\SystemUser;
use App\Notification\DatabaseDown;
use App\Notification\DatabaseUp;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Pool;
use Simbiat\Database\Query;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run regular server status checks
 */
final class Healthcheck
{
    /**
     * Clear old sessions
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     *
     * @throws \RuntimeException
     */
    #[AsCommand(name: 'app:health:db', description: 'Check if DB is up')]
    public function database(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Checking if DB is up...'));
        // Get directory
        $dir = '/app/var/log';
        if (!\is_dir($dir) && !\mkdir($dir) && !\is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }
        $no_db_flag = $dir.'/no_db.flag';
        $crash_flag = $dir.'/backup_crash.flag';
        // If maintenance flag found, it's normal
        if (\is_file('/app/var/log/db_maintenance.flag')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($no_db_flag);

            return Command::SUCCESS;
        }
        // Connect to DB
        Config::dbConnect();
        if (Config::$environment === 'prod' && !Config::$dbup) {
            // Do not do anything if mail has already been sent
            if (!\is_file($no_db_flag)) {
                // Send mail
                new DatabaseDown()->save(
                    SystemUser::Owner->value,
                    ['errors' => \print_r(Pool::$errors, true)],
                    true,
                    false,
                    Config::$admin_email,
                )->send();
                // Generate flag
                \file_put_contents($no_db_flag, 'Database is down');
            }

            return Command::SUCCESS;
        }
        if (\is_file($crash_flag)) {
            $error_text = \file_get_contents($crash_flag);
            try {
                /** @var bool $result */
                $result = Query::query([
                    'UPDATE `sys__settings` SET `value`=0 WHERE `setting` = \'maintenance\';',
                    // Reset any potentially hanged cron jobs (if any)
                    'UPDATE cron__schedule SET `run_by`=NULL, `status`=0 WHERE `run_by` IS NOT NULL;',
                ]);
            } catch (\Throwable) {
                $result = false;
            }
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($crash_flag);
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($no_db_flag);
            // Send mail
            new DatabaseUp()->save(
                SystemUser::Owner->value,
                ['maintenance' => true, 'restored' => $result, 'error_text' => $error_text],
                true,
                false,
                Config::$admin_email,
            )->send();

            return Command::SUCCESS;
        }
        if (\is_file($no_db_flag)) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection Not critical, probably concurrency issue */
            @\unlink($no_db_flag);
            // If we crashed, there is a high chance that some cron jobs failed, as well. Reset any potential jobs like that.
            Query::query('UPDATE cron__schedule SET `run_by`=NULL, `status`=0 WHERE `run_by` IS NOT NULL;');
            // Send mail
            new DatabaseUp()->save(
                SystemUser::Owner->value,
                ['maintenance' => false],
                true,
                false,
                Config::$admin_email,
            )->send();
        }

        return Command::SUCCESS;
    }
}
