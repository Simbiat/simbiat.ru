CREATE TABLE `ffxiv__pvpteam_rank`
(
    `pvprankid` TINYINT(1) UNSIGNED AUTO_INCREMENT COMMENT 'Rank ID as registered by tracker' PRIMARY KEY,
    `rank`      VARCHAR(9)  NOT NULL COMMENT 'Rank name',
    `icon`      VARCHAR(20) NULL COMMENT 'Name of the rank icon file'
) COMMENT 'Rank names used by PvP teams' ENGINE = `InnoDB` `PAGE_COMPRESSED` = 'ON' ROW_FORMAT = DYNAMIC;

CREATE INDEX `pvprank` ON `ffxiv__pvpteam_rank` (`rank`);
