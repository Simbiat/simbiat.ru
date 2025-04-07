USE `simbiatr_simbiat`;
CREATE TABLE IF NOT EXISTS `uc__sessions` (
  `sessionid` varchar(256) NOT NULL COMMENT 'Session''s UID',
  `cookieid` varchar(256) DEFAULT NULL COMMENT 'Cookie associated with the session',
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Last time session was determined active',
  `userid` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'User ID',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_as_ci DEFAULT NULL COMMENT 'Session''s IP',
  `useragent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'UserAgent used in session',
  `page` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_nopad_ai_ci DEFAULT NULL COMMENT 'Which page is being viewed at the moment',
  `data` text DEFAULT NULL COMMENT 'Session''s data. Not meant for sensitive information.',
  PRIMARY KEY (`sessionid`),
  KEY `viewing` (`page`),
  KEY `session_to_user` (`userid`),
  KEY `session_to_cookie` (`cookieid`),
  KEY `time` (`time` DESC),
  CONSTRAINT `session_to_cookie` FOREIGN KEY (`cookieid`) REFERENCES `uc__cookies` (`cookieid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `session_to_user` FOREIGN KEY (`userid`) REFERENCES `uc__users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_nopad_as_cs ROW_FORMAT=DYNAMIC `PAGE_COMPRESSED`='ON';