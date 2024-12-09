<?php
declare(strict_types = 1);

#Bootstrap things
require_once dirname(__DIR__).'/bin/Bootstrap.php';

#Below is meant to be run only once, as part of some upgrade to the system.
#It is encouraged to code stuff in a way to avoid potential impact in case of running it twice.
#It is also encouraged to use `echo` or similar function(s) to output stuff to command line for the sake of monitoring.
#Tp run in use `/usr/local/bin/php -f /app/bin/OneTime.php` on respective container

use Simbiat\Website\Config;
use Simbiat\Website\Errors;

Config::dbConnect();
try {
    $characters = Config::$dbController->selectColumn('SELECT `characterid` AS `character`, COUNT(*) AS `count` FROM `ffxiv__character_achievement` GROUP BY `characterid` HAVING `count`>50 ORDER BY `count` DESC;');
    $count = count($characters);
    $current = 1;
    foreach ($characters as $character) {
        echo '['.date('c').'] Character '.$character.' ('.$current.'/'.$count.')'.PHP_EOL;
        #Get list of potential achievements to remove, which is all achievements that besides last 50 and have more than 50 other non-deleted and non-privated characters with it
        $potential = Config::$dbController->selectColumn(
            'SELECT `achievementid`
                        FROM (
                            SELECT * FROM `ffxiv__character_achievement` WHERE `characterid`=:characterid ORDER BY `time` DESC LIMIT 100000 OFFSET 50
                        ) AS `lastAchievements`
                        WHERE (
                            SELECT COUNT(*) AS `count` FROM `ffxiv__character_achievement`
                            INNER JOIN `ffxiv__character` ON `ffxiv__character_achievement`.`characterid`=`ffxiv__character`.`characterid`
                            WHERE `achievementid`=`lastAchievements`.`achievementid` AND `ffxiv__character`.`deleted` IS NULL AND `ffxiv__character`.`privated` IS NULL
                        )>50;',
            [':characterid' => $character],
        );
        #Iterrate over each achievement, and remove them if achievement current character is not one of the last 50 that has the achievement, and that there are still 50 owners of the achievement
        foreach ($potential as $achievement) {
            file_put_contents('/app/logs/achievements_deletion.sql',
                'DELETE FROM `ffxiv__character_achievement`
                            WHERE `achievementid`='.$achievement.' AND `characterid`='.$character.' AND
                            (
                                SELECT `count` FROM (
                                    SELECT COUNT(*) AS `count`, GROUP_CONCAT(`characterid`) AS `ids` FROM (
                                        SELECT `ffxiv__character_achievement`.`characterid` FROM `ffxiv__character_achievement`
                                        INNER JOIN `ffxiv__character` ON `ffxiv__character_achievement`.`characterid`=`ffxiv__character`.`characterid`
                                        WHERE `achievementid`='.$achievement.' AND `ffxiv__character`.`deleted` IS NULL AND `ffxiv__character`.`privated` IS NULL
                                        ORDER BY `time` DESC LIMIT 50
                                    ) AS `latestCharacters`
                                ) AS `validation`
                                WHERE `count`=50 AND NOT FIND_IN_SET('.$character.', `ids`)
                            )=50;',
                FILE_APPEND
            );
        }
        $current++;
    }
} catch (Throwable $exception) {
    #2002 error code means server is not listening on port
    #2006 error code means server has gone away
    #This will happen a lot, in case of database maintenance, during initial boot up or when shutting down. If they happen at this stage, though, logging is practically pointless
    if (preg_match('/HY000.*\[(2002|2006)]/u', $exception->getMessage()) !== 1) {
        Errors::error_log($exception);
    }
    return false;
}
exit;