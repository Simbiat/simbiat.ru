USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `subs__threads` (
  `thread_id` int(10) unsigned NOT NULL COMMENT 'Thread ID',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User ID',
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='List of subscriptions to forum threads' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;