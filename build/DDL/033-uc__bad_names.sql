USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__bad_names` (
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Banned (prohibited) name',
  `added` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When name was banned',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Reason for the ban',
  PRIMARY KEY (`name`),
  KEY `added` (`added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Banned user names' `PAGE_COMPRESSED`='ON';