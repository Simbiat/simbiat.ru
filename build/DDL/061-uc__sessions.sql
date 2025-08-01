USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__sessions` (
  `session_id` varchar(256) NOT NULL COMMENT 'Session''s UID',
  `cookie_id` varchar(256) DEFAULT NULL COMMENT 'Cookie associated with the session',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Last time session was determined active',
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Session''s IP',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'UserAgent used in session',
  `page` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Which page is being viewed at the moment',
  `data` text DEFAULT NULL COMMENT 'Session''s data. Not meant for sensitive information.',
  PRIMARY KEY (`session_id`),
  KEY `viewing` (`page`),
  KEY `session_to_user` (`user_id`),
  KEY `session_to_cookie` (`cookie_id`),
  KEY `time` (`time` DESC),
  CONSTRAINT `session_to_cookie` FOREIGN KEY (`cookie_id`) REFERENCES `uc__cookies` (`cookie_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `session_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';