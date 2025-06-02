USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__alt_links` (
  `threadid` int(10) unsigned NOT NULL COMMENT 'Thread ID, to which link relates to',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Respective alternative URL',
  `type` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Type (or rather name) of alternative source',
  PRIMARY KEY (`threadid`,`type`),
  KEY `alt_link_to_type` (`type`),
  CONSTRAINT `alt_link_to_thread` FOREIGN KEY (`threadid`) REFERENCES `talks__threads` (`threadid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alt_link_to_type` FOREIGN KEY (`type`) REFERENCES `talks__alt_link_types` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Alternative representation of the threads on other websites' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;