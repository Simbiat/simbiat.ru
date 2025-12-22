USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `talks__contact_form` (
  `thread_id` int(10) unsigned NOT NULL COMMENT 'Thread ID',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Email address',
  `access_token` uuid NOT NULL COMMENT 'Token providing anonymous access to the thread',
  PRIMARY KEY (`thread_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs COMMENT='Table with details for tickets created by contact form' `PAGE_COMPRESSED`='ON' ROW_FORMAT=Dynamic;