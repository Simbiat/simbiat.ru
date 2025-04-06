CREATE TABLE IF NOT EXISTS `talks__likes` (
  `postid` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `likevalue` tinyint(1) NOT NULL DEFAULT 1 COMMENT '"Value" of the like. Negative values are counted as a "dislike", and positive values as a "like"',
  PRIMARY KEY (`postid`,`userid`),
  KEY `likes_to_user` (`userid`),
  CONSTRAINT `likes_to_post` FOREIGN KEY (`postid`) REFERENCES `talks__posts` (`postid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs COMMENT='Table for (dis)likes of posts' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;