CREATE TABLE IF NOT EXISTS `sys__files` (
  `fileid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'File hash, which is also its ID. Also used as file name on file system.',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'ID of the uploader',
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Name of the file to be shown to humans',
  `extension` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'File extension',
  `mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'MIME Type',
  `size` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'Size of the file in bytes',
  `added` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When file was added',
  PRIMARY KEY (`fileid`),
  KEY `mime` (`mime`),
  KEY `userid` (`userid`),
  KEY `added` (`added`) USING BTREE,
  KEY `size` (`size` DESC) USING BTREE,
  CONSTRAINT `file_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='List of file attachments' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;