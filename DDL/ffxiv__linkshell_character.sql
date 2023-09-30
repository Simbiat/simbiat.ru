CREATE TABLE `ffxiv__linkshell_character`
(
    `linkshellid` VARCHAR(40) COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Linkshell ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/linkshell/linkshellid/ or https://eu.finalfantasyxiv.com/lodestone/crossworld_linkshell/linkshellid/)',
    `characterid` INT UNSIGNED                                    NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `rankid`      TINYINT(1) UNSIGNED DEFAULT 3                   NOT NULL COMMENT 'Rank ID as registered by tracker',
    `current`     TINYINT(1) UNSIGNED DEFAULT 0                   NOT NULL COMMENT 'Whether character is currently in the group',
    PRIMARY KEY (`linkshellid`, `characterid`),
    CONSTRAINT `link_char_char` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `link_char_link` FOREIGN KEY (`linkshellid`) REFERENCES `ffxiv__linkshell` (`linkshellid`),
    CONSTRAINT `ls_rank2` FOREIGN KEY (`rankid`) REFERENCES `ffxiv__linkshell_rank` (`lsrankid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Characters linked to linkshells, past and present' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `character` ON `ffxiv__linkshell_character` (`characterid`);

CREATE INDEX `ls_rank` ON `ffxiv__linkshell_character` (`rankid`);
