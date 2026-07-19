USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the group',
  `group_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Human-ready group name',
  `system` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating that group is a system one (required for normal functionality)',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Description of the group',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `groupname` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';