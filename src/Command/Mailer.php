<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\NotificationType;
use App\Notification\Notification;
use App\Notification\Test;
use App\Service\Config;
use App\Service\Errors;
use Simbiat\Database\Query;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Send queued notification emails
 */
final class Mailer
{
    /**
     * Send queued notification emails
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    #[AsCommand(name: 'app:mailer', description: 'Send queued notification emails')]
    public function __invoke(OutputInterface $output): int
    {
        try {
            // Connect to DB
            Config::dbConnect();
            if (Config::$dbup) {
                // Send messages
                $output->writeln(Errors::logfmt('Sending emails...'));
                // According to Proton support:
                // Here are approximate limits for your reference:
                // 400 emails per hour
                // 9,600 emails per day
                // Thus limiting to 5 emails per minute
                /** @var array<string, int> $notifications */
                $notifications = Query::query(
                    'SELECT `uuid`, `type` FROM `sys__notifications` WHERE `email` IS NOT NULL AND `sent` IS NULL AND `attempts` < :max_attempts AND (`last_attempt` IS NULL OR `last_attempt`<=DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 5 MINUTE)) ORDER BY `last_attempt` LIMIT 5;',
                    [':max_attempts' => Notification::MAX_ATTEMPTS],
                    return: 'pair',
                );
                if (\count($notifications) > 0) {
                    Query::query(
                        'UPDATE `sys__notifications` SET `last_attempt`=CURRENT_TIMESTAMP(6) WHERE `uuid` IN (:uuid)',
                        [':uuid' => [\array_keys($notifications), 'in', 'string']],
                    );
                }
                foreach ($notifications as $uuid => $type) {
                    $class_name = NotificationType::tryFrom($type);
                    if (null === $class_name) {
                        // Bad type, remove the notification
                        new Test($uuid)->delete();
                    } else {
                        $to_call = "\App\Notification\\".$class_name->name;
                        try {
                            /** @psalm-suppress MixedMethodCall */
                            new $to_call($uuid)->get()->send();
                        } catch (\Throwable $throwable) {
                            Errors::error_log($throwable);
                        }
                    }
                }
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
