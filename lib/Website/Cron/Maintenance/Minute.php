<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron\Maintenance;

use Simbiat\Cron\Agent;
use Simbiat\Cron\EventTypes;
use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Notification;
use Simbiat\Website\Entities\Notifications\Test;
use Simbiat\Website\Enums\LogTypes;
use Simbiat\Website\Enums\NotificationTypes;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;
use Simbiat\Website\Session;

/**
 * Various maintenance tasks menat to be run every minute
 */
class Minute
{
    /**
     * Clean session data
     * @return bool
     */
    public function sessionClean(): bool
    {
        try {
            return (bool)new Session()->gc();
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return false;
        }
    }
    
    /**
     * Clean cookies data
     * @return bool
     */
    public function cookiesClean(): bool
    {
        #Get existing cookies that need to be cleaned
        try {
            $items = Query::query(
                'SELECT `cookie_id`, `user_id`, `ip`, `user_agent`, `time` FROM `uc__cookies` WHERE `user_id` IN (SELECT `user_id` FROM `uc__users` WHERE `system`=1) OR `time`<=DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 MONTH);', return: 'all'
            );
        } catch (\Throwable) {
            $items = [];
        }
        foreach ($items as $item) {
            #Try to delete cookie
            $affected = Query::query('DELETE FROM `uc__cookies`WHERE `cookie_id`=:id',
                [
                    ':id' => $item['cookie_id'],
                ], return: 'affected'
            );
            #If it was deleted - log it
            if ($affected > 0) {
                Security::log(LogTypes::Logout->value, 'Logged out due to cookie timeout', $item, $item['user_id']);
            }
        }
        return true;
    }
    
    /**
     * Try to send unsent email notifications
     * @return bool
     */
    public function sendNotifications(): bool
    {
        #According to Proton support:
        #Here are approximate limits for your reference:
        #400 emails per hour
        #9,600 emails per day
        #Thus limiting to 10 emails per minute, which is still over their limit, but it's not soon that I will reach it
        $notifications = Query::query('SELECT `uuid`, `type` FROM `sys__notifications` WHERE `email` IS NOT NULL AND `sent` IS NULL AND `attempts` < :max_attempts AND (`last_attempt` IS NULL OR `last_attempt`<=DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 5 MINUTE)) ORDER BY `last_attempt` LIMIT 10;', [':max_attempts' => Notification::MAX_ATTEMPTS], return: 'pair');
        if (\count($notifications) > 0) {
            Query::query('UPDATE `sys__notifications` SET `last_attempt`=CURRENT_TIMESTAMP(6) WHERE `uuid` IN (:uuid)', [':uuid' => [\array_keys($notifications), 'in', 'string']]);
        }
        foreach ($notifications as $uuid => $type) {
            $class_name = NotificationTypes::tryFrom($type);
            if ($class_name === null) {
                #Bad type, remove the notification
                new Test($uuid)->delete();
            } else {
                $class_name = "\Simbiat\Website\Entities\Notifications\\$class_name->name";
                try {
                    new $class_name($uuid)->get()->send();
                } catch (\Throwable $throwable) {
                    Errors::error_log($throwable);
                }
                echo 'here';
            }
        }
        return true;
    }
    
    /**
     * Output for
     *
     * @param string                   $message     Message to output
     * @param bool                     $log_in_cron Whether to log to Cron log
     * @param \Simbiat\Cron\EventTypes $event       Cron event type
     *
     * @return void
     */
    public static function cliOutput(string $message, bool $log_in_cron = false, EventTypes $event = EventTypes::CustomInformation): void
    {
        echo '['.\date('c').'] '.$message.\PHP_EOL;
        if ($log_in_cron) {
            try {
                new Agent()->log($message, $event);
            } catch (\Throwable $throwable) {
                Errors::error_log($throwable);
            }
        }
    }
}
