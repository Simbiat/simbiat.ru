CREATE TABLE `sys__log_types` (
  `typeid` tinyint(2) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Type ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Name of the type',
  PRIMARY KEY (`typeid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC COMMENT='Definitions of types of logs' `PAGE_COMPRESSED`='ON';