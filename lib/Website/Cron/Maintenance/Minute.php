<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron\Maintenance;

use Simbiat\Database\Query;
use Simbiat\Website\Enums\LogTypes;
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
        #TODO Actually implement the logic
        return true;
    }
}
