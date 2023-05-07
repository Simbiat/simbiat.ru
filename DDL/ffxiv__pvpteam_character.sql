CREATE TABLE `ffxiv__pvpteam_character`
(
    `characterid` INT UNSIGNED                  NOT NULL COMMENT 'Character ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/character/characterid/)',
    `pvpteamid`   VARCHAR(40)                   NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)',
    `rankid`      TINYINT(1) UNSIGNED DEFAULT 3 NOT NULL COMMENT 'PvP team rank ID as registered by tracker',
    `current`     TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Whether character is currently in the group',
    PRIMARY KEY (`characterid`, `pvpteamid`),
    CONSTRAINT `pvp_char_rank` FOREIGN KEY (`rankid`) REFERENCES `ffxiv__pvpteam_rank` (`pvprankid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `pvp_xchar_id` FOREIGN KEY (`characterid`) REFERENCES `ffxiv__character` (`characterid`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `pvp_xchar_pvp` FOREIGN KEY (`pvpteamid`) REFERENCES `ffxiv__pvpteam` (`pvpteamid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Characters linked to PvP teams, past and present' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
