CREATE TABLE `ffxiv__character_achievement`
(
    `characterid`   INT UNSIGNED                          NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `achievementid` SMALLINT UNSIGNED                     NOT NULL COMMENT 'Achievement ID taken from Lodestone (https://eu.finalfantasyxiv.com/lodestone/character/characterid/achievement/detail/achievementid/)',
    `time`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Time when achievement was received according to Lodestone',
    PRIMARY KEY (`characterid`, `achievementid`),
    CONSTRAINT `char_ach_ach` FOREIGN KEY (`achievementid`) REFERENCES `ffxiv__achievement` (`achievementid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `char_ach_char` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Achievements linked to known characters' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `ach` ON `ffxiv__character_achievement` (`achievementid`);

CREATE INDEX `time` ON `ffxiv__character_achievement` (`time` DESC);
