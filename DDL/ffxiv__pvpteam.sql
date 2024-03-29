CREATE TABLE `ffxiv__pvpteam`
(
    `pvpteamid`    VARCHAR(40) COLLATE utf8mb4_uca1400_nopad_as_ci  NOT NULL COMMENT 'PvP Team ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/pvpteam/pvpteamid/)' PRIMARY KEY,
    `name`         VARCHAR(50) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'PvP Team name',
    `manual`       TINYINT(1) UNSIGNED DEFAULT 0                    NOT NULL COMMENT 'Flag indicating whether entity was added manually',
    `datacenterid` TINYINT(2) UNSIGNED                              NULL COMMENT 'ID of the server PvP Team resides on',
    `formed`       DATE                                             NULL COMMENT 'PvP Team formation day as seen on Lodestone',
    `registered`   DATE                DEFAULT CURRENT_TIMESTAMP()  NOT NULL COMMENT 'When PvP Team was initially added to tracker',
    `updated`      DATETIME(6)         DEFAULT CURRENT_TIMESTAMP(6) NOT NULL ON UPDATE CURRENT_TIMESTAMP(6) COMMENT 'When PvP Team was last updated on the tracker',
    `deleted`      DATE                                             NULL COMMENT 'Date when PvP Team was marked as deleted',
    `communityid`  VARCHAR(40) COLLATE utf8mb4_uca1400_nopad_as_ci  NULL COMMENT 'Community ID taken from Lodestone URL (https://eu.finalfantasyxiv.com/lodestone/community_finder/communityid/)',
    `crest_part_1` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Link to 1st part of the crest (background)',
    `crest_part_2` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Link to 2nd part of the crest (frame)',
    `crest_part_3` VARCHAR(100) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Link to 3rd part of the crest (emblem)',
    CONSTRAINT `pvp_dcid` FOREIGN KEY (`datacenterid`) REFERENCES `ffxiv__server` (`serverid`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'PvP Teams found on Lodestone' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `communityid` ON `ffxiv__pvpteam` (`communityid`);

CREATE INDEX `deleted` ON `ffxiv__pvpteam` (`deleted`);

CREATE FULLTEXT INDEX `name` ON `ffxiv__pvpteam` (`name`);

CREATE INDEX `name_order` ON `ffxiv__pvpteam` (`name`);

CREATE INDEX `registered` ON `ffxiv__pvpteam` (`registered`);

CREATE INDEX `updated` ON `ffxiv__pvpteam` (`updated` DESC);
