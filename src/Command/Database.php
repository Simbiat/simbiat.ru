<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Maintainer\Analyzer;
use Simbiat\Database\Maintainer\Settings;
use Simbiat\Database\Manage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run tasks related to database maintenance
 */
final class Database
{
    /**
     * Create a list of ordered tables for backup generation
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:db:order', description: 'Generate list of ordered tables')]
    public function order(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Generating ordered list of tables...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $dump_order = '';
                #Get tables in order
                foreach (Manage::showOrderedTables($_ENV['DATABASE_NAME']) as $table) {
                    #Get DDL statement
                    $create = Manage::showCreateTable($table['schema'], $table['table'], if_not_exist: true, add_use: true);
                    if ($create === null) {
                        throw new \UnexpectedValueException('Failed to get CREATE statement for table `'.$table['table'].'`;');
                    }
                    #Add item to the file with dump order
                    $dump_order .= $table['table'].' ';
                }
                \file_put_contents(Config::$work_dir.'/data/backups/recommended_table_order.txt', $dump_order);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Generate DDLs
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:db:ddl', description: 'Generate DDLs')]
    public function ddl(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Generating DDLs...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                if (!\is_dir(Config::$ddl_dir) && !\mkdir(Config::$ddl_dir, recursive: true) && !\is_dir(Config::$ddl_dir)) {
                    Errors::error_log(new \RuntimeException('Failed to create DDL directory'));
                }
                #Clean up SQL files, but do not touch manually maintained files with prefixes `000` and `999`
                \array_map(
                    '\unlink',
                    \preg_grep('/\/(000|999)[^\/]*\.sql$/u', \glob(Config::$ddl_dir.'/*.sql'), \PREG_GREP_INVERT)
                );
                #Get tables in order
                foreach (Manage::showOrderedTables($_ENV['DATABASE_NAME']) as $order => $table) {
                    #Get DDL statement
                    $create = Manage::showCreateTable($table['schema'], $table['table'], if_not_exist: true, add_use: true);
                    if ($create === null) {
                        throw new \UnexpectedValueException('Failed to get CREATE statement for table `'.$table['table'].'`;');
                    }
                    #Get DDL statement
                    if (\preg_match('/^(cron|maintainer)__/ui', $table['table']) !== 1) {
                        \file_put_contents(Config::$ddl_dir.'/'.mb_str_pad((string)($order + 1), 3, '0', \STR_PAD_LEFT, 'UTF-8').'-'.$table['table'].'.sql', mb_trim($create, null, 'UTF-8'));
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
     * Create optimization scripts to run during database backup
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:db:optimize', description: 'Create optimization scripts to run during database backup')]
    public function optimize(OutputInterface $output): int
    {
        $output->writeln(Errors::logfmt('Generating optimization scripts...'));
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                $analyzer = new Analyzer();
                $settings = new Settings();
                #Ensure we have all tables, even though we end up doing this twice
                $analyzer->updateTables($_ENV['DATABASE_NAME']);
                #Ensure settings are set to what we want
                $settings->setTableFineTune($_ENV['DATABASE_NAME'], [], 'analyze_histogram', true)
                    ->setTableFineTune($_ENV['DATABASE_NAME'], [], 'analyze_histogram_auto', true)
                    ->setThresholdFragmentation($_ENV['DATABASE_NAME'], [], 5.0)
                    ->setRun($_ENV['DATABASE_NAME'], [], 'check', true)
                    ->setRun($_ENV['DATABASE_NAME'], [], 'fulltext_rebuild', true)
                    ->setGlobalFineTune('prefer_compressed', true)
                    ->setGlobalFineTune('prefer_extended', true)
                    ->setGlobalFineTune('compress_auto_run', true)
                    ->setGlobalFineTune('use_flush', true);
                $analyzer->writeCommandsToFiles(Config::$work_dir.'/data/backups/optimization', $_ENV['DATABASE_NAME'], [], true);
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
