CREATE TABLE `ffxiv__character_servers`
(
    `characterid` INT UNSIGNED        NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `serverid`    TINYINT(2) UNSIGNED NOT NULL COMMENT 'ID of the server character previously resided on',
    PRIMARY KEY (`characterid`, `serverid`),
    CONSTRAINT `char_serv_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `char_serv_serv` FOREIGN KEY (`serverid`) REFERENCES `ffxiv__server` (`serverid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past servers used by characters' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
