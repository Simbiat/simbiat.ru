USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__types` (
  `typeid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `type` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Type name',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Description of the type',
  `icon` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT NULL COMMENT 'Name of the default icon file',
  PRIMARY KEY (`typeid`) USING BTREE,
  KEY `section_type_to_file` (`icon`),
  CONSTRAINT `section_type_to_file` FOREIGN KEY (`icon`) REFERENCES `sys__files` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Types of forums' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;