CREATE TABLE `ffxiv__grandcompany`
(
    `gcId`   TINYINT(1) UNSIGNED                             NOT NULL COMMENT 'ID based on filters from Lodestone' PRIMARY KEY,
    `gcName` VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the company',
    CONSTRAINT `gcName` UNIQUE (`gcName`)
) COMMENT 'Grand Companies as per lore' `PAGE_COMPRESSED` = 'ON';
