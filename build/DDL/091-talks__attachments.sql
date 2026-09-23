USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__attachments` (
  `post_id` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `file_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
  `inline` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating inline file (mainly images inserted through editor)',
  PRIMARY KEY (`post_id`,`file_id`),
  KEY `attachment_to_file` (`file_id`),
  CONSTRAINT `attachment_to_file` FOREIGN KEY (`file_id`) REFERENCES `sys__files` (`file_id`),
  CONSTRAINT `attachment_to_post` FOREIGN KEY (`post_id`) REFERENCES `talks__posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;