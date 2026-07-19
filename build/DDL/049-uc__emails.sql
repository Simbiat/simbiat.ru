USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__emails` (
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'Email address',
  `user_id` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `subscribed` text DEFAULT NULL COMMENT 'Flag indicating, that this mail should receive notifications. The text is a token, that can be used to unsubscribe.',
  `activation` text DEFAULT 'not yet activated' COMMENT 'Encrypted activation code',
  PRIMARY KEY (`email`),
  KEY `subscribed` (`subscribed`(768) DESC),
  KEY `userid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';