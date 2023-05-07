CREATE TABLE `ffxiv__freecompany_character`
(
    `characterid`   INT UNSIGNED                  NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `freecompanyid` VARCHAR(20)                   NOT NULL COMMENT 'Free Company ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/freecompany/freecompanyid/)',
    `rankid`        TINYINT(2) UNSIGNED           NULL COMMENT 'ID calculated based on rank icon on Lodestone',
    `current`       TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Whether character is currently in the group',
    PRIMARY KEY (`characterid`, `freecompanyid`),
    CONSTRAINT `fc_char_rank` FOREIGN KEY (`rankid`) REFERENCES `ffxiv__freecompany_rank` (`rankid`) ON UPDATE SET NULL ON DELETE SET NULL,
    CONSTRAINT `fc_xchar_fc` FOREIGN KEY (`freecompanyid`) REFERENCES `ffxiv__freecompany` (`freecompanyid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fc_xchar_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Characters linked to Free Companies, past and present' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
