USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `sys__languages` (
  `tag` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Language tag as per RFC 5646',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Human-readable name',
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of language tags' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;