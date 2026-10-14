USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__old_music` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'File ID',
  `post_id` int(10) unsigned NOT NULL COMMENT 'ID of the post to which the file is attached',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Name of the file to be shown to humans',
  `mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'MIME Type',
  `size` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'Size of the file in bytes',
  `hash` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'File hash. Also used as file name on file system.',
  `added` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When file was added',
  `downloads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of downloads',
  UNIQUE KEY `fileid` (`file_id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `mime` (`mime`),
  KEY `file_to_post` (`post_id`),
  CONSTRAINT `file_to_post` FOREIGN KEY (`post_id`) REFERENCES `talks__posts` (`post_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of file attachments' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;