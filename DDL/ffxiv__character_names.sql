CREATE TABLE `ffxiv__character_names`
(
    `characterid` INT UNSIGNED NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `name`        VARCHAR(50)  NOT NULL COMMENT 'Character''s previous name',
    PRIMARY KEY (`characterid`, `name`),
    CONSTRAINT `char_names_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Past names used by characters' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
