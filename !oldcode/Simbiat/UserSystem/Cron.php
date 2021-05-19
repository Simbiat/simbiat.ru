<?php
namespace Simbiat\UserSystem;

class Cron
{
    public $timeperiods = array(
        'onetime'=>0,
        'hourly'=>3600,
        'daily'=>86400,
        'weekly'=>604800,
        'monthly'=>2592000,
        'yearly'=>31536000,
    );
    
    private function auditlogpurge()
    {
        $time = time()-$this->timeperiods['yearly'];
        return (new \Simbiat\Database\Controller)->query('DELETE FROM `usersys__audit` WHERE `time`<= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 YEAR)', [':time'=>$time]);
    }
    
    private function sessionspurge()
    {
        $time = time()-$this->timeperiods['daily'];
        return (new \Simbiat\Database\Controller)->query('DELETE FROM `usersys__sessions` WHERE `time`<=FROM_UNIXTIME(:time)', [':time'=>$time]);
    }
    
    private function cookiespurge()
    {
        $time = time()-$this->timeperiods['weekly'];
        $items = (new \Simbiat\Database\Controller)->selectAll('SELECT * FROM `usersys__cookies` WHERE `time`<=FROM_UNIXTIME(:time)', [':time'=>$time]);
        if (!empty($items) && is_array($items)) {
            $queries = array();
            foreach ($items as $item) {
                $queries[] = array(
                    'INSERT INTO `usersys__audit`(`userid`, `ip`, `useragent`, `action`) VALUES (:userid,:ip,:useragent,"Logout")',
                    array(
                        ':userid'=>$item['userid'],
                        ':ip'=>$item['ip'],
                        ':useragent'=>$item['useragent'],
                    )
                );
                $queries[] = array(
                    'DELETE FROM `usersys__cookies`WHERE `cookieid`=:id',
                    array(
                        ':id'=>$item['cookieid'],
                    )
                );
            }
        }
        if (!empty($queries)) {
            $result = (new \Simbiat\Database\Controller)->query($queries);
        } else {
            $result = true;
        }
        return $result;
    }
}
?>