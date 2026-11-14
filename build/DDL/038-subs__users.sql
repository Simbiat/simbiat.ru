USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `subs__users` (
  `author` int(10) unsigned NOT NULL COMMENT 'ID of the thread/post author',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  `threads_only` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Whether subscribed only to new threads or all posts',
  PRIMARY KEY (`author`,`user_id`),
  KEY `threads_only` (`threads_only`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of subscriptions to users' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;