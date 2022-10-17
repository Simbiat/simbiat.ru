CREATE TABLE `ffxiv__grandcompany_rank`
(
    `gcrankid` TINYINT(2) UNSIGNED AUTO_INCREMENT COMMENT 'ID of character''s Grand Company''s affiliation and current rank there as registered by tracker' PRIMARY KEY,
    `gcId`     TINYINT(1) UNSIGNED NOT NULL COMMENT 'Grand Company ID based on filters from Lodestone',
    `gc_rank`  VARCHAR(50)         NOT NULL COMMENT 'Rank name',
    CONSTRAINT `gc_rank` UNIQUE (`gc_rank`),
    CONSTRAINT `gcRank_to_gc` FOREIGN KEY (`gcId`) REFERENCES `ffxiv__grandcompany` (`gcId`) ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT 'Grand Companies'' ranks';
