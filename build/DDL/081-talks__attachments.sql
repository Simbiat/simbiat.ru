USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__attachments` (
  `postid` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `fileid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
  `inline` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating inline file (mainly images inserted through editor)',
  PRIMARY KEY (`postid`,`fileid`),
  KEY `attachment_to_file` (`fileid`),
  CONSTRAINT `attachment_to_file` FOREIGN KEY (`fileid`) REFERENCES `sys__files` (`fileid`),
  CONSTRAINT `attachment_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;