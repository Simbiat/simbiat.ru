<?php
declare(strict_types = 1);

namespace Simbiat\Website\Cron\Maintenance;

use Simbiat\Arrays\Converters;
use Simbiat\Database\Query;
use Simbiat\Website\Enums\LogTypes;
use Simbiat\Website\Enums\NotificationTypes;
use Simbiat\Website\Enums\SystemUsers;
use Simbiat\Website\Enums\TalkTypes;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;

/**
 * Various maintenance tasks menat to be run every month
 */
class Month
{
    /**
     * Force regeneration of Argon settings
     * @return bool
     */
    public function argon(): bool
    {
        return \count(Security::argonCalc(true)) !== 0;
    }
    
    /**
     * Clean logs
     * @return bool
     */
    public function logsClean(): bool
    {
        $queries = [];
        #Clean audit logs
        $queries[] = 'DELETE FROM `sys__logs` WHERE `time`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 1 YEAR)';
        try {
            $result = Query::query($queries);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $result = false;
        }
        return $result;
    }
    
    /**
     * Clean statistical data
     * @return void
     */
    public function statisticsClean(): void
    {
        $queries = [];
        #Remove pages that have not been viewed in 2 years
        $queries[] = 'DELETE FROM `seo__pageviews` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR)';
        #Remove visitors who have not come in 2 years
        $queries[] = 'DELETE FROM `seo__visitors` WHERE `last`<= DATE_SUB(CURRENT_TIMESTAMP(6), INTERVAL 2 YEAR)';
        try {
            Query::query($queries);
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
        }
    }
    
    /**
     * Remove entries that would violate foreign key restrictions, if they were used.
     * Service does not use them normally due to performance hit, and to have potentially more flexibility in business logic.
     * While logic should be written in a way to prevent such "violations", having a job to forcefully remove them is useful.
     * Nullable values will be set to NULL.
     * @return bool
     */
    public function cleanForeignKeys(): bool
    {
        #TODO Actually write queries for this
        #Logs
        Query::query('DELETE FROM `sys__logs` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(LogTypes::class), 'in', 'int']]);
        Query::query('UPDATE `sys__logs` SET `user_id`=:user_id WHERE `user_id` NOT IN (SELECT `user_id` FROM `uc__users`);', [':user_id' => SystemUsers::Unknown->value]);
        #Notification types
        Query::query('DELETE FROM `sys__notifications` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(NotificationTypes::class), 'in', 'int']]);
        #Unsupported section types
        Query::query('DELETE FROM `talks__sections` WHERE `type` NOT IN (:types);', [':types' => [Converters::enumValues(TalkTypes::class), 'in', 'int']]);
        return true;
    }
}
