USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `sys__slow_log` (
  `hash` varchar(32) NOT NULL COMMENT 'Query hash used, used as unique key',
  `time` timestamp(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Last time a query with this hash was detected as slow',
  `length` time(6) NOT NULL COMMENT 'Maximum runtime of a query with this hash',
  `examined` bigint(21) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of rows examined by the longest query with this hash',
  `sent` bigint(21) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of rows sent by the longest query with this hash',
  `text` mediumtext NOT NULL COMMENT 'Full text of the longest query with this hash',
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Custom table for slow queries' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;