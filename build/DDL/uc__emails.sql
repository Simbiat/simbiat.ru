CREATE TABLE `uc__emails` (
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci NOT NULL COMMENT 'Email address',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `subscribed` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Flag indicating, that this mail should receive notifications',
  `activation` text DEFAULT NULL COMMENT 'Encrypted activation code',
  PRIMARY KEY (`email`),
  KEY `subscribed` (`subscribed` DESC),
  KEY `userid` (`userid`),
  CONSTRAINT `email_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';