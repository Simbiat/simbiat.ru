USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__alt_link_types` (
  `type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `type` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Type (or rather name) of alternative source',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL DEFAULT '/img/icons/Earth.svg' COMMENT 'Icon for the source',
  `regex` varchar(255) NOT NULL COMMENT 'Regex to validate the domain when adding links. Do not include ''www'' and remember to escape dots with ''\\''!',
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='"Types" of alternative links for forum threads' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;