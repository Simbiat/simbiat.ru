USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__cookies` (
  `cookie_id` varchar(256) NOT NULL COMMENT 'Cookie ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'ID of the user to which this cookie belongs',
  `validator` text NOT NULL COMMENT 'Encrypted validator string, to compare against cookie',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Time of last update/use of the cookie',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'Last IP, that used the cookie',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Last UserAgent of the client, from which the cookie was used',
  PRIMARY KEY (`cookie_id`) USING BTREE,
  KEY `cookie_to_user` (`user_id`),
  KEY `time` (`time` DESC),
  CONSTRAINT `cookie_to_user` FOREIGN KEY (`user_id`) REFERENCES `uc__users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';