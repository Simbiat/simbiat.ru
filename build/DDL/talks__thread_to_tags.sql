CREATE TABLE `talks__thread_to_tags` (
  `threadid` int(10) unsigned NOT NULL COMMENT 'Thread ID',
  `tagid` int(10) unsigned NOT NULL COMMENT 'Tag ID',
  PRIMARY KEY (`threadid`,`tagid`) USING BTREE,
  KEY `thr_tag_tag` (`tagid`),
  CONSTRAINT `thr_tag_tag` FOREIGN KEY (`tagid`) REFERENCES `talks__tags` (`tagid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `thr_tag_thread` FOREIGN KEY (`threadid`) REFERENCES `talks__threads` (`threadid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Threads to tags junction table' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;