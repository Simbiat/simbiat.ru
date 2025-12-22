USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__posts_history` (
  `post_id` int(10) unsigned NOT NULL COMMENT 'Post ID',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'Time of the change',
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'ID of the user, who edited text',
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Text of the post',
  KEY `history_to_post` (`post_id`),
  KEY `time` (`time` DESC) USING BTREE,
  KEY `history_to_user` (`user_id`),
  CONSTRAINT `history_to_post` FOREIGN KEY (`post_id`) REFERENCES `talks__posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `history_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Posts'' history' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;