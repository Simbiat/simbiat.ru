USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__alt_links` (
  `thread_id` int(10) unsigned NOT NULL COMMENT 'Thread ID, to which link relates to',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Respective alternative URL',
  `type` tinyint(3) unsigned NOT NULL COMMENT 'Type of alternative source',
  `added` timestamp(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When link was added',
  `added_by` int(10) NOT NULL COMMENT 'By whom the link was added',
  `edited` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'When link was edited',
  `edited_by` int(10) NOT NULL COMMENT 'By whom the link was edited',
  `checked` timestamp(6) NULL DEFAULT NULL COMMENT 'When the link was checked last time',
  PRIMARY KEY (`thread_id`,`type`),
  KEY `alt_link_to_type` (`type`),
  KEY `checked` (`checked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Alternative representation of the threads on other websites' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;