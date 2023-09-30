CREATE TABLE `ffxiv__linkshell_rank`
(
    `lsrankid` TINYINT(1) UNSIGNED AUTO_INCREMENT COMMENT 'Rank ID as registered by tracker' PRIMARY KEY,
    `rank`     VARCHAR(6) COLLATE utf8mb4_uca1400_nopad_ai_ci  NOT NULL COMMENT 'Rank name',
    `icon`     VARCHAR(20) COLLATE utf8mb4_uca1400_nopad_as_ci NULL COMMENT 'Name of the rank icon file'
) COMMENT 'Rank names used by linkshells' `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `lsrank` ON `ffxiv__linkshell_rank` (`rank`);
