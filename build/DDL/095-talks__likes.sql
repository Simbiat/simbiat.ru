USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__likes` (
  `post_id` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `like_value` tinyint(1) NOT NULL DEFAULT 1 COMMENT '"Value" of the like. Negative values are counted as a "dislike", and positive values as a "like"',
  PRIMARY KEY (`post_id`,`user_id`),
  KEY `likes_to_user` (`user_id`),
  CONSTRAINT `likes_to_post` FOREIGN KEY (`post_id`) REFERENCES `talks__posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `likes_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Table for (dis)likes of posts' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;