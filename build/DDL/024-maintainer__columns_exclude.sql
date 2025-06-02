USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `maintainer__columns_exclude` (
  `schema` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Schema name',
  `table` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Table name',
  `column` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Column name',
  UNIQUE KEY `schema` (`schema`,`table`,`column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Additional columns to exclude from histogram generation' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;