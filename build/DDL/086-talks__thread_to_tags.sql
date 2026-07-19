USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__thread_to_tags` (
  `thread_id` int(10) unsigned NOT NULL COMMENT 'Thread ID',
  `tag_id` int(10) unsigned NOT NULL COMMENT 'Tag ID',
  PRIMARY KEY (`thread_id`,`tag_id`) USING BTREE,
  KEY `thr_tag_tag` (`tag_id`),
  CONSTRAINT `thr_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `talks__tags` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `thr_tag_thread` FOREIGN KEY (`thread_id`) REFERENCES `talks__threads` (`thread_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Threads to tags junction table' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;