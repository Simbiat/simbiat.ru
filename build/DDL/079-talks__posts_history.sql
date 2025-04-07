USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__posts_history` (
  `postid` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Time of the change',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'ID of the user, who edited text',
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci NOT NULL COMMENT 'Text of the post',
  KEY `history_to_post` (`postid`),
  KEY `time` (`time` DESC) USING BTREE,
  KEY `history_to_user` (`userid`),
  CONSTRAINT `history_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `history_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Posts'' history' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;