CREATE TABLE `ffxiv__grandcompany`
(
    `gcId`   TINYINT(1) UNSIGNED NOT NULL COMMENT 'ID based on filters from Lodestone' PRIMARY KEY,
    `gcName` VARCHAR(25)         NOT NULL COMMENT 'Name of the company',
    CONSTRAINT `gcName` UNIQUE (`gcName`)
) COMMENT 'Grand Companies as per lore' `PAGE_COMPRESSED` = 'ON';
