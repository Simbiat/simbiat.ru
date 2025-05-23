USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__users` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL COMMENT 'User''s username/login',
  `system` tinyint(1) unsigned DEFAULT 0 COMMENT 'Flag indicating that user is a system one (required for normal functionality)',
  `phone` bigint(15) unsigned DEFAULT NULL COMMENT 'User''s phone number in international format',
  `password` text NOT NULL COMMENT 'Hashed password',
  `strikes` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT 'Number of unsuccessful logins',
  `pw_reset` text DEFAULT NULL COMMENT 'Password reset code',
  `api_key` text DEFAULT NULL COMMENT 'API key',
  `ff_token` varchar(64) NOT NULL COMMENT 'Token for linking FFXIV characters',
  `registered` datetime(6) NOT NULL DEFAULT current_timestamp(6) COMMENT 'When user was registered',
  `updated` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'When user was updated',
  `parentid` int(10) unsigned DEFAULT NULL COMMENT 'User ID, that added this one (if added manually)',
  `birthday` date DEFAULT NULL COMMENT 'User''s date of birth',
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s first name',
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s last/family name (also known as surname)',
  `middlename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s middle name(s)',
  `fathername` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s patronymic or matronymic name (also known as father''s name or mother''s name)',
  `prefix` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'The prefix or title, such as "Mrs.", "Mr.", "Miss", "Ms.", "Dr.", or "Mlle."',
  `suffix` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'The suffix, such as "Jr.", "B.Sc.", "PhD.", "MBASW", or "IV"',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT 'User''s sex',
  `about` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Introductory words from the user',
  `timezone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'UTC' COMMENT 'User''s timezone',
  `country` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s country',
  `city` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User''s city',
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci DEFAULT NULL COMMENT 'User''s personal website',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `ff_token` (`ff_token`),
  UNIQUE KEY `username_unique` (`username`),
  UNIQUE KEY `api_key` (`api_key`) USING HASH,
  KEY `parentid` (`parentid`),
  KEY `birthday` (`birthday`),
  KEY `registered` (`registered`),
  KEY `usergender` (`sex`),
  FULLTEXT KEY `username` (`username`),
  CONSTRAINT `parent_to_user` FOREIGN KEY (`parentid`) REFERENCES `uc__users` (`userid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';