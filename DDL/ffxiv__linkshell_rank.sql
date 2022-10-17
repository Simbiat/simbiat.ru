CREATE TABLE `ffxiv__linkshell_rank`
(
    `lsrankid` TINYINT(1) UNSIGNED AUTO_INCREMENT COMMENT 'Rank ID as registered by tracker' PRIMARY KEY,
    `rank`     VARCHAR(6)  NOT NULL COMMENT 'Rank name',
    `icon`     VARCHAR(20) NULL COMMENT 'Name of the rank icon file'
) COMMENT 'Rank names used by linkshells';

CREATE INDEX `lsrank` ON `ffxiv__linkshell_rank` (`rank`);
