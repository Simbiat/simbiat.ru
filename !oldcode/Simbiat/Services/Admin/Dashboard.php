<?php
namespace Simbiat\Services\Admin;

trait Dashboard
{    
    private function dashboard(): array
    {
        $result = array();
        $curtime = time() - 3600;
        $result['cron'] = (new \Simbiat\Database\Controller)->selectAll('SELECT 
            COUNT(*) as `count`, \'total\' as `type` FROM `sys__cron`
            UNION ALL
            SELECT COUNT(*) as `count`, \'running\' as `type` FROM `sys__cron` WHERE `status` = 1 AND '.$curtime.'<`lastrun`
            UNION ALL
            SELECT COUNT(*) as `count`, \'hanging\' as `type` FROM `sys__cron` WHERE `status` = 1 AND '.$curtime.'>`lastrun`
            UNION ALL
            SELECT COUNT(*) as `count`, \'error\' as `type` FROM `sys__cron` WHERE `lasterror` IS NOT NULL AND `lasterror` != \'Pending jobs found, rescheduled\'
        ');
        $result['cron'] = (new \Simbiat\ArrayHelpers)->MultiToSingle((new \Simbiat\ArrayHelpers)->DigitToKey($result['cron'], "type"), "count");
        $result['achievements'] = (new \Simbiat\Database\Controller)->selectAll('SELECT `ff__achievement`.`achievementid`, `name`, `icon`, `ff__character_achievement`.`characterid` FROM `ff__achievement` LEFT JOIN `ff__character_achievement` ON `ff__character_achievement`.`achievementid` = `ff__achievement`.`achievementid` WHERE `category` IS NULL OR `howto` IS NULL GROUP BY `ff__achievement`.`achievementid`');
        $result['bots'] = (new \Simbiat\Database\Controller)->selectAll('SELECT `username`, COUNT(`username`) AS `sessions` FROM `usersys__sessions` WHERE `bot` = 1 AND `username` is not null GROUP BY `username`');
        return $result;
    }
}
?>