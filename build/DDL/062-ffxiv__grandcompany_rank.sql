USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `ffxiv__grandcompany_rank` (
  `gc_rank_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of character''s Grand Company''s affiliation and current rank there as registered by tracker',
  `gc_id` tinyint(1) unsigned NOT NULL COMMENT 'Grand Company ID based on filters from Lodestone',
  `gc_rank` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Rank name',
  PRIMARY KEY (`gc_rank_id`),
  UNIQUE KEY `gc_rank` (`gc_rank`) USING BTREE,
  KEY `gcRank_to_gc` (`gc_id`),
  CONSTRAINT `gcRank_to_gc` FOREIGN KEY (`gc_id`) REFERENCES `ffxiv__grandcompany` (`gc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Grand Companies'' ranks' `PAGE_COMPRESSED`='ON';