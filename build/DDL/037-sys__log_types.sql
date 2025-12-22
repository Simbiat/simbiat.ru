USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `sys__log_types` (
  `type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the type',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Name of the type',
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC COMMENT='Definitions of types of logs' `PAGE_COMPRESSED`='ON';