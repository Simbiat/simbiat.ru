<?php
declare(strict_types = 1);

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';
#Enable implicit flush for CLI mode
ini_set('implicit_flush', 1);

#Below is meant to be run only once, as part of some upgrade to the system.
#It is encouraged to code stuff in a way to avoid potential impact in case of running it twice.
#It is also encouraged to use `echo` or similar function(s) to output stuff to the command line for the sake of monitoring.
#To run in use `/usr/local/bin/php -f /app/bin/OneTime.php` on the respective container

use Simbiat\Database\Query;
use Simbiat\FFXIV\Lodestone;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;

Config::dbConnect();
try {
    echo '['.date('c').'] Getting companies...'.PHP_EOL;
    $fc_ids = Query::query('SELECT DISTINCT(`fc_id`) as `fc_id` FROM `ffxiv__freecompany_character` WHERE `rank_id` IS NULL;', return: 'column');
    $count = count($fc_ids);
    foreach ($fc_ids as $key => $fc_id) {
        echo '['.date('c').'] Processing '.$fc_id.' ('.($key + 1).'/'.$count.')'.PHP_EOL;
        try {
            $fc_data = new Lodestone()->getFreeCompanyMembers($fc_id, 0)->getResult();
        } catch (Throwable $exception) {
            if (preg_match('/Lodestone has throttled the request/ui', $exception->getMessage()) === 1 || preg_match('/Lodestone not available/ui', $exception->getMessage()) === 1) {
                sleep(60);
                continue;
            }
            throw $exception;
        }
        if (is_array($fc_data['freecompanies']) && is_array($fc_data['freecompanies'][$fc_id]) && array_key_exists('members', $fc_data['freecompanies'][$fc_id]) && is_array($fc_data['freecompanies'][$fc_id]['members'])) {
            $member = array_last($fc_data['freecompanies'][$fc_id]['members']);
            if ($member['rank_id'] > 0) {
                Query::query('UPDATE `ffxiv__freecompany_character` SET `rank_id`='.$member['rank_id'].' WHERE `fc_id`=\''.$fc_id.'\' AND `rank_id` IS NULL;');
            } else {
                Query::query('UPDATE `ffxiv__freecompany_character` SET `rank_id`=(SELECT MAX(`rank_id`) AS `rank_id` FROM `ffxiv__freecompany_rank` WHERE `fc_id`=`ffxiv__freecompany_character`.`fc_id`) WHERE `fc_id`=\''.$fc_id.'\' AND `rank_id` IS NULL;');
            }
        }
    }
    echo '['.date('c').'] Completed'.PHP_EOL;
} catch (Throwable $exception) {
    #2002 error code means server is not listening on port
    #2006 error code means server has gone away
    #This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
    if (preg_match('/HY000.*\[(2002|2006)]/u', $exception->getMessage()) !== 1) {
        Errors::error_log($exception);
    }
    return false;
}
exit(0);