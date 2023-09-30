CREATE TABLE `ffxiv__clan`
(
    `clanid` TINYINT(2) UNSIGNED                             NOT NULL COMMENT 'Clan ID based on filters taken from Lodestone' PRIMARY KEY,
    `clan`   VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Clan name',
    `race`   VARCHAR(15) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Race name',
    `raceid` TINYINT(2) UNSIGNED                             NOT NULL COMMENT 'Race ID based on filters taken from Lodestone',
    CONSTRAINT `clan` UNIQUE (`clan`, `race`)
) COMMENT 'Clans/races as per lore' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
