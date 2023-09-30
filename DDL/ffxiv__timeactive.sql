CREATE TABLE `ffxiv__timeactive`
(
    `activeid` TINYINT(1) UNSIGNED                            NOT NULL COMMENT 'Active time ID based on filters from Lodestone' PRIMARY KEY,
    `active`   VARCHAR(8) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Active time as shown on Lodestone',
    CONSTRAINT `active` UNIQUE (`active`)
) COMMENT 'IDs to identify when a free company is active' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
