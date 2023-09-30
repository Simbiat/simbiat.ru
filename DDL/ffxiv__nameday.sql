CREATE TABLE `ffxiv__nameday`
(
    `namedayid` SMALLINT(3) UNSIGNED AUTO_INCREMENT COMMENT 'Nameday ID as registered by the tracker' PRIMARY KEY,
    `nameday`   VARCHAR(32) COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Nameday',
    CONSTRAINT `nameday` UNIQUE (`nameday`)
) COMMENT 'Namedays as per lore' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;
