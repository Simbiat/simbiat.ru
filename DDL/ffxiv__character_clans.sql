CREATE TABLE `ffxiv__character_clans`
(
    `characterid` INT UNSIGNED        NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `genderid`    TINYINT(1) UNSIGNED NOT NULL COMMENT '0 for female and 1 for male',
    `clanid`      TINYINT(2) UNSIGNED NOT NULL COMMENT 'Clan ID identifying both clan and race of the character',
    PRIMARY KEY (`characterid`, `genderid`, `clanid`),
    CONSTRAINT `char_clan_clan` FOREIGN KEY (`clanid`) REFERENCES `ffxiv__clan` (`clanid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `char_clan_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past clans used by characters' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
