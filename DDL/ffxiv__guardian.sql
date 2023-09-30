CREATE TABLE `ffxiv__guardian`
(
    `guardianid` TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'Guardian ID as registered by the tracker' PRIMARY KEY,
    `guardian`   VARCHAR(25) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Guardian name',
    CONSTRAINT `guardian` UNIQUE (`guardian`)
) COMMENT 'Guardians as per lore' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
